<?php
require_once 'config.php';
require_once 'includes/db.php';

$publicacoes = [];
try {
    $pdo = conectar();
    // 1. Atualiza a consulta para buscar a nova coluna 'link'
    $stmt = $pdo->query("SELECT id, titulo, texto, link FROM publicacoes_academicas ORDER BY titulo ASC");
    $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicações acadêmicas: " . $e->getMessage());
}

require_once 'templates/header_publico.php';
?>
<main class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center flex items-center justify-center" style="color: var(--cor-primaria);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Publicações Acadêmicas
        </h2>

        <div class="max-w-3xl mx-auto space-y-4">
            <?php if (empty($publicacoes)): ?>
                <p class="text-center text-gray-500">Nenhuma publicação acadêmica encontrada no momento.</p>
            <?php else: ?>
                <?php foreach ($publicacoes as $pub): ?>
                    <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                        <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-teal-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <?php echo htmlspecialchars($pub['titulo']); ?>
                            </span>
                            <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose max-w-none">
                            <?php echo $pub['texto']; ?>

                            <?php if (!empty($pub['link'])): ?>
                                <div class="mt-4">
                                    <a href="<?php echo htmlspecialchars($pub['link']); ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-sm no-underline" style="color: var(--cor-primaria);">
                                        Saiba Mais &rarr;
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php require_once 'templates/footer_publico.php'; ?>
