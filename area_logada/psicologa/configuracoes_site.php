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
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline striethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
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

    <form id="config-form" action="salvar_opcoes.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        <input type="hidden" name="active_tab" id="active_tab" value="geral">

        <div id="geral-content" class="tab-content">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Aparência e Banner Principal</h2>
            <div class="mb-4">
                <label for="banner_inicio_titulo" class="block text-gray-700 font-medium mb-2">Título do Banner</label>
                <textarea id="banner_inicio_titulo" name="conteudo_banner_inicio_titulo" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('banner_inicio', 'titulo'); ?></textarea>
            </div>
            </div>

        <div id="missao-content" class="tab-content hidden">
            </div>

        <div id="filosofia-content" class="tab-content hidden">
            </div>

        <div id="slides-content" class="tab-content hidden">
            </div>
        
        <div id="atuacao-content" class="tab-content hidden">
            </div>
        
        <div id="sobre-content" class="tab-content hidden">
            </div>
        
        <div id="contato-content" class="tab-content hidden">
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
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.add('hidden');
    });
    document.querySelectorAll('.tab-button').forEach(function(button) {
        button.classList.remove('active-tab');
    });
    document.getElementById(tabName + '-content').classList.remove('hidden');
    document.querySelector(`[data-tab-content='${tabName}-content']`).classList.add('active-tab');
    document.getElementById('active_tab').value = tabName;
}

// ======================================================================
// NOVA LÓGICA PARA SUBMETER APENAS A ABA ATIVA
// ======================================================================
document.getElementById('config-form').addEventListener('submit', function(event) {
    // Sincroniza o conteúdo do editor de texto antes de desativar os campos
    if (typeof hugerte !== 'undefined') {
        hugerte.triggerSave();
    }

    // Obtém o ID do conteúdo da aba ativa
    const activeTabContentId = document.getElementById('active_tab').value + '-content';
    
    // Itera por todos os elementos do formulário
    const formElements = this.elements;
    for (let i = 0; i < formElements.length; i++) {
        const element = formElements[i];
        
        // Ignora botões e campos que não têm nome
        if (!element.name || element.tagName === 'BUTTON') {
            continue;
        }

        // Encontra o 'tab-content' pai mais próximo do elemento
        const parentTab = element.closest('.tab-content');
        
        // Se o elemento estiver dentro de um 'tab-content' que não seja o ativo, desativa-o
        if (parentTab && parentTab.id !== activeTabContentId) {
            element.disabled = true;
        }
    }
    // O formulário agora será enviado apenas com os campos da aba ativa
});
// ======================================================================

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'geral';
    const tabButton = document.querySelector(`[data-tab-content='${tab}-content']`);
    if (tabButton) {
        tabButton.click();
    } else {
        document.querySelector('.tab-button').click();
    }
});
</script>

<?php require_once 'templates/footer.php'; ?>
