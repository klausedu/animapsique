<?php
/*
Conteúdo para: area_logada/psicologa/api_recibos.php (NOVO FICHEIRO)
*/
header('Content-Type: application/json');
$base_path = realpath(dirname(__FILE__) . '/../../');
require_once $base_path . '/config.php';

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(401); exit;
}
require_once $base_path . '/includes/db.php';

$paciente_id = filter_input(INPUT_GET, 'paciente_id', FILTER_VALIDATE_INT);
if (!$paciente_id) {
    echo json_encode(['recibos' => [], 'paciente' => null]);
    exit;
}

try {
    $pdo = conectar();
    
    $stmt_paciente = $pdo->prepare("SELECT nome FROM pacientes WHERE id = ?");
    $stmt_paciente->execute([$paciente_id]);
    $paciente = $stmt_paciente->fetch(PDO::FETCH_ASSOC);

    $stmt_recibos = $pdo->prepare("SELECT id, caminho_pdf, data_emissao, valor_recebido, data_recebimento FROM recibos WHERE paciente_id = ? ORDER BY data_emissao DESC");
    $stmt_recibos->execute([$paciente_id]);
    $recibos = $stmt_recibos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['recibos' => $recibos, 'paciente' => $paciente]);

} catch (Exception $e) {
    http_response_code(500);
    error_log("Erro na API de recibos: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
