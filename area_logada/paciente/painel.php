<?php
// MODO DE DIAGNÓSTICO ATIVADO
echo '<pre style="background: #f1f1f1; padding: 15px; border: 1px solid #ccc; margin: 20px; font-family: monospace;">';
echo "<strong>INÍCIO DO DIAGNÓSTICO DA AGENDA</strong><br>";

require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Painel do Paciente';
// O header é incluído no final para que a saída de diagnóstico apareça no topo.

$paciente_id = $_SESSION['user_id'];
$proxima_sessao = null;

try {
    $pdo = conectar();
    $hoje = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
    echo "Data e Hora Atuais: " . $hoje->format('Y-m-d H:i:s') . "<br><br>";

    $sessoes_candidatas = [];

    // --- 1. Busca por sessões INDIVIDUAIS ---
    echo "<strong>--- A procurar por sessões individuais... ---</strong><br>";
    $stmt_individual = $pdo->prepare(
        "SELECT data_hora_inicio FROM agenda 
         WHERE paciente_id = ? AND data_hora_inicio >= NOW() AND status IN ('planejado', 'confirmado') 
         ORDER BY data_hora_inicio ASC LIMIT 1"
    );
    $stmt_individual->execute([$paciente_id]);
    $sessao_individual = $stmt_individual->fetch();
    if ($sessao_individual) {
        echo "Sessão individual encontrada: " . $sessao_individual['data_hora_inicio'] . "<br>";
        $sessoes_candidatas[] = new DateTime($sessao_individual['data_hora_inicio']);
    } else {
        echo "Nenhuma sessão individual futura encontrada.<br>";
    }

    // --- 2. Busca por sessões RECORRENTES ---
    echo "<br><strong>--- A procurar por regras de recorrência... ---</strong><br>";
    $stmt_recorrencias = $pdo->prepare(
        "SELECT * FROM agenda_recorrencias 
         WHERE paciente_id = ? AND data_fim_recorrencia >= CURDATE()"
    );
    $stmt_recorrencias->execute([$paciente_id]);
    $recorrencias = $stmt_recorrencias->fetchAll();

    if (empty($recorrencias)) {
        echo "Nenhuma regra de recorrência ativa encontrada para este paciente.<br>";
    }

    foreach ($recorrencias as $regra) {
        echo "<hr>A analisar regra de recorrência ID #" . $regra['id'] . ":<br>";
        echo "   - Dia da semana: " . $regra['dia_semana'] . " (0=Dom, 1=Seg, ...)<br>";
        echo "   - Hora: " . $regra['hora_inicio'] . " até " . $regra['hora_fim'] . "<br>";
        echo "   - Válida de " . $regra['data_inicio_recorrencia'] . " até " . $regra['data_fim_recorrencia'] . "<br>";

        $data_inicio_regra = new DateTime($regra['data_inicio_recorrencia']);
        $data_fim_regra = new DateTime($regra['data_fim_recorrencia']);
        $data_atual = max(new DateTime('today'), $data_inicio_regra);
        
        echo "   - A procurar a partir de: " . $data_atual->format('Y-m-d') . "<br>";

        while ($data_atual <= $data_fim_regra) {
            if ($data_atual->format('w') == $regra['dia_semana']) {
                $data_hora_ocorrencia = new DateTime($data_atual->format('Y-m-d') . ' ' . $regra['hora_inicio'], new DateTimeZone('America/Sao_Paulo'));
                echo "   -> Potencial ocorrência encontrada em: " . $data_hora_ocorrencia->format('Y-m-d H:i:s') . "<br>";

                if ($data_hora_ocorrencia > $hoje) {
                    echo "      - Esta data é no futuro. A verificar cancelamentos...<br>";
                    $stmt_excecao = $pdo->prepare("SELECT id FROM agenda WHERE recorrencia_id = ? AND DATE(data_hora_inicio) = ? AND status = 'cancelado'");
                    $stmt_excecao->execute([$regra['id'], $data_atual->format('Y-m-d')]);

                    if ($stmt_excecao->fetch() === false) {
                        echo "      - NENHUM cancelamento encontrado. Esta é uma data válida!<br>";
                        $sessoes_candidatas[] = $data_hora_ocorrencia;
                        break; 
                    } else {
                        echo "      - Esta ocorrência foi CANCELADA. A procurar na próxima semana...<br>";
                    }
                } else {
                     echo "      - Esta ocorrência já passou hoje. A ignorar.<br>";
                }
            }
            $data_atual->modify('+1 day');
        }
    }

    // --- 3. Encontra a data mais próxima ---
    echo "<br><strong>--- A calcular a sessão mais próxima... ---</strong><br>";
    echo "Lista de todas as sessões candidatas encontradas:<br>";
    print_r($sessoes_candidatas);

    if (!empty($sessoes_candidatas)) {
        sort($sessoes_candidatas);
        $proxima_sessao = $sessoes_candidatas[0];
        echo "<br><strong>Sessão mais próxima calculada: " . $proxima_sessao->format('Y-m-d H:i:s') . "</strong><br>";
    } else {
        echo "<br><strong>Nenhuma sessão futura encontrada após todos os cálculos.</strong><br>";
    }

} catch (Throwable $e) {
    echo "<strong>ERRO FATAL DURANTE O DIAGNÓSTICO:</strong> " . $e->getMessage();
}
echo "</pre>";

// Inclui o HTML da página depois do bloco de diagnóstico
require_once 'templates/header.php';
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
