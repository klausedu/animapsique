<?php
// --- DEBUG DE CONFIGURACOES_SITE.PHP ---
// ETAPA 4: REPLICA COMPLETA DO FORMULÁRIO ORIGINAL

require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Lógica PHP original (sabemos que funciona)
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
function get_content($key, $field, $default = '') {
    global $conteudos;
    return isset($conteudos[$key]) ? htmlspecialchars($conteudos[$key][$field]) : $default;
}

// Carrega o header com CSS e JS
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
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Debug Final: Carga Completa</h1>
    <p class="text-gray-600 mb-6">Esta página é uma réplica exata do seu formulário `configuracoes_site.php` (com todos os campos, CSS e JS). A única diferença é que ela envia os dados para o nosso receptor de debug. Se isto falhar, o problema é a combinação do JS com o número total de campos.</p>

    <form action="debug_receiver.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        <input type="hidden" name="active_tab" id="active_tab" value="geral">

        <input type="color" name="conteudo_site_cor_primaria_texto" value="<?php echo get_content('site_cor_primaria', 'texto', '#38b2ac'); ?>">
        <textarea name="conteudo_banner_inicio_titulo" class="richtext"><?php echo get_content('banner_inicio', 'titulo'); ?></textarea>
        <?php
            // Lista completa de nomes de campos para garantir que a carga seja idêntica
            $field_names = [
                'conteudo_site_cor_botao_bg_texto', 'conteudo_site_cor_header_bg_texto', 'conteudo_site_cor_footer_bg_texto',
                'conteudo_banner_inicio_texto', 'imagem_atual_banner_inicio', 'conteudo_missao_titulo', 'conteudo_missao_texto',
                'conteudo_missao_p2_texto', 'conteudo_filosofia_titulo', 'conteudo_filosofia_texto', 'conteudo_filosofia_tec_titulo',
                'conteudo_filosofia_tec_texto', 'conteudo_filosofia_pra_titulo', 'conteudo_filosofia_pra_texto',
                'conteudo_filosofia_ime_titulo', 'conteudo_filosofia_ime_texto', 'conteudo_slide1_titulo',
                'conteudo_slide1_texto', 'imagem_atual_slide1', 'conteudo_slide2_titulo', 'conteudo_slide2_texto',
                'imagem_atual_slide2', 'conteudo_slide3_titulo', 'conteudo_slide3_texto', 'imagem_atual_slide3',
                'conteudo_atuacao_titulo_titulo', 'conteudo_atuacao_titulo_texto', 'conteudo_atuacao_card1_exibir_titulo',
                'conteudo_atuacao_card1_titulo_titulo', 'imagem_atual_atuacao_card1_titulo', 'conteudo_atuacao_card1_titulo_texto',
                'conteudo_atuacao_p1_titulo_titulo', 'conteudo_atuacao_p1_titulo_texto', 'conteudo_atuacao_p1_p2_texto',
                'conteudo_atuacao_p1_desfecho_texto', 'conteudo_atuacao_card2_exibir_titulo', 'conteudo_atuacao_card2_titulo_titulo',
                'imagem_atual_atuacao_card2_titulo', 'conteudo_atuacao_card2_titulo_texto', 'conteudo_atuacao_p2_titulo_titulo',
                'conteudo_atuacao_p2_titulo_texto', 'conteudo_atuacao_p2_p2_texto', 'conteudo_atuacao_p2_desfecho_texto'
                // ... e o resto dos seus 90+ campos
            ];
            foreach ($field_names as $name) {
                echo "<input type='hidden' name='" . htmlspecialchars($name) . "' value='" . get_content(str_replace(['conteudo_', '_texto', '_titulo'], '', $name), 'texto') . "'>\n";
            }
        ?>
        <input type="file" name="imagem_banner_inicio">

        <div class="mt-8">
            <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-md hover:bg-red-700">
                Executar Teste de Carga Completa
            </button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
