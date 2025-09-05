<?php

// Inclui os arquivos necessários

require_once 'includes/db.php'; // Já inclui o config.php



// Verifica se o formulário foi enviado

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: login.php');

    exit;

}



// Pega os dados do formulário

$email = $_POST['email'] ?? '';

$senha = $_POST['senha'] ?? '';



// Validação básica

if (empty($email) || empty($senha)) {

    $_SESSION['login_error'] = 'Por favor, preencha todos os campos.';

    header('Location: login.php');

    exit;

}



try {

    $pdo = conectar();

    $usuario = null;

    $tipo_usuario = '';



    // 1. Tenta encontrar o usuário na tabela de psicólogos

    $stmt = $pdo->prepare("SELECT id, nome, senha FROM psicologos WHERE email = ?");

    $stmt->execute([$email]);

    $psicologa = $stmt->fetch();



    if ($psicologa && password_verify($senha, $psicologa['senha'])) {

        $usuario = $psicologa;

        $tipo_usuario = 'psicologa';

    } else {

        // 2. Se não for psicóloga, tenta encontrar na tabela de pacientes

        $stmt = $pdo->prepare("SELECT id, nome, senha FROM pacientes WHERE email = ? AND ativo = 1");

        $stmt->execute([$email]);

        $paciente = $stmt->fetch();



        if ($paciente && password_verify($senha, $paciente['senha'])) {

            $usuario = $paciente;

            $tipo_usuario = 'paciente';

        }

    }



    // Se encontrou um usuário e a senha está correta

    if ($usuario) {

        // Limpa sessões antigas e regenera o ID da sessão para segurança

        session_regenerate_id(true);



        // Armazena os dados do usuário na sessão

        $_SESSION['user_id'] = $usuario['id'];

        $_SESSION['user_nome'] = $usuario['nome'];

        $_SESSION['user_type'] = $tipo_usuario;

        $_SESSION['logged_in'] = true;



        // Redireciona para o painel apropriado

        if ($tipo_usuario === 'psicologa') {

            header('Location: area_logada/psicologa/painel.php');

        } else {

            header('Location: area_logada/paciente/painel.php');

        }

        exit;

    } else {

        // Se não encontrou o usuário ou a senha está incorreta

        $_SESSION['login_error'] = 'E-mail ou senha inválidos.';

        header('Location: login.php');

        exit;

    }



} catch (PDOException $e) {

    error_log('Erro no login: ' . $e->getMessage());

    $_SESSION['login_error'] = 'Ocorreu um erro no servidor. Tente novamente.';

    header('Location: login.php');

    exit;

}

?>

