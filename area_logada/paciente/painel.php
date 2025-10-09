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
    $hoje = new DateTime();

    // 1. Encontra a próxima sessão individual
    $stmt_individual = $pdo->prepare(
        "SELECT data_hora_inicio FROM agenda 
         WHERE paciente_id = ? AND data_hora_inicio >= NOW() AND status IN ('planejado', 'confirmado') 
         ORDER BY data_hora_inicio ASC LIMIT 1"
    );
    $stmt_individual->execute([$paciente_id]);
    $sessao_individual = $stmt_individual->fetch();
    $proxima_data_individual = $sessao_individual ? new DateTime($sessao_individual['data_hora_inicio']) : null;

    // 2. Encontra a próxima sessão recorrente
    $stmt_recorrencias = $pdo->prepare(
        "SELECT * FROM agenda_recorrencias 
         WHERE paciente_id = ? AND data_fim_recorrencia >= CURDATE()"
    );
    $stmt_recorrencias->execute([$paciente_id]);
    $recorrencias = $stmt_recorrencias->fetchAll();
    
    $proxima_data_recorrente = null;

    foreach ($recorrencias as $regra) {
        $data_inicio_regra = new DateTime($regra['data_inicio_recorrencia']);
        $data_fim_regra = new DateTime($regra['data_fim_recorrencia']);
        $dia_semana_regra = $regra['dia_semana']; // 0=Domingo, 1=Segunda, ...

        // Começa a procurar a partir de hoje
        $data_candidata = clone $hoje;

        // Se a regra só começa no futuro, começa a procurar a partir daí
        if ($data_candidata < $data_inicio_regra) {
            $data_candidata = $data_inicio_regra;
        }

        // Procura a próxima data válida que corresponda ao dia da semana
        while ($data_candidata->format('w') != $dia_semana_regra) {
            $data_candidata->modify('+1 day');
        }

        // Loop para encontrar a primeira ocorrência válida que não foi cancelada
        while ($data_candidata <= $data_fim_regra) {
            $data_formatada = $data_candidata->format('Y-m-d');
            
            // Verifica se existe uma exceção (cancelamento) para esta data
            $stmt_excecao = $pdo->prepare(
                "SELECT id FROM agenda 
                 WHERE recorrencia_id = ? AND DATE(data_hora_inicio) = ? AND status = 'cancelado'"
            );
            $stmt_excecao->execute([$regra['id'], $data_formatada]);
            
            if ($stmt_excecao->fetch() === false) {
                // Não é uma exceção, então esta é uma data válida
                $data_hora_recorrente = new DateTime($data_formatada . ' ' . $regra['hora_inicio']);
                
                // Se for hoje, mas a hora já passou, continua para a próxima semana
                if ($data_hora_recorrente < $hoje) {
                    $data_candidata->modify('+1 week');
                    continue;
                }

                // Encontrámos a próxima ocorrência válida. Compara com a melhor que já temos.
                if ($proxima_data_recorrente === null || $data_hora_recorrente < $proxima_data_recorrente) {
                    $proxima_data_recorrente = $data_hora_recorrente;
                }
                break; // Para de procurar para esta regra, pois já encontrámos a mais próxima
            }
            
            // Se foi uma exceção, avança para a próxima semana
            $data_candidata->modify('+1 week');
        }
    }

    // 3. Compara a sessão individual com a recorrente e escolhe a mais próxima
    if ($proxima_data_individual && $proxima_data_recorrente) {
        $proxima_sessao = ($proxima_data_individual < $proxima_data_recorrente) ? $proxima_data_individual : $proxima_data_recorrente;
    } elseif ($proxima_data_individual) {
        $proxima_sessao = $proxima_data_individual;
    } else {
        $proxima_sessao = $proxima_data_recorrente;
    }

} catch (PDOException $e) {
    // Registar o erro para depuração
    error_log("Erro ao buscar próxima sessão: " . $e->getMessage());
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
                        <?php echo $proxima_sessao->format('d/m/Y \à\s H:i'); ?>
                    </p>
                    <a href="sala_atendimento.php" class="mt-4 inline-block bg-teal-600 text-white font-bold py-2 px-4 rounded hover:bg-teal-700 transition duration-300">
                        Entrar na Sala de Atendimento
                    </a>
                <?php else: ?>
                    <p class="text-gray-600">Nenhuma sessão agendada</p>
                <?php endif; ?>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Acesso Rápido</h2>
                <ul class="space-y-2">
                    <li><a href="agenda.php" class="text-blue-600 hover:underline">Minha Agenda</a></li>
                    <li><a href="mensagens.php" class="text-blue-600 hover:underline">Mensagens</a></li>
                    <li><a href="diario.php" class="text-blue-600 hover:underline">Meu Diário</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer.php'; ?>
