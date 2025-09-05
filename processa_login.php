<?php
// **CORREÇÃO: Inicia a sessão de forma segura no início absoluto do script.**
// Isto garante que a sessão está sempre disponível antes de qualquer outra operação.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
            // A query para o paciente está correta, assumindo que a coluna 'whereby_room_url' existe.
            $stmt = $pdo->prepare("SELECT id, nome, senha, whereby_room_url FROM pacientes WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                // Regenera o ID da sessão para segurança após o login bem-sucedido
                session_regenerate_id(true);
                
                $_SESSION['logged_in'] = true;
                $_SESSION['user_type'] = 'paciente';
                $_SESSION['paciente_id'] = $user['id'];
                $_SESSION['paciente_nome'] = $user['nome'];
                $_SESSION['paciente_whereby_url'] = $user['whereby_room_url'];
                
                header("Location: area_logada/paciente/painel.php");
                exit();
            }
        } elseif ($tipo_usuario === 'psicologa') {
            // A query para a psicóloga está correta.
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
        
        // Se a autenticação falhar, armazena a mensagem de erro na sessão e redireciona.
        $_SESSION['login_error'] = $response['message'];
        header("Location: login.php");
        exit();

    } catch (PDOException $e) {
        // Em caso de erro de base de dados, redireciona com uma mensagem genérica.
        $_SESSION['login_error'] = "Ocorreu um erro no sistema. Tente novamente mais tarde.";
        error_log("Erro de login (PDOException): " . $e->getMessage()); // Log do erro real para depuração.
        header("Location: login.php");
        exit();
    }
} else {
    // Redireciona se o acesso não for via POST, para segurança.
    header("Location: login.php");
    exit();
}
?>
