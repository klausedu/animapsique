<?php
// =================================================================
// INÍCIO DO FICHEIRO: sobre.php
// =================================================================
require_once 'config.php';
require_once 'includes/db.php';

// Define todas as seções necessárias para esta página
$secoes = [
    'sobre_objetivo_titulo', 'sobre_reflexao_imagem', 'sobre_psicologa_foto',
    'sobre_mim_texto', 'sobre_quem_sou_titulo', 'sobre_especializacoes_titulo',
    'sobre_modalidades_titulo', 'sobre_mod1_imagem', 'sobre_mod2_imagem', 'sobre_mod3_imagem'
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
    die("Erro ao buscar conteúdos do site: " . $e->getMessage());
}

// Função auxiliar para obter valores (SEM htmlspecialchars no TEXTO)
function get_content($key, $field, $default = '') {
    global $conteudos;
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
    // Escapa o título e a imagem, mas não o texto (que contém HTML)
    return in_array($field, ['titulo', 'imagem']) ? htmlspecialchars($value) : $value;
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

require_once 'templates/header_publico.php';
?>
<div class="bg-white">
    <section class="relative text-white py-20 bg-cover bg-center" style="background-image: url('<?php echo get_image_url('sobre_reflexao_imagem', 'https://images.unsplash.com/photo-1549492423-400259a5e5a4?q=80&w=2940&auto=format&fit=crop'); ?>');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="container mx-auto px-6 text-center relative">
            <h2 class="text-3xl font-bold mb-4"><?php echo get_content('sobre_objetivo_titulo', 'titulo', 'Objetivo'); ?></h2>
            <div class="prose prose-xl mx-auto text-white">
                <?php echo get_content('sobre_objetivo_titulo', 'texto', '<p>“O objetivo da psicoterapia...”</p>'); ?>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="container mx-auto px-6 flex flex-col md:flex-row items-start gap-12">
            <div class="md:w-1/3 w-full space-y-8">
                <img src="<?php echo get_image_url('sobre_psicologa_foto', 'https://placehold.co/400x400/EFEFEF/333333?text=Foto'); ?>" alt="Foto da Psicóloga" class="rounded-lg shadow-lg w-full">
                <div class="prose italic text-gray-700 text-lg leading-relaxed">
                   <?php echo get_content('sobre_mim_texto', 'texto', '<i>... Priorizo a constante especialização...</i>'); ?>
                </div>
            </div>

            <div class="md:w-2/3 w-full space-y-12">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4"><?php echo get_content('sobre_quem_sou_titulo', 'titulo', 'Quem sou eu...'); ?></h3>
                    <div class="prose max-w-none text-gray-600 leading-relaxed">
                        <?php echo get_content('sobre_quem_sou_titulo', 'texto', '<p>Nara Helena Lopes, Psicóloga Clínica...</p>'); ?>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4"><?php echo get_content('sobre_especializacoes_titulo', 'titulo', 'Minhas especializações'); ?></h3>
                    <div class="prose max-w-none text-gray-600 leading-relaxed space-y-4">
                        <?php echo get_content('sobre_especializacoes_titulo', 'texto', '<ul><li>Pós Doutorado...</li></ul>'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-12 text-center"><?php echo get_content('sobre_modalidades_titulo', 'titulo', 'Modalidades de Atendimento Psicológico'); ?></h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <img src="<?php echo get_image_url('sobre_mod1_imagem', 'https://placehold.co/300x200/EFEFEF/333333?text=Online'); ?>" alt="Psicoterapia on-line" class="rounded-lg shadow-md w-full h-48 object-cover mb-4">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2"><?php echo get_content('sobre_mod1_imagem', 'titulo', 'Psicoterapia on-line'); ?></h4>
                    <div class="prose text-gray-600 mx-auto">
                        <?php echo get_content('sobre_mod1_imagem', 'texto', '<p>Atendimentos psicológicos especializados com recursos on-line...</p>'); ?>
                    </div>
                </div>
                <div class="text-center">
                    <img src="<?php echo get_image_url('sobre_mod2_imagem', 'https://placehold.co/300x200/EFEFEF/333333?text=Focal'); ?>" alt="Psicoterapia focal on-line" class="rounded-lg shadow-md w-full h-48 object-cover mb-4">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2"><?php echo get_content('sobre_mod2_imagem', 'titulo', 'Psicoterapia focal on-line'); ?></h4>
                    <div class="prose text-gray-600 mx-auto">
                        <?php echo get_content('sobre_mod2_imagem', 'texto', '<p>Atendimentos psicológicos especializados, com recursos on-line, com duração breve...</p>'); ?>
                    </div>
                </div>
                <div class="text-center">
                    <img src="<?php echo get_image_url('sobre_mod3_imagem', 'https://placehold.co/300x200/EFEFEF/333333?text=Rodas'); ?>" alt="Rodas de conversa on-line" class="rounded-lg shadow-md w-full h-48 object-cover mb-4">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2"><?php echo get_content('sobre_mod3_imagem', 'titulo', 'Rodas de conversa on-line'); ?></h4>
                    <div class="prose text-gray-600 mx-auto">
                        <?php echo get_content('sobre_mod3_imagem', 'texto', '<p>Encontros em grupo de sessão única...</p>'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<style>
/* Estilos para o conteúdo gerado pelo editor */
.prose p { margin-bottom: 1em; }
.prose ul, .prose ol { margin-left: 1.25em; margin-bottom: 1em; }
.prose-xl h1, .prose-xl h2, .prose-xl h3 { color: inherit; }
</style>
<?php require_once 'templates/footer_publico.php'; ?>
