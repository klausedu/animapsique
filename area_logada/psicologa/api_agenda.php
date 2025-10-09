<?php
// Ativar a exibição de todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Função para enviar uma resposta de erro e terminar o script
function send_error_and_exit($message, $details = null) {
    http_response_code(500);
    $response = ['status' => 'error', 'message' => $message];
    if ($details) {
        $response['details'] = $details;
    }
    echo json_encode($response);
    exit;
}

// Etapa 1: Calcular o caminho base
$base_path = realpath(dirname(__FILE__) . '/../../');
if (!$base_path) {
    send_error_and_exit("Falha na Etapa 1: Não foi possível determinar o caminho base (base_path).");
}

// Etapa 2: Verificar e carregar config.php
$config_path = $base_path . '/config.php';
if (!file_exists($config_path)) {
    send_error_and_exit("Falha na Etapa 2: O ficheiro de configuração não foi encontrado.", "Caminho procurado: " . $config_path);
}
require_once $config_path;

// Iniciar a sessão após carregar a configuração
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Etapa 3: Verificar a autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'psicologa') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Falha na Etapa 3: Acesso não autorizado.']);
    exit;
}

// Etapa 4: Verificar e carregar db.php
$db_path = $base_path . '/includes/db.php';
if (!file_exists($db_path)) {
    send_error_and_exit("Falha na Etapa 4: O ficheiro de base de dados não foi encontrado.", "Caminho procurado: " . $db_path);
}
require_once $db_path;


// Etapa 5: Verificar se a função conectar() existe
if (!function_exists('conectar')) {
    send_error_and_exit("Falha na Etapa 5: O ficheiro db.php foi carregado, mas a função conectar() não existe.");
}

// Etapa 6: Tentar conectar à base de dados
try {
    $pdo = conectar();
    if (!$pdo) {
        throw new Exception("A função conectar() retornou um valor nulo ou falso.");
    }
} catch (Throwable $e) {
    send_error_and_exit("Falha na Etapa 6: Erro ao conectar à base de dados.", $e->getMessage());
}

// Se todas as etapas passarem, o problema está na lógica da agenda
echo json_encode([
    'status' => 'success',
    'message' => 'Todas as dependências foram carregadas e a conexão com a base de dados foi bem-sucedida. O problema está na lógica de consulta de eventos.'
]);

// A lógica original da agenda viria aqui.
// Por agora, deixamos de fora para garantir que a base funciona.

?>
