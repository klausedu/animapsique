<?php
require_once '../../config.php';
require_once '../../includes/auth_paciente.php';
require_once '../../includes/db.php';

$atribuicao_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$atribuicao_id) {
    header('Location: quizzes.php');
    exit;
}

try {
    $pdo = conectar();
    // Busca os dados do quiz e verifica se ele pertence ao paciente logado e está pendente
    $sql = "
        SELECT q.titulo, q.descricao, qa.quiz_id
        FROM quiz_atribuicoes qa
        JOIN quizzes q ON qa.quiz_id = q.id
        WHERE qa.id = ? AND qa.paciente_id = ? AND qa.status = 'pendente'
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$atribuicao_id, $paciente_id]);
    $quiz = $stmt->fetch();

    if (!$quiz) {
        // Se não encontrar, ou já foi respondido, ou não pertence ao paciente
        header('Location: quizzes.php');
        exit;
    }

    // Busca as perguntas e opções do quiz
    $stmt_perguntas = $pdo->prepare("SELECT id, texto_pergunta, tipo FROM quiz_perguntas WHERE quiz_id = ? ORDER BY ordem ASC");
    $stmt_perguntas->execute([$quiz['quiz_id']]);
    $perguntas = $stmt_perguntas->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao carregar quiz para resposta: " . $e->getMessage());
    header('Location: quizzes.php');
    exit;
}

$page_title = 'Responder: ' . htmlspecialchars($quiz['titulo']);
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <form action="processa_respostas_quiz.php" method="POST">
        <input type="hidden" name="atribuicao_id" value="<?php echo $atribuicao_id; ?>">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($quiz['titulo']); ?></h2>
            <p class="text-gray-600 mt-1 mb-6"><?php echo htmlspecialchars($quiz['descricao']); ?></p>

            <div class="space-y-8">
                <?php foreach ($perguntas as $index => $pergunta): ?>
                    <fieldset>
                        <legend class="text-base font-semibold leading-6 text-gray-900"><?php echo ($index + 1) . '. ' . htmlspecialchars($pergunta['texto_pergunta']); ?></legend>
                        <div class="mt-4 space-y-4">
                            <?php if ($pergunta['tipo'] === 'texto'): ?>
                                <textarea name="respostas[<?php echo $pergunta['id']; ?>][texto]" rows="4" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                            
                            <?php else: // Unica ou Múltipla Escolha
                                $stmt_opcoes = $pdo->prepare("SELECT id, texto_opcao FROM quiz_opcoes WHERE pergunta_id = ?");
                                $stmt_opcoes->execute([$pergunta['id']]);
                                $opcoes = $stmt_opcoes->fetchAll();
                                $inputType = ($pergunta['tipo'] === 'unica_escolha') ? 'radio' : 'checkbox';
                            ?>
                                <?php foreach ($opcoes as $opcao): ?>
                                <div class="relative flex items-start">
                                    <div class="flex h-6 items-center">
                                        <input id="opcao_<?php echo $opcao['id']; ?>" name="respostas[<?php echo $pergunta['id']; ?>][opcoes][]" value="<?php echo $opcao['id']; ?>" type="<?php echo $inputType; ?>" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-600">
                                    </div>
                                    <div class="ml-3 text-sm leading-6">
                                        <label for="opcao_<?php echo $opcao['id']; ?>" class="font-medium text-gray-900"><?php echo htmlspecialchars($opcao['texto_opcao']); ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 border-t pt-6 text-right">
                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">
                    Enviar Respostas
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
