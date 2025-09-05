<?php
header('Content-Type: application/json');
// **CORREÇÃO: O caminho para os arquivos foi ajustado.**
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
        
        // Criar a sala na API do Whereby
        $ch = curl_init();
        $endDate = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))
            ->modify('+5 years') // A sala será válida por 5 anos
            ->setTimezone(new DateTimeZone('UTC'))
            ->format('Y-m-d\TH:i:s.v\Z');

        curl_setopt($ch, CURLOPT_URL, "https://api.whereby.com/v1/meetings");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "isLocked" => true,
            "endDate" => $endDate,
            "roomNamePattern" => "personal",
            "fields" => ["hostRoomUrl"] 
        ]));

        $headers = [
            'Authorization: Bearer ' . WHEREBY_API_KEY,
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code >= 400) {
            // Log do erro retornado pela API para depuração
            error_log("Whereby API Error (HTTP $http_code): " . $result);
            throw new Exception("O serviço de vídeo retornou um erro. Tente novamente mais tarde.");
        }
        
        $whereby_data = json_decode($result, true);
        if (isset($whereby_data['roomUrl'])) {
            $roomUrl = $whereby_data['roomUrl'];
            
            // Atualizar o paciente no banco de dados com a nova URL
            $stmt = $pdo->prepare("UPDATE pacientes SET whereby_room_url = ? WHERE id = ?");
            $stmt->execute([$roomUrl, $paciente_id]);

            $response = [
                'success' => true,
                'roomUrl' => $roomUrl
            ];
        } else {
            throw new Exception("A resposta da API do Whereby não continha uma URL da sala.");
        }

    } catch (Exception $e) {
        // Log do erro para que você possa ver os detalhes no servidor
        error_log("Erro ao criar sala Whereby para o paciente ID $paciente_id: " . $e->getMessage());
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'A chave da API do Whereby não está configurada no servidor.';
}

echo json_encode($response);
?>
