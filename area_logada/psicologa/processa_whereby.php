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
                // **MUDANÇA: Pedir o meetingId à API**
                "fields" => ["hostRoomUrl", "meetingId"] 
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
                $api_error = json_decode($result, true);
                $error_message = $api_error['error']['message'] ?? $result;
                throw new Exception("O serviço de vídeo retornou um erro: " . $error_message);
            }
            
            $whereby_data = json_decode($result, true);
            if (isset($whereby_data['roomUrl']) && isset($whereby_data['meetingId'])) {
                $roomUrl = $whereby_data['roomUrl'];
                $meetingId = $whereby_data['meetingId'];
                
                // **MUDANÇA: Guardar o roomUrl e o meetingId na base de dados**
                $stmt = $pdo->prepare("UPDATE pacientes SET whereby_room_url = ?, whereby_meeting_id = ? WHERE id = ?");
                $stmt->execute([$roomUrl, $meetingId, $paciente_id]);

                $response = [ 'success' => true, 'roomUrl' => $roomUrl ];
            } else {
                throw new Exception("A resposta da API do Whereby não continha os dados necessários.");
            }
        } else {
            $response['message'] = 'A chave da API do Whereby não está configurada no servidor.';
        }

    } elseif ($action === 'remove_room') {
        // **INÍCIO DA NOVA LÓGICA PARA REMOVER A SALA**
        
        // 1. Encontrar o meetingId na base de dados
        $stmt_select = $pdo->prepare("SELECT whereby_meeting_id FROM pacientes WHERE id = ?");
        $stmt_select->execute([$paciente_id]);
        $paciente = $stmt_select->fetch();
        $meetingId = $paciente['whereby_meeting_id'] ?? null;

        // 2. Se existir um meetingId, chamar a API para apagar
        if ($meetingId && defined('WHEREBY_API_KEY') && WHEREBY_API_KEY != 'SUA_CHAVE_DE_API_AQUI') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.whereby.dev/v1/meetings/" . $meetingId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            $headers = ['Authorization: Bearer ' . WHEREBY_API_KEY];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Se a sala não for encontrada (404), não há problema, ela já não existe.
            // Se der outro erro, registamos no log, mas continuamos.
            if ($http_code >= 400 && $http_code != 404) {
                 error_log("Whereby API Delete Error (HTTP $http_code) para Meeting ID $meetingId");
            }
        }
        
        // 3. Limpar os dados da base de dados, independentemente do resultado da API
        $stmt_update = $pdo->prepare("UPDATE pacientes SET whereby_room_url = NULL, whereby_meeting_id = NULL WHERE id = ?");
        $stmt_update->execute([$paciente_id]);
        $response = ['success' => true];
        // **FIM DA NOVA LÓGICA**

    } else {
        $response['message'] = 'Ação desconhecida.';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Erro em processa_whereby: " . $e->getMessage());
}

echo json_encode($response);
?>
