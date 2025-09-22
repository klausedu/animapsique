<?php
// =================================================================
// INÍCIO DO FICHEIRO: index.php
// =================================================================
require_once 'config.php';
require_once 'includes/db.php';

// Buscar todos os conteúdos da página inicial de uma só vez
$conteudos = [];
$secoes = [
    'banner_inicio', 'missao', 'missao_p2', 'filosofia',
    'filosofia_tec', 'filosofia_pra', 'filosofia_ime',
    'slide1', 'slide2', 'slide3'
];
$placeholders = rtrim(str_repeat('?,', count($secoes)), ',');
try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT secao, titulo, texto, imagem FROM conteudo_site WHERE secao IN ($placeholders)");
    $stmt->execute($secoes);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteudos[$row['secao']] = $row;
    }
} catch (PDOException $e) {
    die("Erro ao buscar conteúdos do site: " . $e->getMessage());
}

// Função auxiliar para obter valores
function get_content($key, $field, $default = '') {
    global $conteudos;
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;

    // Escapa o título e a imagem, exceto para o título do banner que pode ter HTML
    if (in_array($field, ['titulo', 'imagem'])) {
        // Exceção para o título do banner, que é um campo de rich text
        if ($key === 'banner_inicio' && $field === 'titulo') {
            return $value;
        }
        return htmlspecialchars($value);
    }
    // Retorna outros campos (como 'texto') sem escapar
    return $value;
}


// Função para construir o caminho da imagem de forma segura e evitar cache
function get_image_url($secao, $fallback_url) {
    $imagem_nome = get_content($secao, 'imagem');
    $caminho_no_servidor = __DIR__ . '/uploads/site/' . $imagem_nome;
    if ($imagem_nome && file_exists($caminho_no_servidor)) {
        return BASE_URL . '/uploads/site/' . $imagem_nome . '?v=' . filemtime($caminho_no_servidor);
    }
    return $fallback_url;
}

// Prepara os dados do slide para o JavaScript de forma segura
$slides_data = [
    ['imagem' => get_image_url('slide1', 'https://images.unsplash.com/photo-1491841550275-5b462bf975db?q=80&w=2940&auto=format&fit=crop'), 'texto' => get_content('slide1', 'texto', 'Citação do slide 1'), 'titulo' => get_content('slide1', 'titulo', 'Autor 1')],
    ['imagem' => get_image_url('slide2', 'https://images.unsplash.com/photo-1506466010722-395aa2bef877?q=80&w=2874&auto=format&fit=crop'), 'texto' => get_content('slide2', 'texto', 'Citação do slide 2'), 'titulo' => get_content('slide2', 'titulo', 'Autor 2')],
    ['imagem' => get_image_url('slide3', 'https://images.unsplash.com/photo-1542856391-a9f2393b2b7e?q=80&w=2874&auto=format&fit=crop'), 'texto' => get_content('slide3', 'texto', 'Citação do slide 3'), 'titulo' => get_content('slide3', 'titulo', 'Autor 3')]
];

require_once 'templates/header_publico.php';
?>
<section class="relative bg-cover bg-center text-white py-20" style="background-image: url('<?php echo get_image_url('banner_inicio', 'https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=2874&auto=format&fit=crop'); ?>');">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="container mx-auto px-6 text-center relative prose prose-xl text-white">
        <h1><?php echo get_content('banner_inicio', 'titulo', 'Bem-vindo à AnimaPsique'); ?></h1>
        <div><?php echo get_content('banner_inicio', 'texto', '<p>Um espaço de acolhimento e transformação.</p>'); ?></div>
        <a href="contato.php" style="background-color: var(--cor-botao-bg);" class="no-underline inline-block mt-4 py-2 px-4 text-white rounded-full hover:opacity-90 transition-opacity">Agende a sua Consulta</a>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            <?php echo htmlspecialchars(get_content('missao', 'titulo', 'Nossa Missão')); ?>
        </h2>
        <div class="prose lg:prose-xl max-w-3xl mx-auto mb-4"><?php echo get_content('missao', 'texto', '<p>Parágrafo 1 sobre a missão.</p>'); ?></div>
        <div class="prose max-w-3xl mx-auto"><?php echo get_content('missao_p2', 'texto', '<p>Parágrafo 2 sobre a missão.</p>'); ?></div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
            <?php echo htmlspecialchars(get_content('filosofia', 'titulo', 'Nossa Filosofia')); ?>
        </h2>
        <div class="prose lg:prose-xl max-w-3xl mx-auto text-center mb-10"><?php echo get_content('filosofia', 'texto', '<p>Texto principal sobre a filosofia.</p>'); ?></div>

        <div class="max-w-3xl mx-auto space-y-4">
            <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                    <span><?php echo htmlspecialchars(get_content('filosofia_tec', 'titulo', '+ Inserção de Tecnologias digitais')); ?></span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose">
                    <?php echo get_content('filosofia_tec', 'texto', 'Texto expansível sobre tecnologias.'); ?>
                </div>
            </div>
            <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                    <span><?php echo htmlspecialchars(get_content('filosofia_pra', 'titulo', '+ Práticas psicológicas internacionais')); ?></span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose">
                    <?php echo get_content('filosofia_pra', 'texto', 'Texto expansível sobre práticas internacionais.'); ?>
                </div>
            </div>
             <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                    <span><?php echo htmlspecialchars(get_content('filosofia_ime', 'titulo', '+ Imersão, ampliação da subjetividade')); ?></span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose">
                     <?php echo get_content('filosofia_ime', 'texto', 'Texto expansível sobre imersão.'); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-6">
        <div x-data="carousel()"
             x-init="start()"
             @mouseenter="stop()"
             @mouseleave="start()"
             class="relative w-full max-w-4xl mx-auto h-80 flex items-center justify-center">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index"
                     class="absolute inset-0 w-full h-full bg-cover bg-center rounded-lg shadow-xl transition-all duration-1000 ease-in-out"
                     :style="{ backgroundImage: `url('${slide.imagem}')` }"
                     x-transition:enter="transition ease-out duration-1000"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-1000"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">
                    <div class="absolute inset-0 bg-black bg-opacity-60 rounded-lg flex items-center justify-center p-6">
                        <div class="text-center text-white">
                            <div class="prose prose-2xl text-white italic" x-html="slide.texto"></div>
                            <footer class="text-lg font-semibold mt-4" x-text="`— ${slide.titulo}`"></footer>
                        </div>
                    </div>
                </div>
            </template>
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goToSlide(index)"
                            :class="{'bg-white': activeSlide === index, 'bg-white/50': activeSlide !== index}"
                            class="w-3 h-3 rounded-full hover:bg-white transition"></button>
                </template>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<script>
function carousel() {
    return {
        activeSlide: 0,
        slides: <?php echo json_encode($slides_data, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS); ?>,
        interval: null,
        start() {
            this.interval = setInterval(() => {
                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            }, 5000);
        },
        stop() {
            clearInterval(this.interval);
        },
        goToSlide(index) {
            this.activeSlide = index;
            this.stop();
            this.start();
        }
    }
}
</script>
<style>
/* Estilos para o conteúdo gerado pelo editor */
.prose h1 { color: inherit; }
.prose p { margin-bottom: 1em; }
.prose ul, .prose ol { margin-left: 1.25em; margin-bottom: 1em; }
</style>
<?php require_once 'templates/footer_publico.php'; ?>
<?php
// =================================================================
// FIM DO FICHEIRO: index.php
// =================================================================
?>
