<?php
require_once 'config.php';
require_once 'includes/db.php';

$secoes = [
    'academicas_titulo_pagina',
    'academica1_titulo', 'academica1_texto',
    'academica2_titulo', 'academica2_texto',
    'academica3_titulo', 'academica3_texto'
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

function get_content($key, $field, $default = '') {
    global $conteudos;
    $value = isset($conteudos[$key][$field]) ? $conteudos[$key][$field] : $default;
    return $field === 'titulo' ? htmlspecialchars($value) : $value;
}

require_once 'templates/header_publico.php';
?>
<main class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <?php echo get_content('academicas_titulo_pagina', 'titulo', 'Publicações Acadêmicas'); ?>
        </h2>

        <div class="max-w-3xl mx-auto space-y-4">
            <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                    <span><?php echo get_content('academica1_titulo', 'titulo', 'Formação Profissional'); ?></span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose">
                    <?php echo get_content('academica1_texto', 'texto', 'Detalhes sobre a formação profissional.'); ?>
                </div>
            </div>
            <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                    <span><?php echo get_content('academica2_titulo', 'titulo', 'Artigos e Pesquisas'); ?></span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose">
                     <?php echo get_content('academica2_texto', 'texto', 'Detalhes sobre artigos e pesquisas.'); ?>
                </div>
            </div>
             <div x-data="{ open: false }" class="bg-gray-50 rounded-lg shadow-sm">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left text-lg font-semibold text-gray-700">
                    <span><?php echo get_content('academica3_titulo', 'titulo', 'Participação em Eventos'); ?></span>
                    <svg :class="{'transform rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="p-4 pt-0 text-gray-600 prose">
                     <?php echo get_content('academica3_texto', 'texto', 'Detalhes sobre participação em eventos.'); ?>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php require_once 'templates/footer_publico.php'; ?>
