<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$page_title = 'Salas de Atendimento';
require_once 'templates/header.php';

try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, nome, whereby_room_url FROM pacientes WHERE ativo = 1 ORDER BY nome ASC");
    $pacientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $pacientes = [];
    $error_message = "Erro ao buscar pacientes: " . $e->getMessage();
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Salas de Atendimento Virtuais</h2>

        <?php if (isset($error_message)): ?>
            <p class="text-red-500"><?php echo htmlspecialchars($error_message); ?></p>
        <?php elseif (empty($pacientes)): ?>
            <p class="text-gray-600">Nenhum paciente ativo encontrado.</p>
        <?php else: ?>
            <ul role="list" class="divide-y divide-gray-200">
                <?php foreach ($pacientes as $paciente): ?>
                    <li class="flex justify-between gap-x-6 py-5">
                        <div class="flex min-w-0 gap-x-4">
                            <div class="min-w-0 flex-auto">
                                <p class="text-lg font-semibold leading-6 text-gray-900"><?php echo htmlspecialchars($paciente['nome']); ?></p>
                            </div>
                        </div>
                        <div class="shrink-0 sm:flex sm:flex-col sm:items-end">
                            <?php if (!empty($paciente['whereby_room_url'])): ?>
                                <a href="<?php echo htmlspecialchars($paciente['whereby_room_url']); ?>" target="_blank" class="inline-flex items-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                                    Entrar na Sala
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-600">
                                    Sala não disponível
                                </span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
