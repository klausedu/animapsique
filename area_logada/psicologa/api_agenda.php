<?php
// Versão Definitiva - Corrigida em 2024-10-09 para remover a coluna 'sala_reuniao_url'.
// Se este erro persistir, o problema é o cache do servidor.

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$base_path = realpath(dirname(__FILE__) . '/../../');

require_once $base_path . '/config.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'psicologa') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}
require_once $base_path . '/includes/db.php';

try {
    $pdo = conectar();
    $eventos = [];
    
    $start_param = $_GET['start'] ?? date('Y-m-01');
    $end_param = $_GET['end'] ?? date('Y-m-t');

    $start_date_query = (new DateTime($start_param))->format('Y-m-d H:i:s');
    $end_date_query = (new DateTime($end_param))->format('Y-m-d H:i:s');
    
    // A consulta foi corrigida para não incluir a coluna 'a.sala_reuniao_url'
    $stmt_individuais = $pdo->prepare("
        SELECT a.id, a.data_hora_inicio, a.data_hora_fim, a.status, a.paciente_id, a.recorrencia_id, p.nome AS paciente_nome
        FROM agenda a 
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        WHERE a.data_hora_inicio < ? AND a.data_hora_fim > ?
    ");
    $stmt_individuais->execute([$end_date_query, $start_date_query]);
    
    // O resto do código processa os resultados... (está correto)
    $excecoes = [];
    while ($agendamento = $stmt_individuais->fetch(PDO::FETCH_ASSOC)) {
        if ($agendamento['recorrencia_id'] && $agendamento['status'] === 'cancelado') {
            $data_excecao = (new DateTime($agendamento['data_hora_inicio']))->format('Y-m-d');
            if (!isset($excecoes[$data_excecao])) { $excecoes[$data_excecao] = []; }
            $excecoes[$data_excecao][] = $agendamento['recorrencia_id'];
        }
        $titulo = ($agendamento['status'] === 'livre' || !$agendamento['paciente_nome']) ? 'Horário Livre' : $agendamento['paciente_nome'];
        $cor = '#3b82f6';
        if ($agendamento['status'] === 'livre') $cor = '#0d9488';
        if ($agendamento['status'] === 'cancelado') $cor = '#ef4444';
        $eventos[] = [
            'id' => $agendamento['id'], 'title' => $titulo, 'start' => $agendamento['data_hora_inicio'],
            'end' => $agendamento['data_hora_fim'], 'color' => $cor,
            'extendedProps' => [ 'pacienteId' => $agendamento['paciente_id'], 'status' => $agendamento['status'] ]
        ];
    }

    $start_date_sql_recorrencia = (new DateTime($start_param))->format('Y-m-d');
    $stmt_recorrencias = $pdo->prepare("SELECT r.*, p.nome as paciente_nome FROM agenda_recorrencias r JOIN pacientes p ON r.paciente_id = p.id WHERE r.data_fim_recorrencia >= ?");
    $stmt_recorrencias->execute([$start_date_sql_recorrencia]);
    $start_periodo = new DateTime($start_param);
    $end_periodo = new DateTime($end_param);
    while ($regra = $stmt_recorrencias->fetch(PDO::FETCH_ASSOC)) {
        $data_inicio_regra = new DateTime($regra['data_inicio_recorrencia']);
        $data_fim_regra = new DateTime($regra['data_fim_recorrencia']);
        $data_atual = max($start_periodo, $data_inicio_regra);
        $data_fim_loop = min($end_periodo, $data_fim_regra);
        while ($data_atual <= $data_fim_loop) {
            $data_str = $data_atual->format('Y-m-d');
            if ($data_atual->format('w') == $regra['dia_semana'] && (!isset($excecoes[$data_str]) || !in_array($regra['id'], $excecoes[$data_str]))) {
                $eventos[] = [
                    'id' => 'rec_' . $regra['id'] . '_' . $data_str, 'title' => $regra['paciente_nome'],
                    'start' => $data_str . ' ' . $regra['hora_inicio'], 'end' => $data_str . ' ' . $regra['hora_fim'],
                    'color' => '#16a34a',
                    'extendedProps' => [ 'pacienteId' => $regra['paciente_id'], 'status' => 'recorrente', 'recorrenciaId' => $regra['id'] ]
                ];
            }
            $data_atual->modify('+1 day');
        }
    }
    echo json_encode($eventos);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor.', 'details' => $e->getMessage()]);
}
?>
