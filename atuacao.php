<?php
require_once 'config.php';
require_once 'includes/db.php';

// Define todas as secções necessárias para esta página
$secoes = [
    'atuacao_titulo',
    'atuacao_card1_titulo', 'atuacao_card2_titulo', 'atuacao_card3_titulo',
    'atuacao_card4_titulo', 'atuacao_card5_titulo',
    'atuacao_card1_exibir', 'atuacao_card2_exibir', 'atuacao_card3_exibir',
    'atuacao_card4_exibir', 'atuacao_card5_exibir'
];
$placeholders = rtrim(str_repeat('?,', count($secoes)), ',');
$conteudos = [];
try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT secao, titulo, texto, imagem FROM conteudo_site WHERE secao IN ($placeholders)");
    $stmt->execute($secoes);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteudos[$row['secao']] = $row;
    }
} catch (PDOException $e) {
    die("Erro ao buscar conteúdo da página: " . $e->getMessage());
}

// Função auxiliar para obter valores (SEM htmlspecialchars)
function get_content($key, $field, $default = '') {
    global $conteudos;
    // Retorna o texto bruto para renderizar o HTML do editor
    return isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
}

require_once 'templates/header_publico.php';
?>
<main class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-4" style="color: var(--cor-primaria);">
        <?php echo htmlspecialchars(get_content('atuacao_titulo', 'titulo', 'Áreas de Atuação')); ?>
    </h1>
    <div class="prose lg:prose-xl max-w-3xl mx-auto mb-12 text-center">
        <?php echo get_content('atuacao_titulo', 'texto', 'Texto introdutório sobre as áreas de atuação.'); ?>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?php if (get_content("atuacao_card{$i}_exibir", 'titulo') === 'sim'): ?>
                <a href="atuacao_detalhe.php?id=<?php echo $i; ?>" class="flip-card-container block">
                    <div class="flip-card">
                        <div class="flip-card-front bg-cover bg-center rounded-lg shadow-lg" style="background-image: url('uploads/site/<?php echo htmlspecialchars(get_content("atuacao_card{$i}_titulo", 'imagem', 'https://placehold.co/300x400/E2E8F0/4A5568?text=Imagem')); ?>')">
                            <div class="bg-black bg-opacity-40 p-4 flex items-end h-full rounded-lg">
                                <h3 class="text-white text-xl font-bold"><?php echo htmlspecialchars(get_content("atuacao_card{$i}_titulo", 'titulo', "Serviço {$i}")); ?></h3>
                            </div>
                        </div>
                        <div class="flip-card-back bg-white p-6 rounded-lg shadow-lg flex flex-col justify-center items-center text-center">
                            <div class="text-gray-700 prose">
                                <?php echo get_content("atuacao_card{$i}_titulo", 'texto', "Texto do verso do serviço {$i}."); ?>
                            </div>
                            <span class="mt-4 text-sm font-semibold" style="color: var(--cor-primaria);">Saber Mais &rarr;</span>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</main>
<style>
    .flip-card-container {
        perspective: 1000px;
        height: 400px;
    }
    .flip-card {
        width: 100%;
        height: 100%;
        position: relative;
        transition: transform 0.8s;
        transform-style: preserve-3d;
    }
    .flip-card-container:hover .flip-card {
        transform: rotateY(180deg);
    }
    .flip-card-front, .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
    }
    .flip-card-back {
        transform: rotateY(180deg);
    }
    /* Estilos para o conteúdo gerado pelo editor */
    .prose p { margin-bottom: 1em; }
    .prose ul, .prose ol { margin-left: 1.25em; margin-bottom: 1em; }
</style>
<?php require_once 'templates/footer_publico.php'; ?>
