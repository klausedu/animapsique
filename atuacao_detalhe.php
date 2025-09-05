<?php
require_once 'config.php';
require_once 'includes/db.php';

// Validar o ID recebido para garantir que é um número entre 1 e 5
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1 || $id > 5) {
    header("Location: atuacao.php");
    exit;
}

// Define todas as secções necessárias para a página de detalhe
$secoes = [
    "atuacao_p{$id}_titulo",
    "atuacao_p{$id}_p2",
    "atuacao_p{$id}_desfecho"
];
$placeholders = rtrim(str_repeat('?,', count($secoes)), ',');

$conteudos = [];
try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT secao, titulo, texto FROM conteudo_site WHERE secao IN ($placeholders)");
    $stmt->execute($secoes);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $conteudos[$row['secao']] = $row;
    }
} catch (PDOException $e) {
    die("Erro ao buscar conteúdo da página: " . $e->getMessage());
}

// Função auxiliar para obter valores de forma segura
function get_content($key, $field, $default = '') {
    global $conteudos;
    return isset($conteudos[$key][$field]) ? htmlspecialchars($conteudos[$key][$field]) : $default;
}

// Função para formatar o texto com marcadores de ícone
function formatar_marcadores($texto) {
    if (empty(trim($texto))) return '';

    $linhas = preg_split("/\r\n|\n|\r/", $texto);
    $html = '';
    $em_lista = false;

    $icone_svg = '<svg class="w-5 h-5 mr-3 mt-1 flex-shrink-0 text-[var(--cor-primaria)]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

    foreach ($linhas as $linha) {
        $linha_trim = trim($linha);

        if (strpos($linha_trim, '*') === 0) {
            if (!$em_lista) {
                $html .= '<div class="space-y-3">'; // Inicia um grupo de itens de lista
                $em_lista = true;
            }
            $texto_item = trim(substr($linha_trim, 1));
            $html .= '<div class="flex items-start"><span class="flex-shrink-0">' . $icone_svg . '</span><span>' . htmlspecialchars($texto_item) . '</span></div>';
        } else {
            if ($em_lista) {
                $html .= '</div>'; // Fecha o grupo de itens de lista
                $em_lista = false;
            }
            if (!empty($linha_trim)) {
                $html .= '<p class="mb-4">' . htmlspecialchars($linha_trim) . '</p>';
            }
        }
    }

    if ($em_lista) {
        $html .= '</div>'; // Garante que o último grupo de lista é fechado
    }

    return $html;
}

require_once 'templates/header_publico.php';
?>

<main class="bg-white py-16">
    <div class="container mx-auto px-6 max-w-4xl">
        <article class="prose lg:prose-xl max-w-none">
            <h1 class="text-4xl font-bold text-gray-800 mb-6" style="color: var(--cor-primaria);">
                <?php echo get_content("atuacao_p{$id}_titulo", 'titulo', "Título do Serviço {$id}"); ?>
            </h1>

            <div class="text-lg text-gray-700 leading-relaxed space-y-4">
                <?php echo formatar_marcadores(get_content("atuacao_p{$id}_titulo", 'texto', 'Parágrafo 1 sobre o serviço.')); ?>
            </div>

            <hr class="my-8">

            <div class="text-lg text-gray-700 leading-relaxed space-y-4">
                <?php echo formatar_marcadores(get_content("atuacao_p{$id}_p2", 'texto', 'Parágrafo 2 sobre o serviço.')); ?>
            </div>

            <hr class="my-8">

            <div class="text-lg text-gray-700 leading-relaxed space-y-4 bg-gray-50 p-6 rounded-lg">
                <?php echo formatar_marcadores(get_content("atuacao_p{$id}_desfecho", 'texto', 'Desfecho sobre o serviço.')); ?>
            </div>

             <div class="mt-12 text-center">
                <a href="atuacao.php" class="inline-block text-lg font-semibold border-b-2 border-transparent hover:border-[var(--cor-primaria)] transition-colors" style="color: var(--cor-primaria);">
                    &larr; Voltar para Áreas de Atuação
                </a>
            </div>
        </article>
    </div>
</main>

<?php require_once 'templates/footer_publico.php'; ?>
