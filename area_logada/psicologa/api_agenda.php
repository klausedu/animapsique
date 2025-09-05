<?php
/*
Conteúdo para: area_logada/psicologa/api_agenda.php
*/
header('Content-Type: application/json');
$base_path = realpath(dirname(__FILE__) . '/../../');
require_once $base_path . '/config.php';
if (!isset($_SESSION['logged_in'])) { http_response_code(401); exit; }
require_once $base_path . '/includes/db.php';

try {
    $pdo = conectar();
    $eventos = [];
    $start_date_str = $_GET['start'] ?? date('Y-m-01');
    $end_date_str = $_GET['end'] ?? date('Y-m-t');
    $start_date_sql = (new DateTime($start_date_str))->format('Y-m-d');
    $timezone = new DateTimeZone('America/Sao_Paulo');
    $hoje = new DateTime('now', $timezone);

    // 1. Busca eventos individuais e exceções de recorrência (eventos cancelados)
    $stmt_individuais = $pdo->prepare("
        SELECT a.id, a.data_hora_inicio, a.data_hora_fim, a.status, a.paciente_id, a.recorrencia_id, p.nome AS paciente_nome 
        FROM agenda a 
        LEFT JOIN pacientes p ON a.paciente_id = p.id
        WHERE a.data_hora_inicio BETWEEN ? AND ?
    ");
    $stmt_individuais->execute([$start_date_str, $end_date_str]);
    
    $excecoes = [];
    while ($agendamento = $stmt_individuais->fetch(PDO::FETCH_ASSOC)) {
        $data_agendamento = new DateTime($agendamento['data_hora_inicio'], $timezone);

        // Se for uma exceção, guarda-a para não gerar o evento recorrente
        if ($agendamento['recorrencia_id'] && $agendamento['status'] === 'cancelado') {
            $data_excecao = $data_agendamento->format('Y-m-d');
            $excecoes[$data_excecao][] = $agendamento['recorrencia_id'];

            // LÓGICA ATUALIZADA: Só mostra o evento cancelado se for do passado.
            // Se for do futuro, a exceção apenas impede a criação do evento recorrente, tornando-o invisível.
            if ($data_agendamento >= $hoje) {
                continue; // Pula a adição deste evento futuro cancelado à lista
            }
        }
        
        // Adiciona o evento individual à lista (pode ser um evento normal ou uma exceção visível do passado)
        $titulo = $agendamento['status'] === 'livre' ? 'Horário Livre' : $agendamento['paciente_nome'];
        $cor = '#3b82f6'; // Cor padrão
        if ($agendamento['status'] === 'livre') $cor = '#0d9488';
        if ($agendamento['status'] === 'cancelado') $cor = '#ef4444';

        $eventos[] = [
            'id' => $agendamento['id'],
            'title' => $titulo,
            'start' => $agendamento['data_hora_inicio'],
            'end' => $agendamento['data_hora_fim'],
            'color' => $cor,
            'extendedProps' => [ 'pacienteId' => $agendamento['paciente_id'], 'status' => $agendamento['status'] ]
        ];
    }

    // 2. Gera eventos a partir das regras de recorrência
    $stmt_recorrencias = $pdo->prepare("SELECT * FROM agenda_recorrencias WHERE data_fim_recorrencia >= ?");
    $stmt_recorrencias->execute([$start_date_sql]);
    
    while ($regra = $stmt_recorrencias->fetch(PDO::FETCH_ASSOC)) {
        $data_atual = new DateTime($regra['data_inicio_recorrencia'], $timezone);
        $data_fim = new DateTime($regra['data_fim_recorrencia'], $timezone);

        while ($data_atual <= $data_fim) {
            $data_str = $data_atual->format('Y-m-d');
            // Verifica se o dia da semana corresponde E se não há uma exceção para este dia/regra
            if ($data_atual->format('w') == $regra['dia_semana'] && (!isset($excecoes[$data_str]) || !in_array($regra['id'], $excecoes[$data_str]))) {
                $eventos[] = [
                    'id' => 'rec_' . $regra['id'] . '_' . $data_str,
                    'title' => $regra['titulo'],
                    'start' => $data_str . ' ' . $regra['hora_inicio'],
                    'end' => $data_str . ' ' . $regra['hora_fim'],
                    'color' => '#16a34a',
                    'extendedProps' => [ 'pacienteId' => $regra['paciente_id'], 'status' => 'planejado' ]
                ];
            }
            $data_atual->modify('+1 day');
        }
    }

    echo json_encode($eventos);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
