<?php
require_once 'config.php';
require_once 'includes/db.php';

$token = $_POST['token'] ?? '';
$senha = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';

// --- Validações ---
if (empty($token) || empty($senha) || empty($confirma_senha)) {
    // Redireciona de volta com erro se algum campo estiver vazio
    // Idealmente, passar a mensagem de erro via sessão
    header('Location: registrar.php?token=' . urlencode($token) . '&error=empty');
    exit;
}

if ($senha !== $confirma_senha) {
    // Redireciona se as senhas não conferem
    header('Location: registrar.php?token=' . urlencode($token) . '&error=mismatch');
    exit;
}

if (strlen($senha) < 8) {
    // Redireciona se a senha for muito curta
    header('Location: registrar.php?token=' . urlencode($token) . '&error=short');
    exit;
}

try {
    $pdo = conectar();
    // Verifica se o token é válido e corresponde a um usuário pendente
    $stmt = $pdo->prepare("SELECT id FROM pacientes WHERE token_registro = ? AND token_expiracao > NOW() AND ativo = 0");
    $stmt->execute([$token]);
    $paciente = $stmt->fetch();

    if (!$paciente) {
        // Se o token for inválido, redireciona para uma página de erro genérica ou login
        header('Location: login.php?error=invalid_token');
        exit;
    }

    // Criptografa a senha com o algoritmo mais seguro disponível no PHP
    $senha_hash = password_hash($senha, PASSWORD_ARGON2ID);

    // Atualiza o cadastro do paciente: define a senha, ativa a conta e remove o token
    $stmt = $pdo->prepare(
        "UPDATE pacientes SET senha = ?, ativo = 1, token_registro = NULL, token_expiracao = NULL WHERE id = ?"
    );
    $stmt->execute([$senha_hash, $paciente['id']]);

    // Define uma mensagem de sucesso na sessão para exibir na próxima página
    $_SESSION['registro_sucesso'] = "Sua conta foi ativada com sucesso! Agora você já pode fazer o login.";
    header('Location: login.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao processar registro: " . $e->getMessage());
    // Redireciona para uma página de erro genérica
    header('Location: registrar.php?token=' . urlencode($token) . '&error=server');
    exit;
}

?>
