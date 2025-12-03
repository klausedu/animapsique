<?php
require_once 'config.php';
require_once 'includes/db.php';

// Define todas as secções necessárias para esta página
$secoes = ['contato_titulo', 'contato_endereco_sp', 'contato_endereco_cwb', 'contato_whatsapp'];
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
    die("Erro ao buscar conteúdos: " . $e->getMessage());
}

// Função auxiliar para obter valores de forma segura
function get_content($key, $field, $default = '') {
    global $conteudos;
    if (!isset($conteudos[$key][$field])) {
        return $default;
    }
    
    $content = $conteudos[$key][$field];
    
    // Se for o campo 'texto', permite HTML (decodifica entidades se necessário)
    if ($field === 'texto') {
        return html_entity_decode($content);
    }
    
    // Para outros campos (como título), mantém a segurança
    return htmlspecialchars($content);
}

require_once 'templates/header_publico.php';
?>

<main class="py-16 md:py-24">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold" style="color: var(--cor-primaria);">
                <?php echo get_content('contato_titulo', 'titulo', 'Entre em Contato'); ?>
            </h1>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                <?php echo get_content('contato_titulo', 'texto', 'Se você está buscando apoio psicológico, preencha o formulário abaixo para agendar uma primeira conversa.'); ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Solicitar Primeira Sessão</h2>
                <form action="processa_contato.php" method="POST">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                            <input type="text" name="nome" id="nome" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--cor-primaria)] focus:ring-[var(--cor-primaria)]">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--cor-primaria)] focus:ring-[var(--cor-primaria)]">
                        </div>
                        <div>
                            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone (WhatsApp)</label>
                            <input type="tel" name="telefone" id="telefone" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--cor-primaria)] focus:ring-[var(--cor-primaria)]">
                        </div>
                        <div>
                            <label for="idade" class="block text-sm font-medium text-gray-700">Idade</label>                            <input type="number" name="idade" id="idade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--cor-primaria)] focus:ring-[var(--cor-primaria)]">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="motivo" class="block text-sm font-medium text-gray-700">Principais motivos da busca</label>
                            <textarea name="motivo" id="motivo" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--cor-primaria)] focus:ring-[var(--cor-primaria)]"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent px-6 py-3 text-base font-medium text-white shadow-sm hover:opacity-90 transition-opacity" style="background-color: var(--cor-botao-bg);">
                            Enviar Solicitação
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800">Atendimento Online</h3>
                    <p class="text-gray-600 mt-2">Realizado através de videochamada, com a mesma segurança e sigilo do presencial.</p>
                    <a href="<?php echo get_content('contato_whatsapp', 'texto', '#'); ?>" target="_blank" class="mt-4 inline-block font-semibold hover:opacity-80" style="color: var(--cor-primaria);">Fale comigo no WhatsApp &rarr;</a>
                </div> 
                
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800">Endereços</h3>
                    <div class="mt-4">
                        <p class="font-semibold">São Paulo</p>
                        <p class="text-gray-600"><?php echo get_content('contato_endereco_sp', 'texto', 'Endereço de São Paulo'); ?></p>
                    </div> 
                    
                    <div class="mt-4">
                        <p class="font-semibold">Curitiba</p>
                        <p class="text-gray-600"><?php echo get_content('contato_endereco_cwb', 'texto', 'Endereço de Curitiba'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'templates/footer_publico.php'; ?>