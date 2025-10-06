<?php
// --- DEBUG DE CONFIGURACOES_SITE.PHP ---
// ETAPA 3: ADICIONAR O JAVASCRIPT AO CÓDIGO FUNCIONAL

require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// A sua lógica original para buscar os dados (sabemos que funciona)
$conteudos = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT secao, titulo, texto, imagem FROM conteudo_site");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteudos[$row['secao']] = $row;
    }
} catch (PDOException $e) {
    die("Erro ao buscar conteúdos: ". $e->getMessage());
}
function get_content($key, $field, $default = '') {
    global $conteudos;
    return isset($conteudos[$key]) ? htmlspecialchars($conteudos[$key][$field]) : $default;
}

// Vamos adicionar o header para carregar o CSS, pois o JS pode depender dele
require_once 'templates/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/hugerte@1.0.9/hugerte.min.js"></script>
<script>
  // A inicialização exata do seu ficheiro original
  hugerte.init({
    selector: 'textarea.richtext',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 300,
  });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Debug: Teste com JavaScript Ativo</h1>
    <p class="text-gray-600 mb-6">Esta é a Etapa 2 que funcionou, mas agora com o CSS e o JavaScript (incluindo o editor de texto Hugerte) do seu ficheiro original. Se este teste falhar, o problema está na forma como o JavaScript interage com o formulário.</p>

    <form action="debug_receiver.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Banner Principal</h2>
        <div class="mb-4">
            <label for="banner_inicio_titulo" class="block text-gray-700 font-medium mb-2">Título do Banner (com editor)</label>
            <textarea id="banner_inicio_titulo" name="conteudo_banner_inicio_titulo" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('banner_inicio', 'titulo'); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="banner_inicio_texto" class="block text-gray-700 font-medium mb-2">Texto do Banner (com editor)</
            <textarea id="banner_inicio_texto" name="conteudo_banner_inicio_texto" class="w-full px-3 py-2 border border-gray-300 rounded-md richtext"><?php echo get_content('banner_inicio', 'texto'); ?></textarea>
        </div>
        <div class="mb-6">
            <label for="imagem_banner_inicio" class="block text-gray-700 font-medium mb-2">Nova Imagem do Banner</label>
            <input type="file" id="imagem_banner_inicio" name="imagem_banner_inicio" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0">
            <input type="hidden" name="imagem_atual_banner_inicio" value="<?php echo get_content('banner_inicio', 'imagem'); ?>">
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-md hover:bg-red-700">
                Executar Teste da Etapa 3
            </button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
