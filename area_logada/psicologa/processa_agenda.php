<?php
// Ficheiro: area_logada/psicologa/processa_agenda.php
// Versão Final - Corrigida para funcionar com FormData.

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

// CORREÇÃO: Lê a ação diretamente do array $_POST, que é preenchido pelo FormData.
$action = $_POST['action'] ?? '';

if (empty($action)) {
    // Se a ação estiver vazia, devolve a mensagem de erro que você está a ver.
    echo json_encode(['success' => false, 'message' => 'Ação não especificada. Verifique os dados enviados.']);
    exit;
}

try {
    $pdo = conectar();

    switch ($action) {
        case 'create':
        case 'update':
            $recorrente = isset($_POST['recorrente']);

            if ($recorrente) {
                // Lógica para criar uma regra de recorrência
                $pacienteId = $_POST['paciente_id'] ?? null;
                $data_inicio = $_POST['data'] ?? null;
                $hora_inicio = $_POST['hora_inicio'] ?? null;
                $hora_fim = $_POST['hora_fim'] ?? null;
                $data_fim_recorrencia = $_POST['data_fim_recorrencia'] ?? null;

                if (!$pacienteId || !$data_inicio || !$hora_inicio || !$hora_fim || !$data_fim_recorrencia) {
                    throw new Exception("Todos os campos para agendamento recorrente são obrigatórios.");
                }

                $dia_semana = (new DateTime($data_inicio))->format('w');
                $stmt = $pdo->prepare("INSERT INTO agenda_recorrencias (paciente_id, dia_semana, hora_inicio, hora_fim, data_inicio_recorrencia, data_fim_recorrencia) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$pacienteId, $dia_semana, $hora_inicio, $hora_fim, $data_inicio, $data_fim_recorrencia]);
            } else {
                // Lógica para criar ou atualizar um evento único
                $eventId = $_POST['eventId'] ?? null;
                $pacienteId = $_POST['paciente_id'] ?: null;
                $data = $_POST['data'] ?? null;
                $hora_inicio = $_POST['hora_inicio'] ?? null;
                $hora_fim = $_POST['hora_fim'] ?? null;
                $status = $_POST['status'] ?? 'planejado';

                if (!$data || !$hora_inicio || !$hora_fim) {
                    throw new Exception("Data, hora de início e fim são obrigatórios.");
                }

                $data_hora_inicio = $data . ' ' . $hora_inicio;
                $data_hora_fim = $data . ' ' . $hora_fim;

                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO agenda (paciente_id, data_hora_inicio, data_hora_fim, status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$pacienteId, $data_hora_inicio, $data_hora_fim, $status]);
                } else { // update
                    if (!$eventId) throw new Exception("ID do evento é obrigatório para atualizar.");
                    $stmt = $pdo->prepare("UPDATE agenda SET paciente_id = ?, data_hora_inicio = ?, data_hora_fim = ?, status = ? WHERE id = ?");
                    $stmt->execute([$pacienteId, $data_hora_inicio, $data_hora_fim, $status, $eventId]);
                }
            }
            echo json_encode(['success' => true]);
            break;

        case 'delete_evento':
            $eventId = $_POST['eventId'] ?? null;
            if (!$eventId) throw new Exception("ID do evento é obrigatório para apagar.");
            $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = ?");
            $stmt->execute([$eventId]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_serie':
            $recorrenciaId = $_POST['recorrenciaId'] ?? null;
            if (!$recorrenciaId) throw new Exception("ID da recorrência é obrigatório.");
            $stmt = $pdo->prepare("DELETE FROM agenda_recorrencias WHERE id = ?");
            $stmt->execute([$recorrenciaId]);
            // Também apaga exceções ligadas a esta recorrência
            $stmt = $pdo->prepare("DELETE FROM agenda WHERE recorrencia_id = ?");
            $stmt->execute([$recorrenciaId]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_ocorrencia':
            $recorrenciaId = $_POST['recorrenciaId'] ?? null;
            $dataOcorrencia = $_POST['data'] ?? null;
            if (!$recorrenciaId || !$dataOcorrencia) throw new Exception("Dados insuficientes para cancelar a ocorrência.");
            
            // Insere um evento 'cancelado' para sobrepor a recorrência nesta data específica
            $stmt = $pdo->prepare("INSERT INTO agenda (recorrencia_id, data_hora_inicio, status) VALUES (?, ?, 'cancelado')");
            $stmt->execute([$recorrenciaId, $dataOcorrencia . ' 00:00:00']);
            echo json_encode(['success' => true]);
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
