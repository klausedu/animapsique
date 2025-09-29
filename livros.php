<?php
require_once 'config.php';
require_once 'includes/db.php';

// Define as seções necessárias para esta página
$secoes = [
    'livros_titulo_pagina',
    'livro1_titulo', 'livro1_link', 'livro1_imagem',
    'livro2_titulo', 'livro2_link', 'livro2_imagem',
    'livro3_titulo', 'livro3_link', 'livro3_imagem',
    'livro1_exibir', 'livro2_exibir', 'livro3_exibir'
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

function get_content($key, $field, $default = '') {
    global $conteudos;
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
    return in_array($field, ['titulo', 'imagem']) ? htmlspecialchars($value) : $value;
}

function get_image_url($secao, $fallback_url) {
    $imagem_nome = get_content($secao, 'imagem');
    $caminho_no_servidor = __DIR__ . '/uploads/site/' . $imagem_nome;
    if ($imagem_nome && file_exists($caminho_no_servidor)) {
        return BASE_URL . '/uploads/site/' . $imagem_nome . '?v=' . filemtime($caminho_no_servidor);
    }
    return $fallback_url;
}

require_once 'templates/header_publico.php';
?>

<main class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-12 text-center">
            <?php echo get_content('livros_titulo_pagina', 'titulo', 'Livros'); ?>
        </h2>
        <div class="grid md:grid-cols-3 gap-8">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <?php if (get_content("livro{$i}_exibir", 'titulo') === 'sim'): ?>
                    <div class="text-center bg-white p-6 rounded-lg shadow-lg">
                        <img src="<?php echo get_image_url("livro{$i}_imagem", 'https://placehold.co/300x400/EFEFEF/333333?text=Capa+Livro'); ?>" alt="<?php echo get_content("livro{$i}_titulo", 'titulo'); ?>" class="rounded-lg shadow-md w-full h-auto object-cover mb-4 max-h-96">
                        <h4 class="text-xl font-semibold text-gray-800 mb-2"><?php echo get_content("livro{$i}_titulo", 'titulo', "Livro {$i}"); ?></h4>
                        <div class="prose text-gray-600 mx-auto">
                            <?php echo get_content("livro{$i}_titulo", 'texto', "<p>Descrição do livro {$i}.</p>"); ?>
                        </div>
                        <?php if($link = get_content("livro{$i}_link", 'titulo')): ?>
                            <a href="<?php echo $link; ?>" target="_blank" rel="noopener noreferrer" class="mt-4 inline-block font-semibold hover:opacity-80" style="color: var(--cor-primaria);">
                                Saiba Mais &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php
require_once 'templates/footer_publico.php';
?>
