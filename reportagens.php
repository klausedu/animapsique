<?php
require_once 'config.php';
require_once 'includes/db.php';

$publicacoes = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, titulo, resumo, link, imagem FROM publicacoes ORDER BY id DESC");
    $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicações: " . $e->getMessage());
}

require_once 'templates/header_publico.php';
?>

<main class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12" style="color: var(--cor-primaria);">Publicações e Artigos</h1>

        <?php if (empty($publicacoes)): ?>
            <p class="text-center text-gray-500">Nenhuma publicação disponível no momento.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($publicacoes as $pub): ?>
                    <a href="publicacao_detalhe.php?id=<?php echo $pub['id']; ?>" class="block bg-white rounded-lg shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 group">
                        <div class="relative">
                            <?php if (!empty($pub['imagem'])): ?>
                                <img src="uploads/site/<?php echo htmlspecialchars($pub['imagem']); ?>" alt="<?php echo htmlspecialchars($pub['titulo']); ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Sem Imagem</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-[var(--cor-primaria)] transition-colors"><?php echo htmlspecialchars($pub['titulo']); ?></h2>
                            <p class="text-gray-600 text-sm mb-4">
                                <?php
                                // Mostra um resumo de 100 caracteres
                                //echo htmlspecialchars(mb_substr($pub['resumo'], 0, 100)) . '...';
                                ?>
                            </p>
                            <span class="font-semibold text-sm text-[var(--cor-primaria)]">Ler Mais &rarr;</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'templates/footer_publico.php'; ?>

