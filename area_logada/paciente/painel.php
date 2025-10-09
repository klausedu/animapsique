<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Painel do Paciente';
require_once 'templates/header.php';

$paciente_id = $_SESSION['user_id'];
$proxima_sessao = null;

try {
    $pdo = conectar();
    $hoje = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
    $sessoes_candidatas = [];

    // --- 1. Busca pela próxima sessão INDIVIDUAL no futuro ---
    $stmt_individual = $pdo->prepare(
        "SELECT data_hora_inicio FROM agenda 
         WHERE paciente_id = ? AND data_hora_inicio >= NOW() AND status IN ('planejado', 'confirmado') 
         ORDER BY data_hora_inicio ASC LIMIT 1"
    );
    $stmt_individual->execute([$paciente_id]);
    $sessao_individual = $stmt_individual->fetch();
    if ($sessao_individual) {
        $sessoes_candidatas[] = new DateTime($sessao_individual['data_hora_inicio']);
    }

    // --- 2. Busca por regras de sessões RECORRENTES ---
    $stmt_recorrencias = $pdo->prepare(
        "SELECT * FROM agenda_recorrencias 
         WHERE paciente_id = ? AND data_fim_recorrencia >= CURDATE()"
    );
    $stmt_recorrencias->execute([$paciente_id]);
    $recorrencias = $stmt_recorrencias->fetchAll();

    foreach ($recorrencias as $regra) {
        $data_inicio_regra = new DateTime($regra['data_inicio_recorrencia']);
        $data_fim_regra = new DateTime($regra['data_fim_recorrencia']);
        
        // Começa a procurar a partir de hoje ou da data de início da regra, o que for mais recente.
        $data_atual = max(new DateTime('today'), $data_inicio_regra);

        // Loop para encontrar a primeira ocorrência válida
        while ($data_atual <= $data_fim_regra) {
            // Se o dia da semana corresponder à regra...
            if ($data_atual->format('w') == $regra['dia_semana']) {
                $data_hora_ocorrencia = new DateTime(
                    $data_atual->format('Y-m-d') . ' ' . $regra['hora_inicio'],
                    new DateTimeZone('America/Sao_Paulo')
                );

                // ... e se a ocorrência ainda não passou hoje...
                if ($data_hora_ocorrencia > $hoje) {
                    // ... verifica se não há um cancelamento para esta data.
                    $stmt_excecao = $pdo->prepare(
                        "SELECT id FROM agenda WHERE recorrencia_id = ? AND DATE(data_hora_inicio) = ? AND status = 'cancelado'"
                    );
                    $stmt_excecao->execute([$regra['id'], $data_atual->format('Y-m-d')]);

                    if ($stmt_excecao->fetch() === false) {
                        // Não há cancelamento, então esta é a próxima sessão válida para esta regra.
                        $sessoes_candidatas[] = $data_hora_ocorrencia;
                        // Para de procurar para esta regra, pois já encontrámos a mais próxima.
                        break; 
                    }
                }
            }
            // Avança para o dia seguinte.
            $data_atual->modify('+1 day');
        }
    }

    // --- 3. Encontra a data mais próxima de todas as candidatas ---
    if (!empty($sessoes_candidatas)) {
        sort($sessoes_candidatas); // Ordena todas as datas encontradas
        $proxima_sessao = $sessoes_candidatas[0]; // A primeira da lista é a mais próxima
    }

} catch (Throwable $e) {
    // Para depuração, podemos registar o erro
    error_log("Erro no painel do paciente ao buscar sessão: " . $e->getMessage());
}
?>

<main class="flex-grow">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['user_nome']); ?>!</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md col-span-1 md:col-span-2">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Próxima Sessão</h2>
                <?php if ($proxima_sessao): ?>
                    <p class="text-2xl text-teal-600 font-bold">
                        <?php 
                            $formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'America/Sao_Paulo', IntlDateFormatter::GREGORIAN, "EEEE, d 'de' MMMM 'de' yyyy");
                            echo $formatter->format($proxima_sessao);
                        ?>
                    </p>
                    <p class="text-xl text-gray-800 font-medium">
                        às <?php echo $proxima_sessao->format('H:i'); ?>
                    </p>
                    <a href="sala_atendimento" class="mt-4 inline-block bg-teal-600 text-white font-bold py-2 px-4 rounded hover:bg-teal-700 transition duration-300">
                        Entrar na Sala de Atendimento
                    </a>
                <?php else: ?>
                    <p class="text-gray-600">Nenhuma sessão agendada</p>
                <?php endif; ?>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Acesso Rápido</h2>
                <ul class="space-y-2">
                    <li><a href="agenda" class="text-blue-600 hover:underline">Minha Agenda</a></li>
                    <li><a href="mensagens" class="text-blue-600 hover:underline">Mensagens</a></li>
                    <li><a href="diario" class="text-blue-600 hover:underline">Meu Diário</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?>
