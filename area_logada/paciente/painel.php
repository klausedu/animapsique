<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Painel de Controle';
require_once 'templates/header.php';

try {
    $pdo = conectar();
    
    // Buscar próxima sessão agendada
    $stmt_agenda = $pdo->prepare(
        "SELECT data_hora_inicio FROM agenda WHERE paciente_id = ? AND data_hora_inicio >= NOW() AND status = 'planejado' ORDER BY data_hora_inicio ASC LIMIT 1"
    );
    $stmt_agenda->execute([$paciente_id]);
    $proxima_sessao = $stmt_agenda->fetchColumn();

    // Contar mensagens não lidas
    $stmt_msgs = $pdo->prepare(
        "SELECT COUNT(*) FROM mensagens_status WHERE usuario_id = ? AND tipo_usuario = 'paciente' AND lida = 0"
    );
    $stmt_msgs->execute([$paciente_id]);
    $mensagens_nao_lidas = $stmt_msgs->fetchColumn();

} catch (PDOException $e) {
    error_log("Erro no painel do paciente: " . $e->getMessage());
    $proxima_sessao = null;
    $mensagens_nao_lidas = 0;
}

?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Mensagem de boas-vindas -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Olá, <?php echo htmlspecialchars($paciente_nome); ?>!</h2>
        <p class="text-gray-600 mt-2">Este é o seu espaço seguro para acompanhar sua jornada terapêutica.</p>
    </div>

    <!-- Grid de cartões de resumo -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <!-- Card: Próxima Sessão -->
        <div class="overflow-hidden rounded-lg bg-white shadow p-5">
            <dt class="truncate text-sm font-medium text-gray-500">Próxima Sessão</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                <?php if ($proxima_sessao): ?>
                    <?php 
                        $data = new DateTime($proxima_sessao);
                        echo $data->format('d/m/Y \à\s H:i');
                    ?>
                <?php else: ?>
                    Nenhuma sessão agendada
                <?php endif; ?>
            </dd>
            <div class="mt-4">
                <a href="agenda.php" class="font-medium text-teal-700 hover:text-teal-900">Ver minha agenda completa &rarr;</a>
            </div>
        </div>

        <!-- Card: Mensagens Novas -->
        <div class="overflow-hidden rounded-lg bg-white shadow p-5">
            <dt class="truncate text-sm font-medium text-gray-500">Mensagens Novas</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                <?php echo $mensagens_nao_lidas; ?>
            </dd>
            <div class="mt-4">
                 <a href="mensagens.php" class="font-medium text-teal-700 hover:text-teal-900">Acessar caixa de entrada &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
