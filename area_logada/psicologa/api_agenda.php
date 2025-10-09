<?php
/*
Conteúdo para: area_logada/psicologa/api_agenda.php
*/
header('Content-Type: application/json');

// Adiciona um manipulador de erros para capturar detalhes
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$base_path = realpath(dirname(__FILE__) . '/../../');
require_once $base_path . '/config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'psicologa') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}
require_once $base_path . '/includes/db.php';

try {
    $pdo = conectar();
    $eventos = [];
    
    // Pega as datas da requisição ou usa o mês atual como padrão
    $start_param = $_GET['start'] ?? date('Y-m-01');
    $end_param = $_GET['end'] ?? date('Y-m-t');

    // **CORREÇÃO:** Converte as datas recebidas para o formato que o MySQL entende (YYYY-MM-DD HH:MI:SS)
    // O construtor DateTime consegue interpretar o formato com fuso horário que o FullCalendar envia.
    $start_date_query = (new DateTime($start_param))->format('Y-m-d H:i:s');
    $end_date_query = (new DateTime($end_param))->format('Y-m-d H:i:s');
    
    // Data de início para a consulta de recorrências (apenas a data é necessária)
    $start_date_sql_recorrencia = (new DateTime($start_param))->format('Y-m-d');
    
    $timezone = new DateTimeZone('America/Sao_Paulo');
    $hoje = new DateTime('now', $timezone);

    // 1. Busca eventos individuais e exceções de recorrência (eventos cancelados)
    $stmt_individuais = $pdo->prepare("
        SELECT a.id, a.data_hora_inicio, a.data_hora_fim, a.status, a.paciente_id, a.recorrencia_id, p.nome AS paciente_nome, a.sala_reuniao_url 
        FROM agenda a 
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        WHERE a.data_hora_inicio < ? AND a.data_hora_fim > ?
    ");
    // **CORREÇÃO:** Usa as datas já formatadas na execução da consulta
    $stmt_individuais->execute([$end_date_query, $start_date_query]);
    
    $excecoes = [];
    while ($agendamento = $stmt_individuais->fetch(PDO::FETCH_ASSOC)) {
        $data_agendamento = new DateTime($agendamento['data_hora_inicio'], $timezone);

        if ($agendamento['recorrencia_id'] && $agendamento['status'] === 'cancelado') {
            $data_excecao = $data_agendamento->format('Y-m-d');
            $excecoes[$data_excecao][] = $agendamento['recorrencia_id'];
            // Não pula eventos cancelados passados para manter o histórico
        }
        
        $titulo = ($agendamento['status'] === 'livre' || !$agendamento['paciente_nome']) ? 'Horário Livre' : $agendamento['paciente_nome'];
        $cor = '#3b82f6'; // Azul padrão para 'confirmado'
        if ($agendamento['status'] === 'livre') $cor = '#0d9488'; // Teal para 'livre'
        if ($agendamento['status'] === 'cancelado') $cor = '#ef4444'; // Vermelho para 'cancelado'

        $eventos[] = [
            'id' => $agendamento['id'],
            'title' => $titulo,
            'start' => $agendamento['data_hora_inicio'],
            'end' => $agendamento['data_hora_fim'],
            'color' => $cor,
            'extendedProps' => [ 
                'pacienteId' => $agendamento['paciente_id'], 
                'status' => $agendamento['status'],
                'salaUrl' => $agendamento['sala_reuniao_url']
            ]
        ];
    }

    // 2. Gera eventos a partir das regras de recorrência
    $stmt_recorrencias = $pdo->prepare("SELECT r.*, p.nome as paciente_nome FROM agenda_recorrencias r JOIN pacientes p ON r.paciente_id = p.id WHERE r.data_fim_recorrencia >= ?");
    $stmt_recorrencias->execute([$start_date_sql_recorrencia]);
    
    $start_periodo = new DateTime($start_param);
    $end_periodo = new DateTime($end_param);

    while ($regra = $stmt_recorrencias->fetch(PDO::FETCH_ASSOC)) {
        $data_inicio_regra = new DateTime($regra['data_inicio_recorrencia']);
        $data_fim_regra = new DateTime($regra['data_fim_recorrencia']);
        
        // Define o ponto de partida para o loop: o maior entre o início do período e o início da regra
        $data_atual = max($start_periodo, $data_inicio_regra);
        // Define o ponto final para o loop: o menor entre o fim do período e o fim da regra
        $data_fim_loop = min($end_periodo, $data_fim_regra);

        while ($data_atual <= $data_fim_loop) {
            $data_str = $data_atual->format('Y-m-d');
            // Verifica se o dia da semana corresponde e se não é uma exceção
            if ($data_atual->format('w') == $regra['dia_semana'] && (!isset($excecoes[$data_str]) || !in_array($regra['id'], $excecoes[$data_str]))) {
                $eventos[] = [
                    'id' => 'rec_' . $regra['id'] . '_' . $data_str,
                    'title' => $regra['paciente_nome'], // Usa o nome do paciente
                    'start' => $data_str . ' ' . $regra['hora_inicio'],
                    'end' => $data_str . ' ' . $regra['hora_fim'],
                    'color' => '#16a34a', // Verde para recorrências
                    'extendedProps' => [ 
                        'pacienteId' => $regra['paciente_id'], 
                        'status' => 'recorrente',
                        'recorrenciaId' => $regra['id']
                    ]
                ];
            }
            $data_atual->modify('+1 day');
        }
    }

    echo json_encode($eventos);

} catch (Exception $e) {
    // Se ocorrer um erro, ele será enviado como resposta
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor.', 'details' => $e->getMessage()]);
}
?>
