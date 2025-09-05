<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Verifica se a ação foi enviada via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    header('Location: pacientes.php');
    exit;
}

$action = $_POST['action'];

if ($action === 'add_paciente') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validação
    if (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Por favor, forneça um nome e um e-mail válidos.';
        header('Location: pacientes.php');
        exit;
    }

    try {
        $pdo = conectar();

        // Verifica se o e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = 'Este e-mail já está cadastrado na plataforma.';
            header('Location: pacientes.php');
            exit;
        }

        // Gera um token de registro seguro e único
        $token = bin2hex(random_bytes(32));
        // Define a expiração do token (ex: 24 horas)
        $expiracao = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $expiracao->add(new DateInterval('PT24H'));
        $data_expiracao = $expiracao->format('Y-m-d H:i:s');

        // Insere o novo paciente com status pendente
        $stmt = $pdo->prepare(
            "INSERT INTO pacientes (nome, email, token_registro, token_expiracao, ativo) VALUES (?, ?, ?, ?, 0)"
        );
        $stmt->execute([$nome, $email, $token, $data_expiracao]);

        // Cria o link de registro
        $link_registro = BASE_URL . '/registrar.php?token=' . $token;

        // Monta a mensagem de sucesso para a psicóloga
        $_SESSION['success_message'] = "Paciente pré-cadastrado! Envie o link a seguir para que ele(a) complete o registro: <br><br> <strong class='break-all'>$link_registro</strong>";

    } catch (PDOException $e) {
        error_log("Erro ao adicionar paciente: " . $e->getMessage());
        $_SESSION['error_message'] = 'Ocorreu um erro no servidor ao tentar adicionar o paciente. Tente novamente.';
    }

    header('Location: pacientes.php');
    exit;
}
?>
