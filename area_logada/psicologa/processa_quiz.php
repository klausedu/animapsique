<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quizzes.php');
    exit;
}

$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$perguntas = $_POST['perguntas'] ?? [];

// Validação básica
if (empty($titulo) || empty($perguntas)) {
    // Definir uma mensagem de erro na sessão para exibir na página anterior
    $_SESSION['quiz_error'] = "O título e pelo menos uma pergunta são obrigatórios.";
    header('Location: criar_quiz.php');
    exit;
}

try {
    $pdo = conectar();
    // Inicia uma transação: ou tudo é salvo, ou nada é.
    $pdo->beginTransaction();

    // 1. Salva o quiz principal e obtém o ID
    $stmt_quiz = $pdo->prepare("INSERT INTO quizzes (titulo, descricao) VALUES (?, ?)");
    $stmt_quiz->execute([$titulo, $descricao]);
    $quiz_id = $pdo->lastInsertId();

    // Prepara as queries para perguntas e opções para serem usadas no loop
    $stmt_pergunta = $pdo->prepare("INSERT INTO quiz_perguntas (quiz_id, texto_pergunta, tipo, ordem) VALUES (?, ?, ?, ?)");
    $stmt_opcao = $pdo->prepare("INSERT INTO quiz_opcoes (pergunta_id, texto_opcao) VALUES (?, ?)");

    // 2. Itera sobre cada pergunta enviada pelo formulário
    foreach ($perguntas as $ordem => $pergunta_data) {
        $texto_pergunta = trim($pergunta_data['texto']);
        $tipo_pergunta = $pergunta_data['tipo'];

        if (empty($texto_pergunta)) {
            // Se uma pergunta estiver vazia, cancela tudo
            throw new Exception("O texto de uma das perguntas não pode estar vazio.");
        }

        // Salva a pergunta e obtém o ID dela
        $stmt_pergunta->execute([$quiz_id, $texto_pergunta, $tipo_pergunta, $ordem]);
        $pergunta_id = $pdo->lastInsertId();

        // 3. Se a pergunta for de múltipla ou única escolha, salva as opções
        if (($tipo_pergunta === 'unica_escolha' || $tipo_pergunta === 'multipla_escolha') && !empty($pergunta_data['opcoes'])) {
            foreach ($pergunta_data['opcoes'] as $texto_opcao) {
                $texto_opcao = trim($texto_opcao);
                if (!empty($texto_opcao)) {
                    $stmt_opcao->execute([$pergunta_id, $texto_opcao]);
                }
            }
        }
    }

    // Se tudo correu bem, confirma as alterações no banco de dados
    $pdo->commit();

    $_SESSION['quiz_success'] = "Quiz '".htmlspecialchars($titulo)."' criado com sucesso!";
    header('Location: quizzes.php');
    exit;

} catch (Exception $e) {
    // Se qualquer erro ocorrer, desfaz todas as operações
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao criar quiz: " . $e->getMessage());
    $_SESSION['quiz_error'] = "Ocorreu um erro ao salvar o quiz: " . $e->getMessage();
    header('Location: criar_quiz.php');
    exit;
}
?>
