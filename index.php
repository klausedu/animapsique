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

// Função auxiliar para obter valores (SEM htmlspecialchars no TEXTO e no TÍTULO)
function get_content($key, $field, $default = '') {
    global $conteudos;
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
    // Escapa apenas a imagem
    return ($field === 'imagem') ? htmlspecialchars($value) : $value;
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
    <div class="container mx-auto px-6 text-center relative prose prose-xl text-white max-w-none">
        <div class="max-w-3xl mx-auto">
            <?php echo get_content('banner_inicio', 'titulo', '<h1>Bem-vindo à AnimaPsique</h1>'); ?>
            <?php echo get_content('banner_inicio', 'texto', '<p>Um espaço de acolhimento e transformação.</p>'); ?>
            <a href="contato.php" style="background-color: var(--cor-botao-bg);" class="no-underline inline-block mt-4 py-2 px-4 text-white rounded-full hover:opacity-90 transition-opacity">Agende a sua Consulta</a>
        </div>
    </div>
</section>
