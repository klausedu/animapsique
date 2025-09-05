<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$atribuicao_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$atribuicao_id) {
    header('Location: quizzes.php');
    exit;
}

try {
    $pdo = conectar();
    // Busca os detalhes da atribuição, quiz e paciente
    $sql = "
        SELECT 
            q.id AS quiz_id, q.titulo, 
            p.nome AS paciente_nome,
            qa.data_resposta
        FROM quiz_atribuicoes qa
        JOIN quizzes q ON qa.quiz_id = q.id
        JOIN pacientes p ON qa.paciente_id = p.id
        WHERE qa.id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$atribuicao_id]);
    $detalhes = $stmt->fetch();
    if (!$detalhes) {
        header('Location: quizzes.php');
        exit;
    }

    // Busca todas as perguntas do quiz
    $stmt_perguntas = $pdo->prepare("SELECT id, texto_pergunta, tipo FROM quiz_perguntas WHERE quiz_id = ? ORDER BY ordem ASC");
    $stmt_perguntas->execute([$detalhes['quiz_id']]);
    $perguntas = $stmt_perguntas->fetchAll();

    // Busca todas as respostas para esta atribuição
    $stmt_respostas = $pdo->prepare("SELECT pergunta_id, resposta_texto, resposta_opcao_id FROM quiz_respostas WHERE atribuicao_id = ?");
    $stmt_respostas->execute([$atribuicao_id]);
    $respostas_raw = $stmt_respostas->fetchAll();
    
    // Organiza as respostas para fácil acesso
    $respostas = [];
    foreach ($respostas_raw as $r) {
        $respostas[$r['pergunta_id']][] = $r;
    }

} catch (PDOException $e) {
    error_log("Erro ao visualizar resposta do quiz: " . $e->getMessage());
    header('Location: quizzes.php');
    exit;
}

$page_title = 'Resposta de ' . htmlspecialchars($detalhes['paciente_nome']);
require_once 'templates/header.php';
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($detalhes['titulo']); ?></h2>
        <p class="text-gray-600 mt-1">Respondido por: <strong><?php echo htmlspecialchars($detalhes['paciente_nome']); ?></strong> em <?php echo (new DateTime($detalhes['data_resposta']))->format('d/m/Y'); ?></p>
        <a href="ver_respostas_quiz.php?id=<?php echo $detalhes['quiz_id']; ?>" class="text-sm text-teal-600 hover:text-teal-800 mt-2 inline-block">&larr; Voltar</a>

        <div class="mt-8 space-y-8">
            <?php foreach ($perguntas as $pergunta): ?>
                <div class="border-t pt-6">
                    <p class="text-base font-semibold leading-6 text-gray-900"><?php echo htmlspecialchars($pergunta['texto_pergunta']); ?></p>
                    <div class="mt-4 p-4 bg-gray-50 rounded-md">
                        <?php if ($pergunta['tipo'] === 'texto'): ?>
                            <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($respostas[$pergunta['id']][0]['resposta_texto'] ?? 'Não respondido')); ?></p>
                        <?php else: // Múltipla ou Única Escolha
                            $opcoes_selecionadas_ids = array_column($respostas[$pergunta['id']] ?? [], 'resposta_opcao_id');
                            // Busca o texto das opções
                            $stmt_opcoes = $pdo->prepare("SELECT id, texto_opcao FROM quiz_opcoes WHERE pergunta_id = ?");
                            $stmt_opcoes->execute([$pergunta['id']]);
                            $opcoes_todas = $stmt_opcoes->fetchAll();
                        ?>
                        <ul class="list-disc list-inside space-y-2">
                            <?php foreach ($opcoes_todas as $opcao): ?>
                                <?php if (in_array($opcao['id'], $opcoes_selecionadas_ids)): ?>
                                    <li class="font-semibold text-teal-800"><?php echo htmlspecialchars($opcao['texto_opcao']); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
