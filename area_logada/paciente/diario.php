<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Meu Diário';
require_once 'templates/header.php';

try {
    $pdo = conectar();
    // Busca todas as entradas do diário do paciente logado
    $stmt = $pdo->prepare("SELECT id, titulo, texto, data_criacao FROM diario WHERE paciente_id = ? ORDER BY data_criacao DESC");
    $stmt->execute([$paciente_id]);
    $entradas_diario = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar entradas do diário: " . $e->getMessage());
    $entradas_diario = [];
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Seção para Nova Entrada no Diário -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Nova Entrada no Diário</h2>

        <!-- Exibe mensagens de sucesso ou erro -->
        <?php if (isset($_SESSION['diario_success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['diario_success']; unset($_SESSION['diario_success']); ?></p>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['diario_error'])): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['diario_error']; unset($_SESSION['diario_error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="processa_diario.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título (opcional)</label>
                    <input type="text" name="titulo" id="titulo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div>
                    <label for="texto" class="block text-sm font-medium text-gray-700">Texto</label>
                    <textarea id="texto" name="texto" rows="8" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Escreva seus pensamentos e sentimentos aqui..."></textarea>
                </div>
            </div>
            <div class="mt-4 text-right">
                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                    Salvar Entrada
                </button>
            </div>
        </form>
    </div>

    <!-- Histórico de Entradas -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Minhas Entradas Anteriores</h2>
        <div class="space-y-6">
            <?php if (empty($entradas_diario)): ?>
                <p class="text-gray-500">Nenhuma entrada no diário encontrada.</p>
            <?php else: ?>
                <?php foreach ($entradas_diario as $entrada): ?>
                    <div class="border-l-4 border-teal-500 pl-4">
                        <p class="text-sm font-semibold text-gray-500">
                            <?php echo (new DateTime($entrada['data_criacao']))->format('d/m/Y \à\s H:i'); ?>
                        </p>
                        <?php if (!empty($entrada['titulo'])): ?>
                            <h3 class="text-lg font-bold text-gray-800 mt-1"><?php echo htmlspecialchars($entrada['titulo']); ?></h3>
                        <?php endif; ?>
                        <div class="mt-2 text-gray-700 prose max-w-none">
                            <?php echo nl2br(htmlspecialchars($entrada['texto'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
