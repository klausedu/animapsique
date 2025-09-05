<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Painel de Controle';
require_once 'templates/header.php';

$proxima_sessao_info = null;
$sala_url = null;

try {
    $pdo = conectar();
    
    // **CORREÇÃO: Busca a URL da sala diretamente da base de dados do paciente**
    $stmt_paciente = $pdo->prepare("SELECT whereby_room_url FROM pacientes WHERE id = ?");
    $stmt_paciente->execute([$paciente_id]);
    $paciente_data = $stmt_paciente->fetch();
    if ($paciente_data) {
        $sala_url = $paciente_data['whereby_room_url'];
    }

    // Busca a próxima sessão agendada
    $stmt_agenda = $pdo->prepare("SELECT data_hora_inicio FROM agenda WHERE paciente_id = ? AND data_hora_inicio >= NOW() AND status = 'planejado' ORDER BY data_hora_inicio ASC LIMIT 1");
    $stmt_agenda->execute([$paciente_id]);
    $proxima_sessao_info = $stmt_agenda->fetch();

} catch (PDOException $e) {
    error_log("Erro no painel do paciente: " . $e->getMessage());
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Olá, <?php echo htmlspecialchars($paciente_nome); ?>!</h2>
        <p class="text-gray-600 mt-2">Este é o seu espaço seguro para acompanhar sua jornada terapêutica.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="overflow-hidden rounded-lg bg-white shadow p-5">
            <dt class="truncate text-sm font-medium text-gray-500">Sua Sala de Atendimento</dt>
            <dd class="mt-2">
                <?php if ($sala_url): ?>
                    <a href="sala_atendimento.php?room=<?php echo urlencode($sala_url); ?>" target="_blank" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">
                        Entrar na Sessão Online
                    </a>
                <?php else: ?>
                    <p class="text-gray-600">A sua sala de atendimento online ainda não está configurada.</p>
                <?php endif; ?>
            </dd>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow p-5">
            <dt class="truncate text-sm font-medium text-gray-500">Próxima Sessão Agendada</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                <?php if ($proxima_sessao_info): ?>
                    <?php 
                        $data = new DateTime($proxima_sessao_info['data_hora_inicio']);
                        echo $data->format('d/m/Y \à\s H:i');
                    ?>
                <?php else: ?>
                    Nenhuma sessão agendada
                <?php endif; ?>
            </dd>
            <div class="mt-4">
                <a href="agenda.php" class="font-medium text-teal-700 hover:text-teal-900">Ver agenda completa &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
