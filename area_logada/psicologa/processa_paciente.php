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
