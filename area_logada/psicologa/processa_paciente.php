<?php
header('Content-Type: application/json');
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$response = ['success' => false, 'message' => 'Ação inválida.'];
$action = $_POST['action'] ?? '';

try {
    $pdo = conectar();
    
    if ($action === 'create') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO pacientes (nome, email, telefone, senha, ativo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone, $senha, $ativo]);
        $paciente_id = $pdo->lastInsertId();

        // **INÍCIO: Criar sala de reunião permanente Whereby**
        if (defined('WHEREBY_API_KEY') && WHEREBY_API_KEY != 'SUA_CHAVE_DE_API_AQUI') {
            try {
                $ch = curl_init();
                
                // Usamos o endpoint /meetings para criar salas que expiram, o que é ideal para sessões.
                // Para salas "permanentes" no contexto de um paciente, criamos uma com data de fim muito longa.
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
                    "roomNamePattern": "personal",
                    "fields" => ["hostRoomUrl"] 
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
                    if (isset($whereby_data['roomUrl'])) {
                        $stmt_update_url = $pdo->prepare("UPDATE pacientes SET whereby_room_url = ? WHERE id = ?");
                        $stmt_update_url->execute([$whereby_data['roomUrl'], $paciente_id]);
                    }
                }
                curl_close($ch);
            } catch (Exception $e) {
                error_log("Error creating Whereby room for patient ID $paciente_id: " . $e->getMessage());
            }
        }
        // **FIM: Criar sala Whereby**
        
        $response = ['success' => true];

    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        $sql = "UPDATE pacientes SET nome = ?, email = ?, telefone = ?, ativo = ? WHERE id = ?";
        $params = [$nome, $email, $telefone, $ativo, $id];

        if (!empty($_POST['senha'])) {
            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            $sql = "UPDATE pacientes SET nome = ?, email = ?, telefone = ?, senha = ?, ativo = ? WHERE id = ?";
            $params = [$nome, $email, $telefone, $senha, $ativo, $id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $response = ['success' => true];
        
    } else {
        throw new Exception('Ação desconhecida.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
