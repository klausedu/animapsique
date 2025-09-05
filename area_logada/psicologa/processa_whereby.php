<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/auth_psicologa.php';
require_once __DIR__ . '/../../includes/db.php';

$response = ['success' => false, 'message' => 'Ação inválida.'];
$paciente_id = $_POST['paciente_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$paciente_id) {
    $response['message'] = 'ID do paciente não fornecido.';
    echo json_encode($response);
    exit;
}

try {
    $pdo = conectar();

    if ($action === 'create_room') {
        if (defined('WHEREBY_API_KEY') && WHEREBY_API_KEY != 'SUA_CHAVE_DE_API_AQUI') {
            
            $ch = curl_init();
            $endDate = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))
                ->modify('+5 years')
                ->setTimezone(new DateTimeZone('UTC'))
                ->format('Y-m-d\TH:i:s.v\Z');

            $postData = [
                "isLocked" => true,
                "endDate" => $endDate,
                "fields" => ["hostRoomUrl"] 
            ];

            curl_setopt($ch, CURLOPT_URL, "https://api.whereby.dev/v1/meetings");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            $headers = [
                'Authorization: Bearer ' . WHEREBY_API_KEY,
                'Content-Type: application/json'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code >= 400) {
                error_log("Whereby API Error (HTTP $http_code) para Paciente ID $paciente_id: " . $result);
                $api_error = json_decode($result, true);
                $error_message = $api_error['error']['message'] ?? $result;
                throw new Exception("O serviço de vídeo retornou um erro: " . $error_message);
            }
            
            $whereby_data = json_decode($result, true);
            if (isset($whereby_data['roomUrl'])) {
                $roomUrl = $whereby_data['roomUrl'];
                $stmt = $pdo->prepare("UPDATE pacientes SET whereby_room_url = ? WHERE id = ?");
                $stmt->execute([$roomUrl, $paciente_id]);
                $response = [ 'success' => true, 'roomUrl' => $roomUrl ];
            } else {
                throw new Exception("A resposta da API do Whereby não continha uma URL da sala.");
            }
        } else {
            $response['message'] = 'A chave da API do Whereby não está configurada no servidor.';
        }
    } elseif ($action === 'remove_room') {
        // Nova lógica para remover a sala
        $stmt = $pdo->prepare("UPDATE pacientes SET whereby_room_url = NULL WHERE id = ?");
        $stmt->execute([$paciente_id]);
        $response = ['success' => true];
    } else {
        $response['message'] = 'Ação desconhecida.';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Erro em processa_whereby.php: " . $e->getMessage());
}

echo json_encode($response);
?>
