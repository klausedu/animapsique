<?php
// Ficheiro: area_logada/psicologa/processa_agenda.php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$base_path = realpath(dirname(__FILE__) . '/../../');

require_once $base_path . '/config.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'psicologa') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
    exit;
}
require_once $base_path . '/includes/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Ação não especificada.']);
    exit;
}

try {
    $pdo = conectar();
    switch ($action) {
        case 'create':
            $start = $input['start'] ?? null;
            $end = $input['end'] ?? null;
            $pacienteId = $input['pacienteId'] ?: null;
            $status = $pacienteId ? 'confirmado' : 'livre';

            if (!$start || !$end) throw new Exception("Data de início e fim são obrigatórias.");

            $stmt = $pdo->prepare("INSERT INTO agenda (data_hora_inicio, data_hora_fim, paciente_id, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$start, $end, $pacienteId, $status]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'update':
            $id = $input['id'] ?? null;
            $pacienteId = $input['pacienteId'] ?: null;
            $status = $pacienteId ? 'confirmado' : 'livre';
            
            if (!$id) throw new Exception("ID do evento é obrigatório para atualizar.");

            $stmt = $pdo->prepare("UPDATE agenda SET paciente_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$pacienteId, $status, $id]);
            echo json_encode(['success' => true]);
            break;
            
        case 'delete':
            $id = $input['id'] ?? null;
            if (!$id) throw new Exception("ID do evento é obrigatório para apagar.");
            $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;
        
        case 'cancel_recorrencia':
            $recorrenciaId = $input['recorrenciaId'] ?? null;
            $dataEvento = $input['start'] ?? null;

            if (!$recorrenciaId || !$dataEvento) throw new Exception("Dados insuficientes para cancelar.");

            $stmt = $pdo->prepare("INSERT INTO agenda (recorrencia_id, data_hora_inicio, status) VALUES (?, ?, 'cancelado')");
            $stmt->execute([$recorrenciaId, $dataEvento]);
            echo json_encode(['success' => true, 'message' => 'Ocorrência cancelada.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida: ' . htmlspecialchars($action)]);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de servidor: ' . $e->getMessage()]);
}
?>
