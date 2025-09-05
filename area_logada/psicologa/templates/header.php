<?php
// Tenta buscar a cor primária da base de dados.
$cor_primaria = '#38b2ac'; // Cor padrão (teal-500)
try {
    // A função conectar() deve estar disponível a partir do ficheiro que inclui este header.
    if (!function_exists('conectar')) {
       // O caminho precisa de ser ajustado para subir três níveis a partir de /area_logada/psicologa/templates/
       require_once __DIR__ . '/../../../includes/db.php';
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
<html lang="pt-BR" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>Área da Psicóloga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --cor-primaria: <?php echo $cor_primaria; ?>;
        }
    </style>
</head>
<body class="h-full">
<div class="min-h-full">
    <nav x-data="{ open: false }" class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="painel.php">
                            <img class="h-10" src="https://animapsique.com.br/wp-content/uploads/2022/03/animapsique_logo70perc.png" alt="AnimaPsique Logotipo">
                        </a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="painel.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Painel</a>
                            <a href="pacientes.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Pacientes</a>
                            <a href="agenda.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Agenda</a>
                            <a href="salas_atendimento.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Salas</a>
                            <a href="mensagens.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Mensagens</a>
                            <a href="quizzes.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Quizzes</a>
                            <a href="publicacoes.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Publicações</a>
                            <a href="recibos.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Recibos</a>
                            <a href="configuracoes_site.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Configurações</a>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <div x-data="{ userMenuOpen: false }" class="relative ml-3">
                            <div>
                                <button @click="userMenuOpen = !userMenuOpen" type="button" class="flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Abrir menu do utilizador</span>
                                    <img class="h-8 w-8 rounded-full" src="https://placehold.co/256x256/E2E8F0/4A5568?text=P" alt="">
                                </button>
                            </div>
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="../../logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Sair</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button @click="open = !open" type="button" class="inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Abrir menu principal</span>
                        <svg :class="{'hidden': open, 'block': !open }" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        <svg :class="{'block': open, 'hidden': !open }" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="open" class="md:hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
                 <a href="painel.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Painel</a>
                 <a href="pacientes.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Pacientes</a>
                 <a href="agenda.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Agenda</a>
                 <a href="salas_atendimento.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Salas</a>
                 <a href="mensagens.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Mensagens</a>
                 <a href="quizzes.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Quizzes</a>
                 <a href="publicacoes.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Publicações</a>
                 <a href="recibos.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Recibos</a>
                 <a href="configuracoes_site.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Configurações</a>
            </div>
            <div class="border-t border-gray-700 pt-4 pb-3">
                <div class="mt-3 space-y-1 px-2">
                    <a href="../../logout.php" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Sair</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
