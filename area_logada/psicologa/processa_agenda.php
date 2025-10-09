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

// Recebe os dados como JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Ação não especificada.']);
    exit;
}

try {
    $pdo = conectar();

    switch ($action) {
        case 'create':
            $start = $input['start'];
            $end = $input['end'];
            $pacienteId = $input['pacienteId'] ?: null;
            $status = $pacienteId ? 'confirmado' : 'livre';

            $stmt = $pdo->prepare("INSERT INTO agenda (data_hora_inicio, data_hora_fim, paciente_id, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$start, $end, $pacienteId, $status]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'update':
            $id = $input['id'];
            $pacienteId = $input['pacienteId'] ?: null;
            $status = $pacienteId ? 'confirmado' : 'livre';
            
            $stmt = $pdo->prepare("UPDATE agenda SET paciente_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$pacienteId, $status, $id]);

            echo json_encode(['success' => true]);
            break;
            
        case 'delete':
            $id = $input['id'];
            $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true]);
            break;
        
        case 'cancel_recorrencia':
            // Cancela uma ocorrência de um evento recorrente
            $recorrenciaId = $input['recorrenciaId'];
            $dataEvento = $input['start']; // A data/hora de início da ocorrência a cancelar

            $stmt = $pdo->prepare("INSERT INTO agenda (recorrencia_id, data_hora_inicio, status) VALUES (?, ?, 'cancelado')");
            $stmt->execute([$recorrenciaId, $dataEvento]);

            echo json_encode(['success' => true, 'message' => 'Ocorrência cancelada.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida.']);
            break;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de servidor: ' . $e->getMessage()]);
}
?>
