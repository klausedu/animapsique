<?php
require_once 'config.php';
require_once 'includes/db.php';

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$senha_antiga = $_POST['senha_antiga'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirma_nova_senha = $_POST['confirma_nova_senha'] ?? '';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$redirect_url = ($user_type === 'psicologa') ? 'area_logada/psicologa/alterar_senha.php' : 'area_logada/paciente/alterar_senha.php';

// Validações
if (empty($senha_antiga) || empty($nova_senha) || empty($confirma_nova_senha)) {
    $_SESSION['senha_error'] = "Todos os campos são obrigatórios.";
    header("Location: $redirect_url");
    exit;
}
if ($nova_senha !== $confirma_nova_senha) {
    $_SESSION['senha_error'] = "A nova senha e a confirmação não coincidem.";
    header("Location: $redirect_url");
    exit;
}
if (strlen($nova_senha) < 8) {
    $_SESSION['senha_error'] = "A nova senha deve ter pelo menos 8 caracteres.";
    header("Location: $redirect_url");
    exit;
}

try {
    $pdo = conectar();
    $tabela = ($user_type === 'psicologa') ? 'psicologos' : 'pacientes';

    // Busca a senha antiga do utilizador
    $stmt = $pdo->prepare("SELECT senha FROM {$tabela} WHERE id = ?");
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch();

    // Verifica se a senha antiga está correta
    if (!$usuario || !password_verify($senha_antiga, $usuario['senha'])) {
        $_SESSION['senha_error'] = "A senha antiga está incorreta.";
        header("Location: $redirect_url");
        exit;
    }

    // Encripta e atualiza a nova senha
    $nova_senha_hash = password_hash($nova_senha, PASSWORD_ARGON2ID);
    $stmt_update = $pdo->prepare("UPDATE {$tabela} SET senha = ? WHERE id = ?");
    $stmt_update->execute([$nova_senha_hash, $user_id]);

    $_SESSION['senha_success'] = "Senha alterada com sucesso!";
    header("Location: $redirect_url");
    exit;

} catch (Exception $e) {
    error_log("Erro ao alterar senha: " . $e->getMessage());
    $_SESSION['senha_error'] = "Ocorreu um erro no servidor.";
    header("Location: $redirect_url");
    exit;
}
