<?php
require_once 'config.php';
require_once 'includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: reportagens.php');
    exit;
}

$publicacao = null;
try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT titulo, resumo, link, imagem FROM publicacoes WHERE id = ?");
    $stmt->execute([$id]);
    $publicacao = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicação: " . $e->getMessage());
}

if (!$publicacao) {
    header('Location: reportagens.php');
    exit;
}

require_once 'templates/header_publico.php';
?>

<main class="bg-white py-12 md:py-20">
    <div class="container mx-auto px-6 max-w-4xl">
        <article>
            <header class="mb-8">
                <a href="reportagens.php" class="text-sm font-semibold text-[var(--cor-primaria)] hover:opacity-80 transition-opacity mb-4 inline-block">&larr; Voltar para todas as publicações</a>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 leading-tight">
                    <?php echo htmlspecialchars($publicacao['titulo']); ?>
                </h1>
            </header>

            <?php if (!empty($publicacao['imagem'])): ?>
                <figure class="mb-8">
                    <img src="uploads/site/<?php echo htmlspecialchars($publicacao['imagem']); ?>" alt="<?php echo htmlspecialchars($publicacao['titulo']); ?>" class="w-full h-auto max-h-[500px] object-cover rounded-lg shadow-lg">
                </figure>
            <?php endif; ?>

            <div class="prose lg:prose-xl max-w-none text-gray-700">
                <?php
                // Exibe o HTML diretamente, pois vem de uma fonte confiável (você)
                echo $publicacao['resumo'];
                ?>
            </div>

            <?php if (!empty($publicacao['link'])): ?>
                <footer class="mt-12 border-t pt-8 text-center">
                    <p class="text-lg text-gray-800 mb-4">Adquira o livro ou leia o artigo completo:</p>
                    <a href="<?php echo htmlspecialchars($publicacao['link']); ?>" target="_blank" rel="noopener noreferrer" class="inline-block text-white font-bold py-3 px-8 rounded-full transition-opacity" style="background-color: var(--cor-primaria);">
                        Saiba Mais
                    </a>
                </footer>
            <?php endif; ?>

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
    .prose img { max-width: 100%; height: auto; border-radius: 0.5rem; margin-top: 2em; margin-bottom: 2em; }
</style>

<?php require_once 'templates/footer_publico.php'; ?>
