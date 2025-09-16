<?php
require_once 'config.php';
require_once 'includes/db.php';

// Validar o ID recebido para garantir que é um número entre 1 e 5
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1 || $id > 5) {
    header("Location: atuacao.php");
    exit;
}

// Define todas as secções necessárias para a página de detalhe
$secoes = [
    "atuacao_p{$id}_titulo",
    "atuacao_p{$id}_p2",
    "atuacao_p{$id}_desfecho"
];
$placeholders = rtrim(str_repeat('?,', count($secoes)), ',');

$conteudos = [];
try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT secao, titulo, texto FROM conteudo_site WHERE secao IN ($placeholders)");
    $stmt->execute($secoes);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteudos[$row['secao']] = $row;
    }
} catch (PDOException $e) {
    die("Erro ao buscar conteúdo da página: " . $e->getMessage());
}

// Função auxiliar para obter valores (SEM htmlspecialchars no TEXTO)
function get_content($key, $field, $default = '') {
    global $conteudos;
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
    // Escapa apenas o título, mas não o texto
    return ($field === 'titulo') ? htmlspecialchars($value) : $value;
}

require_once 'templates/header_publico.php';
?>

<main class="bg-white py-16">
    <div class="container mx-auto px-6 max-w-4xl">
        <article class="prose lg:prose-xl max-w-none">
            <h1 class="text-4xl font-bold text-gray-800 mb-6" style="color: var(--cor-primaria);">
                <?php echo get_content("atuacao_p{$id}_titulo", 'titulo', "Título do Serviço {$id}"); ?>
            </h1>

            <div class="text-gray-700 leading-relaxed">
                <?php echo get_content("atuacao_p{$id}_titulo", 'texto', 'Parágrafo 1 sobre o serviço.'); ?>
            </div>

            <hr class="my-8">

            <div class="text-gray-700 leading-relaxed">
                <?php echo get_content("atuacao_p{$id}_p2", 'texto', 'Parágrafo 2 sobre o serviço.'); ?>
            </div>

            <hr class="my-8">

            <div class="text-gray-700 leading-relaxed bg-gray-50 p-6 rounded-lg">
                <?php echo get_content("atuacao_p{$id}_desfecho", 'texto', 'Desfecho sobre o serviço.'); ?>
            </div>

             <div class="mt-12 text-center">
                <a href="atuacao.php" class="inline-block text-lg font-semibold border-b-2 border-transparent hover:border-[var(--cor-primaria)] transition-colors" style="color: var(--cor-primaria);">
                    &larr; Voltar para Áreas de Atuação
                </a>
            </div>
        </article>
    </div>
</main>

<style>
    /* Estilos para o conteúdo do artigo gerado pelo editor */
    .prose p { margin-bottom: 1.25em; }
    .prose h1, .prose h2, .prose h3 { margin-bottom: 0.8em; margin-top: 1.5em; font-weight: bold; }
    .prose a { color: var(--cor-primaria); text-decoration: underline; }
    .prose ul, .prose ol { margin-left: 1.5em; margin-bottom: 1.25em; }
    .prose li { margin-bottom: 0.5em; }
</style>

<?php require_once 'templates/footer_publico.php'; ?>
