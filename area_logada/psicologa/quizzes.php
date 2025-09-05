<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$page_title = 'Gerenciar Quizzes';
require_once 'templates/header.php';

try {
    $pdo = conectar();
    // Busca todos os quizzes criados para listagem
    $stmt = $pdo->query("SELECT id, titulo, descricao, data_criacao FROM quizzes ORDER BY data_criacao DESC");
    $quizzes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar quizzes: " . $e->getMessage());
    $quizzes = [];
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Botão para criar novo quiz -->
    <div class="text-right mb-6">
        <a href="criar_quiz.php" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
            Criar Novo Quiz
        </a>
    </div>

    <!-- Lista de Quizzes Criados -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Quizzes Criados</h2>
        <div class="space-y-4">
            <?php if (empty($quizzes)): ?>
                <p class="text-gray-500">Nenhum quiz foi criado ainda. Clique no botão acima para começar.</p>
            <?php else: ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="p-4 border rounded-md flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($quiz['titulo']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($quiz['descricao']); ?></p>
                            <p class="text-xs text-gray-400 mt-1">Criado em: <?php echo (new DateTime($quiz['data_criacao']))->format('d/m/Y'); ?></p>
                        </div>
                        <div>
                            <a href="atribuir_quiz.php?id=<?php echo $quiz['id']; ?>" class="text-sm font-medium text-teal-600 hover:text-teal-800 mr-4">Atribuir a Pacientes</a>
                            <a href="ver_respostas_quiz.php?id=<?php echo $quiz['id']; ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Ver Respostas</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
