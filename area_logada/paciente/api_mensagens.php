<?php
header('Content-Type: application/json');

$base_path = realpath(dirname(__FILE__) . '/../../');
require_once $base_path . '/config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'paciente') {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão inválida.']);
    exit;
}

require_once $base_path . '/includes/db.php';

$paciente_id = $_SESSION['user_id'];

try {
    $pdo = conectar();
    
    $sql = "
        (
            SELECT remetente, assunto, conteudo, data_envio
            FROM mensagens
            WHERE remetente = 'paciente' AND remetente_id = :paciente_id_1
        )
        UNION ALL
        (
            SELECT m.remetente, m.assunto, m.conteudo, m.data_envio
            FROM mensagens m
            INNER JOIN mensagens_status ms ON m.id = ms.mensagem_id
            WHERE (m.remetente = 'psicologa' AND ms.usuario_id = :paciente_id_2)
        )
        ORDER BY data_envio ASC;
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':paciente_id_1', $paciente_id, PDO::PARAM_INT);
    $stmt->bindValue(':paciente_id_2', $paciente_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($mensagens);

} catch (Exception $e) {
    http_response_code(500);
    error_log("Erro na API de mensagens do paciente: " . $e->getMessage());
    echo json_encode(['error' => 'Erro ao buscar o histórico de mensagens.']);
}
?>
