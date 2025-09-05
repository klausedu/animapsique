<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Valida o ID do quiz recebido pela URL
$quiz_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$quiz_id) {
    header('Location: quizzes.php');
    exit;
}

try {
    $pdo = conectar();

    // Busca os detalhes do quiz para exibir o título
    $stmt_quiz = $pdo->prepare("SELECT titulo FROM quizzes WHERE id = ?");
    $stmt_quiz->execute([$quiz_id]);
    $quiz = $stmt_quiz->fetch();

    if (!$quiz) {
        // Se o quiz não existe, volta para a lista
        header('Location: quizzes.php');
        exit;
    }

    // Busca todos os pacientes ativos para a lista de seleção
    $stmt_pacientes = $pdo->query("SELECT id, nome FROM pacientes WHERE ativo = 1 ORDER BY nome ASC");
    $pacientes = $stmt_pacientes->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao carregar dados para atribuir quiz: " . $e->getMessage());
    // Lidar com o erro de forma apropriada
    $pacientes = [];
}

$page_title = 'Atribuir Quiz: ' . htmlspecialchars($quiz['titulo']);
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <form action="processa_atribuicao_quiz.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Atribuir Quiz</h2>
                    <p class="text-gray-600 mt-1">Selecione os pacientes que devem responder ao quiz "<strong><?php echo htmlspecialchars($quiz['titulo']); ?></strong>".</p>
                </div>
                <a href="quizzes.php" class="text-sm text-teal-600 hover:text-teal-800">&larr; Voltar para a lista de quizzes</a>
            </div>

            <!-- Exibe mensagens de sucesso ou erro -->
            <?php if (isset($_SESSION['atribuicao_success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['atribuicao_success']; unset($_SESSION['atribuicao_success']); ?></p>
            </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['atribuicao_error'])): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['atribuicao_error']; unset($_SESSION['atribuicao_error']); ?></p>
            </div>
            <?php endif; ?>

            <!-- Lista de Pacientes -->
            <div class="space-y-4 border-t border-b py-4">
                <?php if (empty($pacientes)): ?>
                    <p class="text-gray-500">Não há pacientes ativos para atribuir o quiz.</p>
                <?php else: ?>
                    <?php foreach ($pacientes as $paciente): ?>
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input id="paciente_<?php echo $paciente['id']; ?>" name="pacientes_ids[]" value="<?php echo $paciente['id']; ?>" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-600">
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="paciente_<?php echo $paciente['id']; ?>" class="font-medium text-gray-900"><?php echo htmlspecialchars($paciente['nome']); ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700" <?php if (empty($pacientes)) echo 'disabled'; ?>>
                    Atribuir Quiz aos Selecionados
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
