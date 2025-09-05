<?php
require_once 'config.php';
require_once 'includes/db.php';

$response = ['success' => false, 'message' => 'E-mail ou senha inválidos.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo_usuario = $_POST['tipo_usuario'];

    try {
        $pdo = conectar();

        if ($tipo_usuario === 'paciente') {
            $stmt = $pdo->prepare("SELECT id, nome, senha, whereby_room_url FROM pacientes WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                // Regenera o ID da sessão para segurança
                session_regenerate_id(true);
                
                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'paciente';
                $_SESSION['paciente_id'] = $user['id'];
                $_SESSION['paciente_nome'] = $user['nome'];
                $_SESSION['paciente_whereby_url'] = $user['whereby_room_url']; // Linha atualizada
                
                header("Location: area_logada/paciente/painel.php");
                exit();
            }
        } elseif ($tipo_usuario === 'psicologa') {
            $stmt = $pdo->prepare("SELECT id, nome, senha FROM psicologa WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                // Regenera o ID da sessão para segurança
                session_regenerate_id(true);

                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'psicologa';
                $_SESSION['psicologa_id'] = $user['id'];
                $_SESSION['psicologa_nome'] = $user['nome'];

                header("Location: area_logada/psicologa/painel.php");
                exit();
            }
        }
        
        // Se a autenticação falhar, redireciona com mensagem de erro
        $_SESSION['login_error'] = $response['message'];
        header("Location: login.php");
        exit();

    } catch (PDOException $e) {
        // Em caso de erro de base de dados, redireciona com uma mensagem genérica
        $_SESSION['login_error'] = "Ocorreu um erro no sistema. Tente novamente mais tarde.";
        error_log("Erro de login: " . $e->getMessage()); // Log do erro para a psicóloga
        header("Location: login.php");
        exit();
    }
} else {
    // Redireciona se o acesso não for via POST
    header("Location: login.php");
    exit();
}
?>
