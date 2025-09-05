<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/auth_psicologa.php';
require_once __DIR__ . '/../../includes/db.php';

$response = ['success' => false, 'message' => 'Ação inválida.'];
$paciente_id = $_POST['paciente_id'] ?? null;

if (!$paciente_id) {
    $response['message'] = 'ID do paciente não fornecido.';
    echo json_encode($response);
    exit;
}

if (defined('WHEREBY_API_KEY') && WHEREBY_API_KEY != 'SUA_CHAVE_DE_API_AQUI') {
    try {
        $pdo = conectar();
        
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

        // **CORREÇÃO: URL da API atualizada para api.whereby.dev**
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
            // Agora, vamos fornecer a mensagem de erro exata da API para o utilizador.
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

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'A chave da API do Whereby não está configurada no servidor.';
}

echo json_encode($response);
?>
