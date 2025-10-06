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
<script src="https://cdn.jsdelivr.net/npm/hugerte@1.0.9/hugerte.min.js"></script>
<script>
  hugerte.init({
    selector: 'textarea.richtext',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 300,
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

    <form action="salvar_opcoes.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
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
                <textarea id="banner_inicio_titulo" name="conteudo[banner_inicio][titulo]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('banner_inicio', 'titulo'); ?></textarea>
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

        <div id="missao-content" class="tab-content hidden">
             <h2 class="text-2xl font-semibold text-gray-700 mb-4">Nossa Missão</h2>
            <div class="mb-4">
                <label for="missao_titulo" class="block text-gray-700 font-medium mb-2">Título da Secção</label>
                <input type="text" id="missao_titulo" name="conteudo[missao][titulo]" value="<?php echo get_content('missao', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
            <div class="mb-4">
                <label for="missao_texto" class="block text-gray-700 font-medium mb-2">Parágrafo 1</label>
                <textarea id="missao_texto" name="conteudo[missao][texto]" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('missao', 'texto'); ?></textarea>
            </div>
            <div class="mb-6">
                <label for="missao_p2_texto" class="block text-gray-700 font-medium mb-2">Parágrafo 2</label>
                <textarea id="missao_p2_texto" name="conteudo[missao_p2][texto]" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('missao_p2', 'texto'); ?></textarea>
            </div>
        </div>

        <div id="filosofia-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Nossa Filosofia</h2>
            <div class="mb-4">
                <label for="filosofia_titulo" class="block text-gray-700 font-medium mb-2">Título da Secção</label>
                <input type="text" id="filosofia_titulo" name="conteudo[filosofia][titulo]" value="<?php echo get_content('filosofia', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
            <div class="mb-4">
                <label for="filosofia_texto" class="block text-gray-700 font-medium mb-2">Texto Principal</label>
                <textarea id="filosofia_texto" name="conteudo[filosofia][texto]" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('filosofia', 'texto'); ?></textarea>
            </div>
            <hr class="my-6">
             <h3 class="text-xl font-semibold text-gray-700 mb-3">Textos Expansíveis</h3>
            <div class="mb-4">
                <label for="filosofia_tec_titulo" class="block text-gray-700 font-medium mb-2">Título 1: Tecnologias Digitais</label>
                <input type="text" id="filosofia_tec_titulo" name="conteudo[filosofia_tec][titulo]" value="<?php echo get_content('filosofia_tec', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                <label for="filosofia_tec_texto" class="block text-gray-700 font-medium mb-2 mt-2">Texto Expansível 1</label>
                <textarea id="filosofia_tec_texto" name="conteudo[filosofia_tec][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('filosofia_tec', 'texto'); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="filosofia_pra_titulo" class="block text-gray-700 font-medium mb-2">Título 2: Práticas Internacionais</label>
                <input type="text" id="filosofia_pra_titulo" name="conteudo[filosofia_pra][titulo]" value="<?php echo get_content('filosofia_pra', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                 <label for="filosofia_pra_texto" class="block text-gray-700 font-medium mb-2 mt-2">Texto Expansível 2</label>
                <textarea id="filosofia_pra_texto" name="conteudo[filosofia_pra][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('filosofia_pra', 'texto'); ?></textarea>
            </div>
            <div class="mb-6">
                <label for="filosofia_ime_titulo" class="block text-gray-700 font-medium mb-2">Título 3: Imersão e Subjetividade</label>
                <input type="text" id="filosofia_ime_titulo" name="conteudo[filosofia_ime][titulo]" value="<?php echo get_content('filosofia_ime', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                <label for="filosofia_ime_texto" class="block text-gray-700 font-medium mb-2 mt-2">Texto Expansível 3</label>
                <textarea id="filosofia_ime_texto" name="conteudo[filosofia_ime][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 richtext"><?php echo get_content('filosofia_ime', 'texto'); ?></textarea>
            </div>
        </div>

        <div id="slides-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Banner Rotativo</h2>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="border p-4 rounded-md mb-4">
                <h3 class="text-xl font-semibold text-gray-700 mb-3">Slide <?php echo $i; ?></h3>
                <div class="mb-4">
                    <label for="slide<?php echo $i; ?>_titulo" class="block text-gray-700 font-medium mb-2">Autor da Citação</label>
                    <input type="text" id="slide<?php echo $i; ?>_titulo" name="conteudo[slide<?php echo $i; ?>][titulo]" value="<?php echo get_content("slide{$i}", 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="slide<?php echo $i; ?>_texto" class="block text-gray-700 font-medium mb-2">Texto da Citação</label>
                    <textarea id="slide<?php echo $i; ?>_texto" name="conteudo[slide<?php echo $i; ?>][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content("slide{$i}", 'texto'); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="slide<?php echo $i; ?>_imagem" class="block text-gray-700 font-medium mb-2">Nova Imagem de Fundo</label>
                    <?php $imagem_atual = get_content("slide{$i}", 'imagem'); ?>
                    <?php if ($imagem_atual): ?>
                    <div class="mb-2">
                        <p class="text-sm text-gray-500">Imagem atual:</p>
                        <img src="../../uploads/site/<?php echo $imagem_atual; ?>" alt="Slide <?php echo $i; ?> Atual" class="w-48 h-auto rounded-md border">
                    </div>
                    <?php endif; ?>
                    <input type="hidden" name="conteudo[slide<?php echo $i; ?>][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                    <input type="file" id="slide<?php echo $i; ?>_imagem" name="conteudo_imagem[slide<?php echo $i; ?>]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" accept="image/jpeg, image/png, image/gif">
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <div id="atuacao-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página "Áreas de Atuação"</h2>
            <div class="mb-4">
                <label for="atuacao_titulo" class="block text-gray-700 font-medium mb-2">Título Principal da Página</label>
                <input type="text" id="atuacao_titulo" name="conteudo[atuacao_titulo][titulo]" value="<?php echo get_content('atuacao_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-6">
                <label for="atuacao_texto" class="block text-gray-700 font-medium mb-2">Texto Introdutório</label>
                <textarea id="atuacao_texto" name="conteudo[atuacao_titulo][texto]" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('atuacao_titulo', 'texto'); ?></textarea>
            </div>
            <hr class="my-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Caixas Interativas e Páginas de Conteúdo</h3>
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Serviço <?php echo $i; ?></h4>
                <div class="mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="conteudo[atuacao_card<?php echo $i; ?>_exibir][titulo]" value="nao">
                        <input type="checkbox" name="conteudo[atuacao_card<?php echo $i; ?>_exibir][titulo]" value="sim" <?php echo get_content("atuacao_card{$i}_exibir", 'titulo') === 'sim' ? 'checked' : ''; ?> class="form-checkbox h-5 w-5 text-teal-600">
                        <span class="ml-2 text-gray-700">Exibir este serviço na página de Atuação?</span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="font-semibold text-gray-600 mb-2">Aparência da Caixa</h5>
                        <div class="mb-4">
                            <label for="card<?php echo $i; ?>_titulo" class="block text-gray-700 font-medium mb-2">Título da Frente</label>
                            <input type="text" id="card<?php echo $i; ?>_titulo" name="conteudo[atuacao_card<?php echo $i; ?>_titulo][titulo]" value="<?php echo get_content("atuacao_card{$i}_titulo", 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label for="card<?php echo $i; ?>_imagem" class="block text-gray-700 font-medium mb-2">Nova Imagem da Frente</label>
                            <?php $imagem_atual = get_content("atuacao_card{$i}_titulo", 'imagem'); ?>
                            <?php if ($imagem_atual): ?>
                                <img src="../../uploads/site/<?php echo $imagem_atual; ?>" alt="Caixa <?php echo $i; ?> Imagem" class="w-32 h-auto rounded-md border mb-2">
                            <?php endif; ?>
                            <input type="hidden" name="conteudo[atuacao_card<?php echo $i; ?>_titulo][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                            <input type="file" id="card<?php echo $i; ?>_imagem" name="conteudo_imagem[atuacao_card<?php echo $i; ?>_titulo]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/jpeg, image/png, image/gif">
                        </div>
                        <div class="mb-4">
                            <label for="card<?php echo $i; ?>_texto" class="block text-gray-700 font-medium mb-2">Texto do Verso</label>
                            <textarea id="card<?php echo $i; ?>_texto" name="conteudo[atuacao_card<?php echo $i; ?>_titulo][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content("atuacao_card{$i}_titulo", 'texto'); ?></textarea>
                        </div>
                    </div>
                    <div>
                         <h5 class="font-semibold text-gray-600 mb-2">Conteúdo da Página de Detalhe</h5>
                         <div class="mb-4">
                            <label for="atuacao_p<?php echo $i; ?>_titulo" class="block text-gray-700 font-medium mb-2">Título da Página</label>
                            <input type="text" id="atuacao_p<?php echo $i; ?>_titulo" name="conteudo[atuacao_p<?php echo $i; ?>_titulo][titulo]" value="<?php echo get_content("atuacao_p{$i}_titulo", 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                         <div class="mb-4">
                            <label for="atuacao_p<?php echo $i; ?>_p1" class="block text-gray-700 font-medium mb-2">Parágrafo 1</label>
                            <textarea id="atuacao_p<?php echo $i; ?>_p1" name="conteudo[atuacao_p<?php echo $i; ?>_titulo][texto]" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content("atuacao_p{$i}_titulo", 'texto'); ?></textarea>
                        </div>
                         <div class="mb-4">
                            <label for="atuacao_p<?php echo $i; ?>_p2" class="block text-gray-700 font-medium mb-2">Parágrafo 2</label>
                            <textarea id="atuacao_p<?php echo $i; ?>_p2" name="conteudo[atuacao_p<?php echo $i; ?>_p2][texto]" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content("atuacao_p{$i}_p2", 'texto'); ?></textarea>
                        </div>
                         <div class="mb-4">
                            <label for="atuacao_p<?php echo $i; ?>_desfecho" class="block text-gray-700 font-medium mb-2">Desfecho</label>
                            <textarea id="atuacao_p<?php echo $i; ?>_desfecho" name="conteudo[atuacao_p<?php echo $i; ?>_desfecho][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content("atuacao_p{$i}_desfecho", 'texto'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
       <div id="sobre-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página "Sobre"</h2>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="sobre_objetivo_titulo" class="block text-gray-700 font-medium mb-2">Título da Secção "Objetivo"</label>
                <input type="text" id="sobre_objetivo_titulo" name="conteudo[sobre_objetivo_titulo][titulo]" value="<?php echo get_content('sobre_objetivo_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                <label for="sobre_objetivo_texto" class="block text-gray-700 font-medium mb-2">Texto "Objetivo"</label>
                <textarea id="sobre_objetivo_texto" name="conteudo[sobre_objetivo_titulo][texto]" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('sobre_objetivo_titulo', 'texto'); ?></textarea>
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="sobre_reflexao_imagem" class="block text-gray-700 font-medium mb-2">Nova Imagem para Reflexão</label>
                <?php $imagem_atual = get_content('sobre_reflexao_imagem', 'imagem'); ?>
                <?php if ($imagem_atual): ?><img src="../../uploads/site/<?php echo $imagem_atual; ?>" class="w-48 h-auto rounded-md border mb-2"><?php endif; ?>
                <input type="hidden" name="conteudo[sobre_reflexao_imagem][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                <input type="file" id="sobre_reflexao_imagem" name="conteudo_imagem[sobre_reflexao_imagem]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/jpeg, image/png, image/gif">
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="sobre_psicologa_foto" class="block text-gray-700 font-medium mb-2">Nova Foto da Psicóloga</label>
                <?php $imagem_atual = get_content('sobre_psicologa_foto', 'imagem'); ?>
                <?php if ($imagem_atual): ?><img src="../../uploads/site/<?php echo $imagem_atual; ?>" class="w-48 h-auto rounded-md border mb-2"><?php endif; ?>
                <input type="hidden" name="conteudo[sobre_psicologa_foto][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                <input type="file" id="sobre_psicologa_foto" name="conteudo_imagem[sobre_psicologa_foto]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/jpeg, image/png, image/gif">
                
                <label for="sobre_mim_texto" class="block text-gray-700 font-medium mb-2 mt-4">Texto em itálico ao lado da foto</label>
                <textarea id="sobre_mim_texto" name="conteudo[sobre_mim_texto][texto]" rows="7" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('sobre_mim_texto', 'texto'); ?></textarea>
                </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="sobre_quem_sou_titulo" class="block text-gray-700 font-medium mb-2">Título "Quem sou eu..."</label>
                <input type="text" id="sobre_quem_sou_titulo" name="conteudo[sobre_quem_sou_titulo][titulo]" value="<?php echo get_content('sobre_quem_sou_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                <label for="sobre_quem_sou_texto" class="block text-gray-700 font-medium mb-2">Texto "Quem sou eu..."</label>
                <textarea id="sobre_quem_sou_texto" name="conteudo[sobre_quem_sou_titulo][texto]" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('sobre_quem_sou_titulo', 'texto'); ?></textarea>
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="sobre_especializacoes_titulo" class="block text-gray-700 font-medium mb-2">Título "Minhas especializações"</label>
                <input type="text" id="sobre_especializacoes_titulo" name="conteudo[sobre_especializacoes_titulo][titulo]" value="<?php echo get_content('sobre_especializacoes_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                <label for="sobre_especializacoes_texto" class="block text-gray-700 font-medium mb-2">Texto "Minhas especializações"</label>
                <textarea id="sobre_especializacoes_texto" name="conteudo[sobre_especializacoes_titulo][texto]" rows="7" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('sobre_especializacoes_titulo', 'texto'); ?></textarea>
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                 <h3 class="text-xl font-semibold text-gray-700 mb-3">Modalidades de Atendimento</h3>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="border-t pt-4 mt-4">
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Modalidade <?php echo $i; ?></h4>
                    <label for="mod<?php echo $i; ?>_imagem" class="block text-gray-700 font-medium mb-2">Nova Imagem</label>
                    <?php $imagem_atual = get_content("sobre_mod{$i}_imagem", 'imagem'); ?>
                    <?php if ($imagem_atual): ?><img src="../../uploads/site/<?php echo $imagem_atual; ?>" class="w-48 h-auto rounded-md border mb-2"><?php endif; ?>
                    <input type="hidden" name="conteudo[sobre_mod<?php echo $i; ?>_imagem][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                    <input type="file" id="mod<?php echo $i; ?>_imagem" name="conteudo_imagem[sobre_mod<?php echo $i; ?>_imagem]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/jpeg, image/png, image/gif">
                    <label for="mod<?php echo $i; ?>_titulo" class="block text-gray-700 font-medium mb-2 mt-4">Título</label>
                    <input type="text" id="mod<?php echo $i; ?>_titulo" name="conteudo[sobre_mod<?php echo $i; ?>_imagem][titulo]" value="<?php echo get_content("sobre_mod{$i}_imagem", 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                    <label for="mod<?php echo $i; ?>_texto" class="block text-gray-700 font-medium mb-2">Texto</label>
                    <textarea id="mod<?php echo $i; ?>_texto" name="conteudo[sobre_mod<?php echo $i; ?>_imagem][texto]" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content("sobre_mod{$i}_imagem", 'texto'); ?></textarea>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div id="contato-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página de Contato</h2>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="contato_titulo" class="block text-gray-700 font-medium mb-2">Título Principal</label>
                <input type="text" id="contato_titulo" name="conteudo[contato_titulo][titulo]" value="<?php echo get_content('contato_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                <label for="contato_texto" class="block text-gray-700 font-medium mb-2">Texto Introdutório</label>
                <textarea id="contato_texto" name="conteudo[contato_titulo][texto]" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('contato_titulo', 'texto'); ?></textarea>
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="contato_endereco_sp" class="block text-gray-700 font-medium mb-2">Endereço (São Paulo)</label>
                <input type="text" id="contato_endereco_sp" name="conteudo[contato_endereco_sp][texto]" value="<?php echo get_content('contato_endereco_sp', 'texto'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="contato_endereco_cwb" class="block text-gray-700 font-medium mb-2">Endereço (Curitiba)</label>
                <input type="text" id="contato_endereco_cwb" name="conteudo[contato_endereco_cwb][texto]" value="<?php echo get_content('contato_endereco_cwb', 'texto'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <label for="contato_whatsapp" class="block text-gray-700 font-medium mb-2">Link do WhatsApp</label>
                <input type="text" id="contato_whatsapp" name="conteudo[contato_whatsapp][texto]" value="<?php echo get_content('contato_whatsapp', 'texto'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Ex: https://wa.me/5511999999999">
            </div>
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full bg-teal-600 text-white font-bold py-3 px-4 rounded-md hover:bg-teal-700">
                Guardar Alterações
            </button>
        </div>
    </form>
</div>

<script>
function changeTab(tabName) {
    // Esconder todos os conteúdos
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.add('hidden');
    });
    // Remover a classe ativa de todos os botões
    document.querySelectorAll('.tab-button').forEach(function(button) {
        button.classList.remove('active-tab');
    });
    // Mostrar o conteúdo do separador selecionado e ativar o botão
    document.getElementById(tabName + '-content').classList.remove('hidden');
    document.querySelector(`[data-tab-content='${tabName}-content']`).classList.add('active-tab');
    // Atualiza o valor do campo oculto
    document.getElementById('active_tab').value = tabName;
}

