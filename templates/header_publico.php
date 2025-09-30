<?php
// Define cores padrão
$cores = [
    'primaria' => '#38b2ac', // teal-500
    'header_bg' => '#ffffff', // white
    'footer_bg' => '#1f2937',  // gray-800
    'botao_bg' => '#38b2ac'  // Cor padrão do botão
];

try {
    require_once __DIR__ . '/../includes/db.php';
    $pdo_cores = conectar();
    // Adiciona a busca pela nova cor do botão
    $stmt_cores = $pdo_cores->query("SELECT secao, texto FROM conteudo_site WHERE secao IN ('site_cor_primaria', 'site_cor_header_bg', 'site_cor_footer_bg', 'site_cor_botao_bg')");
    $resultados = $stmt_cores->fetchAll(PDO::FETCH_KEY_PAIR);
    
    if (isset($resultados['site_cor_primaria'])) $cores['primaria'] = htmlspecialchars($resultados['site_cor_primaria']);
    if (isset($resultados['site_cor_header_bg'])) $cores['header_bg'] = htmlspecialchars($resultados['site_cor_header_bg']);
    if (isset($resultados['site_cor_footer_bg'])) $cores['footer_bg'] = htmlspecialchars($resultados['site_cor_footer_bg']);
    // Guarda a nova cor do botão
    if (isset($resultados['site_cor_botao_bg'])) $cores['botao_bg'] = htmlspecialchars($resultados['site_cor_botao_bg']);

} catch (Exception $e) { /* Usa cores padrão em caso de erro */ }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimaPsique - Psicologia Clínica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: <?php echo $cores['primaria']; ?>;
            --cor-header-bg: <?php echo $cores['header_bg']; ?>;
            --cor-footer-bg: <?php echo $cores['footer_bg']; ?>;
            --cor-botao-bg: <?php echo $cores['botao_bg']; ?>;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header style="background-color: var(--cor-header-bg);" class="shadow-md sticky top-0 z-50">
        <nav x-data="{ open: false }" class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="text-xl font-semibold text-gray-700">
                    <a href="index.php">
                        <img src="https://animapsique.com.br/wp-content/uploads/2022/03/animapsique_logo70perc.png" alt="AnimaPsique Logotipo" class="h-12">
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-3">
                    <a href="index.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Início</a>
                    <a href="sobre.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Quem Sou</a>
                    <a href="atuacao.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Áreas de Atuação</a>
                    <a href="publicacoes_academicas.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Publicações Acadêmicas</a>
                    <a href="reportagens.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Reportagens</a>
                    <a href="livros.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Livros</a>
                    <a href="contato.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Contato</a>
                    <a href="login.php" style="background-color: var(--cor-botao-bg);" class="py-2 px-4 text-white rounded-full hover:opacity-90 transition-opacity">Área do Paciente</a>
                </div>
                <div class="md:hidden">
                    <button @click="open = !open" class="text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div x-show="open" @click.away="open = false" class="md:hidden mt-3">
                <a href="index.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Início</a>
                <a href="sobre.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Quem Sou</a>
                <a href="atuacao.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Áreas de Atuação</a>
                <a href="publicacoes_academicas.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Publicações Acadêmicas</a>
                <a href="reportagens.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Reportagens</a>
                <a href="livros.php" class="py-2 px-3 text-gray-700 hover:text-[var(--cor-primaria)]">Livros</a>
                <a href="contato.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Contato</a>
                <a href="login.php" style="background-color: var(--cor-botao-bg);" class="block mt-2 py-2 px-4 text-sm text-white rounded-md text-center">Área do Paciente</a>
            </div>
        </nav>
    </header>
