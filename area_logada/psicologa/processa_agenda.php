<?php
header('Content-Type: application/json');
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$response = ['success' => false, 'message' => 'Ação inválida.'];
$action = $_POST['action'] ?? '';

try {
    $pdo = conectar();
    $pdo->beginTransaction();

    if ($action === 'create') {
        $paciente_id = $_POST['paciente_id'];
        $data = $_POST['data'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $status = $_POST['status'] ?? 'planejado';
        $is_recorrente = isset($_POST['recorrente']);

        if (empty($paciente_id) || empty($data) || empty($hora_inicio) || empty($hora_fim)) {
            throw new Exception('Paciente, data e horários são obrigatórios.');
        }

        $stmt_paciente = $pdo->prepare("SELECT nome FROM pacientes WHERE id = ?");
        $stmt_paciente->execute([$paciente_id]);
        $paciente = $stmt_paciente->fetch();
        $titulo = $paciente ? $paciente['nome'] : 'Agendamento';

        if ($is_recorrente) {
            $data_fim_recorrencia = $_POST['data_fim_recorrencia'];
            $dia_semana = (new DateTime($data))->format('w');
            $sql = "INSERT INTO agenda_recorrencias (paciente_id, titulo, hora_inicio, hora_fim, dia_semana, data_inicio_recorrencia, data_fim_recorrencia) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$paciente_id, $titulo, $hora_inicio, $hora_fim, $dia_semana, $data, $data_fim_recorrencia]);
        } else {
            $data_hora_inicio = $data . ' ' . $hora_inicio;
            $data_hora_fim = $data . ' ' . $hora_fim;
            $sql = "INSERT INTO agenda (paciente_id, data_hora_inicio, data_hora_fim, status) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$paciente_id, $data_hora_inicio, $data_hora_fim, $status]);
        }
        $response = ['success' => true];

    } elseif ($action === 'delete_evento') {
        $eventId = $_POST['eventId'];
        $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = ?");
        $stmt->execute([$eventId]);
        $response = ['success' => true];

    } elseif ($action === 'delete_serie') {
        $recorrenciaId = $_POST['recorrenciaId'];
        $stmt = $pdo->prepare("DELETE FROM agenda_recorrencias WHERE id = ?");
        $stmt->execute([$recorrenciaId]);
        $response = ['success' => true];

    } elseif ($action === 'delete_ocorrencia') {
        $recorrenciaId = $_POST['recorrenciaId'];
        $dataOcorrencia = $_POST['data'];
        
        $stmt_regra = $pdo->prepare("SELECT * FROM agenda_recorrencias WHERE id = ?");
        $stmt_regra->execute([$recorrenciaId]);
        $regra = $stmt_regra->fetch();

        if ($regra) {
            $data_hora_inicio = $dataOcorrencia . ' ' . $regra['hora_inicio'];
            $data_hora_fim = $dataOcorrencia . ' ' . $regra['hora_fim'];
            $sql = "INSERT INTO agenda (paciente_id, data_hora_inicio, data_hora_fim, status, recorrencia_id) VALUES (?, ?, ?, 'cancelado', ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$regra['paciente_id'], $data_hora_inicio, $data_hora_fim, $recorrenciaId]);
        }
        $response = ['success' => true];
    } else {
         throw new Exception('Ação desconhecida.');
    }

    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = $e->getMessage();
    error_log("Erro em processa_agenda.php: " . $e->getMessage());
}

echo json_encode($response);
?>