// Ao carregar a página, verifica se há um parâmetro de separador na URL e o ativa
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'geral'; // Define 'geral' como padrão
    const tabButton = document.querySelector(`[data-tab-content='${tab}-content']`);
    if (tabButton) {
        tabButton.click();
    } else {
        // Se a tab da URL for inválida, clica na primeira
        document.querySelector('.tab-button').click();
    }
    // Script para os seletores de cores
    const colorInputs = [
        { picker: 'site_cor_primaria', display: 'cor-hex-primaria' },
        { picker: 'site_cor_botao_bg', display: 'cor-hex-botao' },
        { picker: 'site_cor_header_bg', display: 'cor-hex-header' },
        { picker: 'site_cor_footer_bg', display: 'cor-hex-footer' }
    ];
    colorInputs.forEach(item => {
        const picker = document.getElementById(item.picker);
        const display = document.getElementById(item.display);
        if (picker && display) {
            picker.addEventListener('input', () => {
                display.textContent = picker.value;
            });
        }
    });
});
</script>
<style>
.tab-button {
    padding: 0.5rem 1rem;
    margin-right: 0.5rem;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    font-weight: 500;
    color: #4a5568; /* gray-700 */
    transition: all 0.2s;
}
.tab-button:hover {
    color: #2d3748; /* gray-800 */
}
.active-tab {
    border-bottom-color: var(--cor-primaria, #38b2ac); /* Usa a cor primária ou a padrão */
    color: #2d3848; /* gray-800 */
}
.tab-content:not(.hidden) {
    animation: fadeIn 0.5s;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<?php require_once 'templates/footer.php'; ?>
