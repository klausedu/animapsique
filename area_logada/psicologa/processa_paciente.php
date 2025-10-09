<?php
// Bloco de Depuração - Início
// Este bloco irá registar os detalhes de cada requisição num arquivo de log.
$log_file = __DIR__ . '/debug_log.txt';
$log_data = "========================================\n";
$log_data .= "Data/Hora: " . date("Y-m-d H:i:s") . "\n";
$log_data .= "Método da Requisição: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log_data .= "Query String: " . $_SERVER['QUERY_STRING'] . "\n";
$log_data .= "Dados GET: " . print_r($_GET, true) . "\n";
$log_data .= "Dados POST: " . print_r($_POST, true) . "\n";
$raw_input = file_get_contents('php://input');
$log_data .= "Input Raw: " . $raw_input . "\n";
$log_data .= "========================================\n\n";
file_put_contents($log_file, $log_data, FILE_APPEND);
// Bloco de Depuração - Fim


require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

header('Content-Type: application/json');

// Tenta obter a ação de GET, POST ou do input raw (para o caso de JSON)
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if (empty($action)) {
    $input_data = json_decode($raw_input, true);
    $action = $input_data['action'] ?? '';
}


try {
    $pdo = conectar();

    switch ($action) {
        case 'add':
            // Lógica para adicionar
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if (empty($nome) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Nome e Email são obrigatórios.']);
                exit;
            }
            
            $senha_hash = !empty($senha) ? password_hash($senha, PASSWORD_DEFAULT) : null;

            $stmt = $pdo->prepare("INSERT INTO pacientes (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $telefone, $senha_hash]);

            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'edit':
            // Lógica para editar
            $id = $_POST['id'] ?? 0;
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($id) || empty($nome) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'ID, Nome e Email são obrigatórios.']);
                exit;
            }

            if (!empty($senha)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE pacientes SET nome = ?, email = ?, telefone = ?, senha = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $telefone, $senha_hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE pacientes SET nome = ?, email = ?, telefone = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $telefone, $id]);
            }
            
            echo json_encode(['success' => true]);
            break;

        case 'get_paciente':
            // Lógica para obter dados de um paciente
            $id = $_GET['id'] ?? 0;
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID do paciente não fornecido.']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id, nome, email, telefone FROM pacientes WHERE id = ?");
            $stmt->execute([$id]);
            $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($paciente) {
                echo json_encode(['success' => true, 'paciente' => $paciente]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Paciente não encontrado.']);
            }
            break;

        case 'delete':
            // Lógica para deletar
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
             if (empty($id)) {
                $input_data = json_decode($raw_input, true);
                $id = $input_data['id'] ?? 0;
            }

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID do paciente não fornecido para exclusão.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'toggle_status':
            // Lógica para ativar/desativar
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            $status = $_GET['status'] ?? $_POST['status'] ?? null;
            
            if (empty($id) || $status === null) {
                echo json_encode(['success' => false, 'message' => 'Dados insuficientes para alterar o status.']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE pacientes SET ativo = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['success' => true]);
            break;

        default:
            // Se nenhuma ação válida for encontrada
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida.']);
            break;
    }
} catch (PDOException $e) {
    // Registra o erro no log de depuração também
    file_put_contents($log_file, "ERRO DE PDO: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
