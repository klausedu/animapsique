<?php
// --- DEBUG DE CONFIGURACOES_SITE.PHP ---
// ETAPA 2: LÓGICA PHP E CAMPOS DO FORMULÁRIO (SEM JS/CSS)

require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// A sua lógica original para buscar os dados
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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Debug - Etapa 2</title>
</head>
<body style="font-family: sans-serif; padding: 20px;">
    <h1>Debug: Teste com Lógica PHP e Campos Reais</h1>
    <p>Adicionámos a busca de dados do PHP e todos os campos do seu formulário original. No entanto, ainda não há CSS nem JavaScript (especialmente o editor de texto).</p>

    <form action="debug_receiver.php" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="active_tab" id="active_tab" value="geral">

        <h3>Geral</h3>
        <input type="color" name="conteudo_site_cor_primaria_texto" value="<?php echo get_content('site_cor_primaria', 'texto', '#38b2ac'); ?>">
        <input type="color" name="conteudo_site_cor_botao_bg_texto" value="<?php echo get_content('site_cor_botao_bg', 'texto', '#38b2ac'); ?>">
        <textarea name="conteudo_banner_inicio_titulo"><?php echo get_content('banner_inicio', 'titulo'); ?></textarea>
        <textarea name="conteudo_banner_inicio_texto"><?php echo get_content('banner_inicio', 'texto'); ?></textarea>
        <input type="file" name="imagem_banner_inicio">
        <input type="hidden" name="imagem_atual_banner_inicio" value="<?php echo get_content('banner_inicio', 'imagem'); ?>">

        <h3>Atuação</h3>
        <input type="text" name="conteudo_atuacao_titulo_titulo" value="<?php echo get_content('atuacao_titulo', 'titulo'); ?>">
        <textarea name="conteudo_atuacao_titulo_texto"><?php echo get_content('atuacao_titulo', 'texto'); ?></textarea>


        <hr style="margin: 20px 0;">
        <button type="submit" style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; cursor: pointer;">
            Executar Teste da Etapa 2
        </button>
    </form>
</body>
</html>
