<?php
/*
Conteúdo para: area_logada/psicologa/processa_agenda.php
*/
header('Content-Type: application/json');
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$response = ['success' => false, 'message' => 'Ação inválida.'];
$action = $_POST['action'] ?? '';

try {
    $pdo = conectar();
    $pdo->beginTransaction(); // Inicia a transação para garantir a consistência dos dados

    if ($action === 'create') {
        $paciente_id = $_POST['paciente_id'];
        $data = $_POST['data'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $status = $_POST['status'] ?? 'planejado'; // Garante um status padrão
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
            $dia_semana = (new DateTime($data))->format('w'); // 0=Dom, 1=Seg...

            $sql = "INSERT INTO agenda_recorrencias (paciente_id, titulo, hora_inicio, hora_fim, dia_semana, data_inicio_recorrencia, data_fim_recorrencia) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$paciente_id, $titulo, $hora_inicio, $hora_fim, $dia_semana, $data, $data_fim_recorrencia]);
        
        } else {
            $data_hora_inicio = $data . ' ' . $hora_inicio;
            $data_hora_fim = $data . ' ' . $hora_fim;
            
            $sql = "INSERT INTO agenda (paciente_id, data_hora_inicio, data_hora_fim, status) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$paciente_id, $data_hora_inicio, $data_hora_fim, $status]);
            $evento_id = $pdo->lastInsertId();

            // INÍCIO: Criar sala de reunião Whereby
            if ($paciente_id && $status === 'planejado' && defined('WHEREBY_API_KEY') && WHEREBY_API_KEY != 'SUA_CHAVE_DE_API_AQUI') {
                try {
                    $ch = curl_init();
                    $endDate = (new DateTime($data_hora_fim, new DateTimeZone('America/Sao_Paulo')))
                        ->setTimezone(new DateTimeZone('UTC'))
                        ->format('Y-m-d\TH:i:s.v\Z');

                    curl_setopt($ch, CURLOPT_URL, "https://api.whereby.com/v1/meetings");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                        "endDate" => $endDate,
                        "fields" => ["hostRoomUrl", "viewerRoomUrl"] // Mudado para pegar a sala do paciente
                    ]));

                    $headers = [
                        'Authorization: Bearer ' . WHEREBY_API_KEY,
                        'Content-Type: application/json'
                    ];
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if (curl_errno($ch) || $http_code >= 400) {
                        error_log("Whereby API Error: " . curl_error($ch) . " - " . $result);
                    } else {
                        $whereby_data = json_decode($result, true);
                        // Usar viewerRoomUrl para o paciente
                        if (isset($whereby_data['viewerRoomUrl'])) { 
                            $stmt_update_url = $pdo->prepare("UPDATE agenda SET sala_reuniao_url = ? WHERE id = ?");
                            $stmt_update_url->execute([$whereby_data['viewerRoomUrl'], $evento_id]);
                        }
                    }
                    curl_close($ch);
                } catch (Exception $e) {
                    error_log("Error creating Whereby room: " . $e->getMessage());
                    // Não lança uma exceção principal, para que o agendamento seja salvo mesmo se o Whereby falhar
                }
            }
            // FIM: Criar sala de reunião Whereby
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
        
        // Em vez de apagar, cria um evento "cancelado" para anular a recorrência nesse dia
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

    $pdo->commit(); // Confirma as alterações

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Desfaz as alterações em caso de erro
    }
    $response['message'] = $e->getMessage();
    error_log("Erro em processa_agenda.php: " . $e->getMessage()); // Loga o erro
}

echo json_encode($response);
?>
