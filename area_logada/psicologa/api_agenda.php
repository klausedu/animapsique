<?php
// ATIVAR A EXIBIÇÃO DE ERROS PARA DEPURAÇÃO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Caminho para o ficheiro de log
$log_file = __DIR__ . '/agenda_debug_log.txt';
// Limpa o log antigo a cada execução para facilitar a leitura
file_put_contents($log_file, "Iniciando depuração da API da Agenda em " . date('Y-m-d H:i:s') . "\n");

function log_message($message) {
    global $log_file;
    file_put_contents($log_file, $message . "\n", FILE_APPEND);
}

try {
    log_message("1. Script iniciado.");

    $base_path = realpath(dirname(__FILE__) . '/../../');
    if (!$base_path) {
        throw new Exception("Erro fatal: Não foi possível calcular o caminho base (base_path).");
    }
    log_message("2. Caminho base calculado: " . $base_path);

    // Verifica se os ficheiros de configuração existem antes de os incluir
    if (!file_exists($base_path . '/config.php')) throw new Exception("Erro fatal: ficheiro config.php não encontrado.");
    require_once $base_path . '/config.php';
    log_message("3. config.php carregado.");
    
    // Inicia a sessão se ainda não tiver sido iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'psicologa') {
        log_message("ERRO: Acesso não autorizado.");
        http_response_code(403);
        echo json_encode(['error' => 'Acesso não autorizado.']);
        exit;
    }
    log_message("4. Autenticação verificada com sucesso.");

    if (!file_exists($base_path . '/includes/db.php')) throw new Exception("Erro fatal: ficheiro db.php não encontrado.");
    require_once $base_path . '/includes/db.php';
    log_message("5. db.php carregado.");

    log_message("6. A tentar conectar à base de dados...");
    $pdo = conectar();
    log_message("7. Conexão à base de dados estabelecida.");

    $eventos = [];
    
    $start_param = $_GET['start'] ?? date('Y-m-01');
    $end_param = $_GET['end'] ?? date('Y-m-t');
    log_message("8. Parâmetros recebidos: START=" . $start_param . ", END=" . $end_param);
    
    // Conversão de datas
    $start_date_obj = new DateTime($start_param);
    $end_date_obj = new DateTime($end_param);
    log_message("9. Objetos DateTime criados com sucesso.");
    
    $start_date_query = $start_date_obj->format('Y-m-d H:i:s');
    $end_date_query = $end_date_obj->format('Y-m-d H:i:s');
    log_message("10. Datas formatadas para a consulta: START=" . $start_date_query . ", END=" . $end_date_query);

    // Consulta principal
    log_message("11. A preparar a consulta de eventos individuais...");
    $stmt_individuais = $pdo->prepare("
        SELECT a.id, a.data_hora_inicio, a.data_hora_fim, a.status, a.paciente_id, a.recorrencia_id, p.nome AS paciente_nome, a.sala_reuniao_url 
        FROM agenda a 
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        WHERE a.data_hora_inicio BETWEEN ? AND ?
    ");
    log_message("12. Consulta preparada. A executar...");
    $stmt_individuais->execute([$start_date_query, $end_date_query]);
    log_message("13. Consulta de eventos individuais executada.");

    // O resto da lógica para processar os eventos...
    // (O código foi omitido por brevidade, pois o erro provavelmente ocorre antes daqui)
    $agendamentos = $stmt_individuais->fetchAll(PDO::FETCH_ASSOC);
    log_message("14. Encontrados " . count($agendamentos) . " agendamentos individuais.");

    // Se tudo correr bem, retorna uma resposta de sucesso vazia por enquanto
    log_message("SUCESSO: O script chegou ao fim sem erros fatais.");
    echo json_encode($eventos); // Retorna a lista de eventos (pode estar vazia)

} catch (Throwable $e) { // Captura qualquer tipo de erro ou exceção
    log_message("ERRO FATAL na linha " . $e->getLine() . ": " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Ocorreu um erro interno no servidor.',
        'details' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
