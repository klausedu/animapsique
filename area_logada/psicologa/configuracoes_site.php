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
    // Retorna o valor com htmlspecialchars para segurança, exceto para campos de texto rico (richtext)
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Função para obter conteúdo HTML (de richtext) sem escapar as tags
function get_html_content($key, $field, $default = '') {
    global $conteudos;
    return isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
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
        <nav class="flex flex-wrap -mb-px space-x-2" aria-label="Tabs">
            <button onclick="changeTab('geral')" class="tab-button" data-tab-content="geral-content">Geral</button>
            <button onclick="changeTab('index')" class="tab-button" data-tab-content="index-content">Página Inicial</button>
            <button onclick="changeTab('sobre')" class="tab-button" data-tab-content="sobre-content">Sobre</button>
            <button onclick="changeTab('atuacao')" class="tab-button" data-tab-content="atuacao-content">Atuação</button>
            <button onclick="changeTab('academicas')" class="tab-button" data-tab-content="academicas-content">Pub. Acadêmicas</button>
            <button onclick="changeTab('livros')" class="tab-button" data-tab-content="livros-content">Livros</button>
            <button onclick="changeTab('contato')" class="tab-button" data-tab-content="contato-content">Contato</button>
        </nav>
    </div>

    <form action="processa_configuracoes.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        <input type="hidden" name="active_tab" id="active_tab" value="geral">

        <div id="geral-content" class="tab-content">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Aparência Geral</h2>
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
            </div>
        </div>
        
        <div id="index-content" class="tab-content hidden">
           <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página Inicial</h2>
           </div>

        <div id="sobre-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página "Sobre"</h2>
            </div>

        <div id="atuacao-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página "Áreas de Atuação"</h2>
            </div>

        <div id="academicas-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página "Publicações Acadêmicas"</h2>
            <div class="mb-4">
                <label for="academicas_titulo_pagina" class="block text-gray-700 font-medium mb-2">Título da Página</label>
                <input type="text" id="academicas_titulo_pagina" name="conteudo[academicas_titulo_pagina][titulo]" value="<?php echo get_content('academicas_titulo_pagina', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <hr class="my-6">
            <div class="mb-4 p-4 border rounded-md bg-gray-50">
                <label for="academica1_titulo" class="block text-gray-700 font-medium mb-2">Título 1</label>
                <input type="text" id="academica1_titulo" name="conteudo[academica1_titulo][titulo]" value="<?php echo get_content('academica1_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <label for="academica1_texto" class="block text-gray-700 font-medium mb-2 mt-2">Texto Expansível 1</label>
                <textarea id="academica1_texto" name="conteudo[academica1_texto][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_html_content('academica1_texto', 'texto'); ?></textarea>
            </div>
            <div class="mb-4 p-4 border rounded-md bg-gray-50">
                <label for="academica2_titulo" class="block text-gray-700 font-medium mb-2">Título 2</label>
                <input type="text" id="academica2_titulo" name="conteudo[academica2_titulo][titulo]" value="<?php echo get_content('academica2_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <label for="academica2_texto" class="block text-gray-700 font-medium mb-2 mt-2">Texto Expansível 2</label>
                <textarea id="academica2_texto" name="conteudo[academica2_texto][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_html_content('academica2_texto', 'texto'); ?></textarea>
            </div>
            <div class="mb-4 p-4 border rounded-md bg-gray-50">
                <label for="academica3_titulo" class="block text-gray-700 font-medium mb-2">Título 3</label>
                <input type="text" id="academica3_titulo" name="conteudo[academica3_titulo][titulo]" value="<?php echo get_content('academica3_titulo', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <label for="academica3_texto" class="block text-gray-700 font-medium mb-2 mt-2">Texto Expansível 3</label>
                <textarea id="academica3_texto" name="conteudo[academica3_texto][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_html_content('academica3_texto', 'texto'); ?></textarea>
            </div>
        </div>

        <div id="livros-content" class="tab-content hidden">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página "Livros"</h2>
            <div class="mb-4">
                <label for="livros_titulo_pagina" class="block text-gray-700 font-medium mb-2">Título Principal da Página</label>
                <input type="text" id="livros_titulo_pagina" name="conteudo[livros_titulo_pagina][titulo]" value="<?php echo get_content('livros_titulo_pagina', 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <hr class="my-6">
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="border p-4 rounded-md mb-4 bg-gray-50">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Livro <?php echo $i; ?></h4>
                <div class="mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="conteudo[livro<?php echo $i; ?>_exibir][titulo]" value="nao">
                        <input type="checkbox" name="conteudo[livro<?php echo $i; ?>_exibir][titulo]" value="sim" <?php echo get_content("livro{$i}_exibir", 'titulo') === 'sim' ? 'checked' : ''; ?> class="form-checkbox h-5 w-5 text-teal-600">
                        <span class="ml-2 text-gray-700">Exibir este livro?</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label for="livro<?php echo $i; ?>_imagem" class="block text-gray-700 font-medium mb-2">Nova Imagem da Capa</label>
                    <?php $imagem_atual = get_content("livro{$i}_imagem", 'imagem'); ?>
                    <?php if ($imagem_atual): ?><img src="../../uploads/site/<?php echo $imagem_atual; ?>" class="w-32 h-auto rounded-md border mb-2"><?php endif; ?>
                    <input type="hidden" name="conteudo[livro<?php echo $i; ?>_imagem][imagem_atual]" value="<?php echo $imagem_atual; ?>">
                    <input type="file" id="livro<?php echo $i; ?>_imagem" name="conteudo_imagem[livro<?php echo $i; ?>_imagem]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/jpeg, image/png, image/gif">
                </div>
                <div class="mb-4">
                    <label for="livro<?php echo $i; ?>_titulo" class="block text-gray-700 font-medium mb-2">Título do Livro</label>
                    <input type="text" id="livro<?php echo $i; ?>_titulo" name="conteudo[livro<?php echo $i; ?>_titulo][titulo]" value="<?php echo get_content("livro{$i}_titulo", 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="livro<?php echo $i; ?>_texto" class="block text-gray-700 font-medium mb-2">Descrição Curta</label>
                    <textarea id="livro<?php echo $i; ?>_texto" name="conteudo[livro<?php echo $i; ?>_titulo][texto]" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_html_content("livro{$i}_titulo", 'texto'); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="livro<?php echo $i; ?>_link" class="block text-gray-700 font-medium mb-2">Link "Saiba Mais"</label>
                    <input type="url" id="livro<?php echo $i; ?>_link" name="conteudo[livro<?php echo $i; ?>_link][titulo]" value="<?php echo get_content("livro{$i}_link", 'titulo'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="https://exemplo.com/livro">
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <div id="contato-content" class="tab-content hidden">
           <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página de Contato</h2>
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
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active-tab'));
    document.getElementById(tabName + '-content').classList.remove('hidden');
    document.querySelector(`[data-tab-content='${tabName}-content']`).classList.add('active-tab');
    document.getElementById('active_tab').value = tabName;
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'geral';
    const tabButton = document.querySelector(`[data-tab-content='${tab}-content']`);
    if (tabButton) {
        tabButton.click();
    } else {
        document.querySelector('.tab-button').click();
    }
    const colorInputs = [
        { picker: 'site_cor_primaria', display: 'cor-hex-primaria' },
        { picker: 'site_cor_botao_bg', display: 'cor-hex-botao' }
    ];
    colorInputs.forEach(item => {
        const picker = document.getElementById(item.picker);
        const display = document.getElementById(item.display);
        if (picker && display) {
            picker.addEventListener('input', () => { display.textContent = picker.value; });
        }
    });
});
</script>
<style>
.tab-button {
    padding: 0.5rem 1rem;
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
    border-bottom-color: var(--cor-primaria, #38b2ac);
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
