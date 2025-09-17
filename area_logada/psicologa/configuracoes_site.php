<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Buscar todos os conteúdos existentes de uma só vez
$conteudos = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT secao, titulo, texto, imagem FROM conteudo_site");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteudos[$row['secao']] = $row;
    }
} catch (PDOException $e) {
    die("Erro ao buscar conteúdos: " . $e->getMessage());
}

// Função auxiliar para obter valores de forma segura
function get_content($key, $field, $default = '') {
    global $conteudos;
    return isset($conteudos[$key]) ? htmlspecialchars($conteudos[$key][$field]) : $default;
}

require_once 'templates/header.php';
?>
<script src="https://cdn.tiny.cloud/1/j9iwoh1j7j4qho7h8elm4scjtv3733q34tylzc7ggbf9ux3e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea.richtext',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 300,
  });
  tinymce.init({
    selector: 'textarea.richtext-title', // Seletor específico para títulos
    plugins: 'autolink charmap',
    toolbar: 'undo redo | bold italic underline | removeformat',
    menubar: false,
    height: 150, // Altura menor para títulos
  });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Configurações do Site</h1>

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['mensagem_sucesso']; ?></span>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['mensagem_erro']; ?></span>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>

    <div id="tabs" class="mb-4 border-b border-gray-200">
        <nav class="flex flex-wrap -mb-px" aria-label="Tabs">
            <button onclick="changeTab('geral')" class="tab-button" data-tab-content="geral-content">Geral</button>
            <button onclick="changeTab('missao')" class="tab-button" data-tab-content="missao-content">Missão</button>
            <button onclick="changeTab('filosofia')" class="tab-button" data-tab-content="filosofia-content">Filosofia</button>
            <button onclick="changeTab('slides')" class="tab-button" data-tab-content="slides-content">Banner Rotativo</button>
            <button onclick="changeTab('atuacao')" class="tab-button" data-tab-content="atuacao-content">Atuação</button>
            <button onclick="changeTab('sobre')" class="tab-button" data-tab-content="sobre-content">Sobre</button>
            <button onclick="changeTab('contato')" class="tab-button" data-tab-content="contato-content">Contato</button>
        </nav>
    </div>

    <form action="processa_configuracoes.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
                <input type="hidden" name="active_tab" id="active_tab" value="geral">
        <div id="geral-content" class="tab-content">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Aparência e Banner Principal</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div>
                    <label for="site_cor_primaria" class="block text-gray-700 font-medium mb-2">Cor Principal (Links)</label>
                    <div class="flex items-center">
                        <input type="color" id="site_cor_primaria" name="conteudo[site_cor_primaria][texto]" value="<?php echo get_content('site_cor_primaria', 'texto', '#38b2ac'); ?>" class="p-1 h-10 w-14 block bg-white border border-gray-300 cursor-pointer rounded-lg">
                        <span id="cor-hex-primaria" class="ml-3 text-gray-600 font-mono"><?php echo get_content('site_cor_primaria', 'texto', '#38b2ac'); ?></span>
                    </div>
                </div>
                 <div>
                    <label for="site_cor_botao_bg" class="block text-gray-700 font-medium mb-2">Cor do Botão</label>
                    <div class="flex items-center">
                        <input type="color" id="site_cor_botao_bg" name="conteudo[site_cor_botao_bg][texto]" value="<?php echo get_content('site_cor_botao_bg', 'texto', '#38b2ac'); ?>" class="p-1 h-10 w-14 block bg-white border border-gray-300 cursor-pointer rounded-lg">
                        <span id="cor-hex-botao" class="ml-3 text-gray-600 font-mono"><?php echo get_content('site_cor_botao_bg', 'texto', '#38b2ac'); ?></span>
                    </div>
                </div>
                <div>
                    <label for="site_cor_header_bg" class="block text-gray-700 font-medium mb-2">Fundo do Cabeçalho</label>
                    <div class="flex items-center">
                        <input type="color" id="site_cor_header_bg" name="conteudo[site_cor_header_bg][texto]" value="<?php echo get_content('site_cor_header_bg', 'texto', '#ffffff'); ?>" class="p-1 h-10 w-14 block bg-white border border-gray-300 cursor-pointer rounded-lg">
                        <span id="cor-hex-header" class="ml-3 text-gray-600 font-mono"><?php echo get_content('site_cor_header_bg', 'texto', '#ffffff'); ?></span>
                    </div>
                </div>
                <div>
                    <label for="site_cor_footer_bg" class="block text-gray-700 font-medium mb-2">Fundo do Rodapé</label>
                    <div class="flex items-center">
                        <input type="color" id="site_cor_footer_bg" name="conteudo[site_cor_footer_bg][texto]" value="<?php echo get_content('site_cor_footer_bg', 'texto', '#1f2937'); ?>" class="p-1 h-10 w-14 block bg-white border border-gray-300 cursor-pointer rounded-lg">
                        <span id="cor-hex-footer" class="ml-3 text-gray-600 font-mono"><?php echo get_content('site_cor_footer_bg', 'texto', '#1f2937'); ?></span>
                    </div>
                </div>
            </div>
            <hr class="my-6">

            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Banner Principal</h2>
            <div class="mb-4">
                <label for="banner_inicio_titulo" class="block text-gray-700 font-medium mb-2">Título do Banner</label>
                <textarea id="banner_inicio_titulo" name="conteudo[banner_inicio][titulo]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext-title"><?php echo get_content('banner_inicio', 'titulo'); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="banner_inicio_texto" class="block text-gray-700 font-medium mb-2">Texto do Banner</label>
                <textarea id="banner_inicio_texto" name="conteudo[banner_inicio][texto]" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('banner_inicio', 'texto'); ?></textarea>
            </div>
            <div class="mb-6">
                <label for="banner_inicio_imagem" class="block text-gray-700 font-medium mb-2">Nova Imagem do Banner</label>
                <?php $imagem_atual = get_content('banner_inicio', 'imagem'); ?>
                <?php if ($imagem_atual): ?>
                    <div class="mb-2">
                        <p class="text-sm text-gray-500">Imagem atual:</p>
                        <img src="../../uploads/site/<?php echo $imagem_atual; ?>" alt="Banner Atual" class="w-48 h-auto rounded-md border">
                    </div>
                <?php endif; ?>
                <input type="hidden" name="conteudo[banner_inicio][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                <input type="file" id="banner_inicio_imagem" name="conteudo_imagem[banner_inicio]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" accept="image/jpeg, image/png, image/gif">
                <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a imagem atual.</p>
            </div>
        </div>

        </form>
</div>
