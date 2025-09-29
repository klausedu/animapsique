<?php
require_once 'config.php';
require_once 'includes/db.php';

$livros = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, titulo, texto, link, imagem FROM livros ORDER BY id DESC");
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar os livros: " . $e->getMessage());
}

require_once 'templates/header_publico.php';
?>

<main class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12 flex items-center justify-center" style="color: var(--cor-primaria);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Livros
        </h1>

        <?php if (empty($livros)): ?>
            <p class="text-center text-gray-500">Nenhum livro disponível no momento.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($livros as $livro): ?>
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center flex flex-col">
                        <?php if (!empty($livro['imagem'])): ?>
                            <img src="uploads/site/<?php echo htmlspecialchars($livro['imagem']); ?>" alt="<?php echo htmlspecialchars($livro['titulo']); ?>" class="rounded-lg shadow-md w-full h-64 object-cover mb-4">
                        <?php else: ?>
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg mb-4">
                                <span class="text-gray-500">Sem Imagem</span>
                            </div>
                        <?php endif; ?>
                        
                        <h4 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($livro['titulo']); ?></h4>
                        
                        <div class="prose text-gray-600 mx-auto flex-grow">
                            <?php echo $livro['texto']; ?>
                        </div>

                        <?php if (!empty($livro['link'])): ?>
                            <a href="<?php echo htmlspecialchars($livro['link']); ?>" target="_blank" rel="noopener noreferrer" class="mt-4 inline-block font-semibold hover:opacity-80" style="color: var(--cor-primaria);">
                                Saiba Mais &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'templates/footer_publico.php'; ?>
