<?php
// Tenta buscar a cor primária da base de dados.
$cor_primaria = '#38b2ac'; // Cor padrão (teal-500)
try {
    // A função conectar() deve estar disponível a partir do ficheiro que inclui este header.
    if (!function_exists('conectar')) {
       require_once __DIR__ . '/../includes/db.php';
    }
    $pdo_header = conectar();
    $stmt_header = $pdo_header->prepare("SELECT texto FROM conteudo_site WHERE secao = ?");
    $stmt_header->execute(['site_cor_primaria']);
    $resultado = $stmt_header->fetch(PDO::FETCH_ASSOC);
    if ($resultado && !empty($resultado['texto'])) {
        $cor_primaria = htmlspecialchars($resultado['texto']);
    }
} catch (Exception $e) {
    // Se houver um erro, simplesmente usa a cor padrão.
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimaPsique - Psicologia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        :root {
            --cor-primaria: <?php echo $cor_primaria; ?>;
        }
        .bg-teal-500 { background-color: var(--cor-primaria); }
        .bg-teal-600 { background-color: var(--cor-primaria); }
        .hover\:bg-teal-600:hover { filter: brightness(90%); }
        .hover\:bg-teal-700:hover { filter: brightness(90%); }
        .text-teal-500, .text-teal-600, .text-teal-700 { color: var(--cor-primaria); }
        .border-teal-500 { border-color: var(--cor-primaria); }
        .ring-teal-500:focus { --tw-ring-color: var(--cor-primaria); }
        .focus\:ring-teal-500:focus { --tw-ring-color: var(--cor-primaria); }
        .active-tab { border-bottom-color: var(--cor-primaria); }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-white shadow-md" x-data="{ open: false }">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php">
                    <img src="https://animapsique.com.br/wp-content/uploads/2022/03/animapsique_logo70perc.png" alt="Logotipo AnimaPsique" class="h-12">
                </a>
                
                <nav class="hidden md:flex space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-teal-500">Início</a>
                    <a href="sobre.php" class="text-gray-600 hover:text-teal-500">Quem Sou</a>
                    <a href="atuacao.php" class="text-gray-600 hover:text-teal-500">Áreas de Atuação</a>
                    <a href="publicacoes.php" class="text-gray-600 hover:text-teal-500">Publicações</a>
                    <a href="contato.php" class="text-gray-600 hover:text-teal-500">Contato</a>
                    <a href="login.php" class="bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600">Área do Paciente</a>
                </nav>

                <div class="md:hidden">
                    <button @click="open = !open" class="text-gray-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="open" class="md:hidden mt-4">
                <a href="index.php" class="block text-gray-600 py-2 hover:text-teal-500">Início</a>
                <a href="sobre.php" class="block text-gray-600 py-2 hover:text-teal-500">Quem Sou</a>
                <a href="atuacao.php" class="block text-gray-600 py-2 hover:text-teal-500">Áreas de Atuação</a>
                <a href="publicacoes.php" class="block text-gray-600 py-2 hover:text-teal-500">Publicações</a>
                <a href="contato.php" class="block text-gray-600 py-2 hover:text-teal-500">Contato</a>
                <a href="login.php" class="block bg-teal-500 text-white mt-2 px-4 py-2 rounded-md hover:bg-teal-600 text-center">Área do Paciente</a>
            </div>
        </div>
    </header>
