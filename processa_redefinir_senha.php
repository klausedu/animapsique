<?php
require_once 'config.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$token = $_POST['token'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';

// --- Validações ---
if (empty($token) || empty($senha) || empty($confirma_senha)) {
    // Idealmente, usar a sessão para passar a mensagem de erro
    header('Location: redefinir_senha.php?token=' . urlencode($token) . '&error=empty');
    exit;
}

if ($senha !== $confirma_senha) {
    header('Location: redefinir_senha.php?token=' . urlencode($token) . '&error=mismatch');
    exit;
}

if (strlen($senha) < 8) {
    header('Location: redefinir_senha.php?token=' . urlencode($token) . '&error=short');
    exit;
}

try {
    $pdo = conectar();
    $tabela = '';
    $agora = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
    
    // Procura o token na tabela de psicólogos
    $stmt_psicologo = $pdo->prepare("SELECT id FROM psicologos WHERE token_reset_senha = ? AND token_reset_expiracao > ?");
    $stmt_psicologo->execute([$token, $agora]);
    $usuario = $stmt_psicologo->fetch();
    if ($usuario) {
        $tabela = 'psicologos';
    }

    // Se não encontrou, procura na tabela de pacientes
    if (!$usuario) {
        $stmt_paciente = $pdo->prepare("SELECT id FROM pacientes WHERE token_reset_senha = ? AND token_reset_expiracao > ?");
        $stmt_paciente->execute([$token, $agora]);
        $usuario = $stmt_paciente->fetch();
        if ($usuario) {
            $tabela = 'pacientes';
        }
    }

    if ($usuario && $tabela) {
        // Encripta a nova senha
        $senha_hash = password_hash($senha, PASSWORD_ARGON2ID);

        // Atualiza a senha e limpa o token de redefinição
        $sql_update = "UPDATE {$tabela} SET senha = ?, token_reset_senha = NULL, token_reset_expiracao = NULL WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$senha_hash, $usuario['id']]);

        $_SESSION['success_message'] = "Senha redefinida com sucesso! Pode agora fazer o login com a sua nova senha.";
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['login_error'] = "Link inválido ou expirado. Por favor, solicite um novo.";
        header('Location: login.php');
        exit;
    }

} catch (Exception $e) {
    error_log("Erro ao redefinir senha: " . $e->getMessage());
    $_SESSION['login_error'] = "Ocorreu um erro no servidor. Tente novamente.";
    header('Location: login.php');
    exit;
}
