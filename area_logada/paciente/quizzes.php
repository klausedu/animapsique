<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$page_title = 'Meus Quizzes';
require_once 'templates/header.php';

try {
    $pdo = conectar();

    // Busca todos os quizzes atribuídos a este paciente
    $sql = "
        SELECT 
            qa.id AS atribuicao_id,
            q.titulo,
            q.descricao,
            qa.status,
            qa.data_atribuicao,
            qa.data_resposta
        FROM quiz_atribuicoes qa
        JOIN quizzes q ON qa.quiz_id = q.id
        WHERE qa.paciente_id = ?
        ORDER BY qa.data_atribuicao DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paciente_id]);
    $quizzes = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao buscar quizzes do paciente: " . $e->getMessage());
    $quizzes = [];
}
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Quizzes Atribuídos</h2>
        <div class="space-y-4">
            <?php if (empty($quizzes)): ?>
                <p class="text-gray-500">Você não tem nenhum quiz pendente ou respondido no momento.</p>
            <?php else: ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="p-4 border rounded-md flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($quiz['titulo']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($quiz['descricao']); ?></p>
                            <p class="text-xs text-gray-400 mt-1">
                                Atribuído em: <?php echo (new DateTime($quiz['data_atribuicao']))->format('d/m/Y'); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <?php if ($quiz['status'] === 'pendente'): ?>
                                <a href="responder_quiz.php?id=<?php echo $quiz['atribuicao_id']; ?>" class="inline-flex items-center rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">
                                    Responder Agora
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-md bg-green-100 px-3 py-2 text-sm font-medium text-green-700">
                                    Respondido em <?php echo (new DateTime($quiz['data_resposta']))->format('d/m/Y'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
