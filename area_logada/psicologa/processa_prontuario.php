<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pacientes.php');
    exit;
}

// Pega os dados do formulário e valida
$paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
$anotacoes = trim($_POST['anotacoes'] ?? '');

// Se os dados forem inválidos, redireciona de volta
if (!$paciente_id || empty($anotacoes)) {
    $_SESSION['prontuario_error'] = 'O campo de anotações não pode estar vazio.';
    // Tenta redirecionar para a página do paciente, se o ID for conhecido
    $redirect_url = $paciente_id ? "prontuario_paciente.php?id=$paciente_id" : 'pacientes.php';
    header("Location: $redirect_url");
    exit;
}

try {
    $pdo = conectar();
    
    // Insere o novo registro no banco de dados
    $sql = "INSERT INTO prontuarios (paciente_id, anotacoes, data_sessao) VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paciente_id, $anotacoes]);

    // Define uma mensagem de sucesso
    $_SESSION['prontuario_success'] = 'Novo registro de prontuário salvo com sucesso!';

} catch (PDOException $e) {
    error_log("Erro ao salvar prontuário: " . $e->getMessage());
    $_SESSION['prontuario_error'] = 'Ocorreu um erro no servidor ao tentar salvar o registro.';
}

// Redireciona de volta para a página de prontuário do paciente
header("Location: prontuario_paciente.php?id=$paciente_id");
exit;
?>
