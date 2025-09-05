<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$quiz_id) {
    header('Location: quizzes.php');
    exit;
}

try {
    $pdo = conectar();
    // Busca o título do quiz
    $stmt_quiz = $pdo->prepare("SELECT titulo FROM quizzes WHERE id = ?");
    $stmt_quiz->execute([$quiz_id]);
    $quiz = $stmt_quiz->fetch();
    if (!$quiz) {
        header('Location: quizzes.php');
        exit;
    }

    // Busca todas as atribuições para este quiz, com os nomes dos pacientes
    $sql = "
        SELECT 
            qa.id AS atribuicao_id,
            p.nome AS paciente_nome,
            qa.status,
            qa.data_atribuicao,
            qa.data_resposta
        FROM quiz_atribuicoes qa
        JOIN pacientes p ON qa.paciente_id = p.id
        WHERE qa.quiz_id = ?
        ORDER BY p.nome ASC
    ";
    $stmt_atribuicoes = $pdo->prepare($sql);
    $stmt_atribuicoes->execute([$quiz_id]);
    $atribuicoes = $stmt_atribuicoes->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao buscar respostas do quiz: " . $e->getMessage());
    $atribuicoes = [];
}

$page_title = 'Respostas para: ' . htmlspecialchars($quiz['titulo']);
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($page_title); ?></h2>
                <p class="text-gray-600 mt-1">Veja abaixo o status de resposta de cada paciente.</p>
            </div>
            <a href="quizzes.php" class="text-sm text-teal-600 hover:text-teal-800">&larr; Voltar para a lista de quizzes</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data da Resposta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($atribuicoes as $atribuicao): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($atribuicao['paciente_nome']); ?></td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($atribuicao['status'] === 'respondido'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Respondido</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo $atribuicao['data_resposta'] ? (new DateTime($atribuicao['data_resposta']))->format('d/m/Y') : 'N/A'; ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <?php if ($atribuicao['status'] === 'respondido'): ?>
                                    <a href="visualizar_resposta.php?id=<?php echo $atribuicao['atribuicao_id']; ?>" class="text-indigo-600 hover:text-indigo-900">Visualizar Respostas</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
