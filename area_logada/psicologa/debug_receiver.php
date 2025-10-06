<?php
// Inicia a sessão para mensagens
session_start();

// Obtém o número de campos recebidos via POST e FILES
$total_post_vars = count($_POST);
$total_files_vars = count($_FILES);
$total_vars = $total_post_vars + $total_files_vars;

echo "<!DOCTYPE html>";
echo "<html lang='pt-br'>";
echo "<head><meta charset='UTF--g'><title>Teste de Debug</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-gray-100 flex items-center justify-center h-screen'>";
echo "<div class='bg-white p-8 rounded-lg shadow-lg text-center'>";
echo "<h1 class='text-2xl font-bold text-green-600'>SUCESSO!</h1>";
echo "<p class='mt-4 text-gray-700'>O pedido foi recebido com sucesso.</p>";
echo "<p class='mt-2 text-gray-900 font-semibold'>Total de Variáveis Recebidas: " . htmlspecialchars($total_vars) . "</p>";
echo "<div class='mt-6 text-left bg-gray-50 p-4 rounded'>";
echo "<h3 class='font-semibold mb-2'>POST Vars:</h3><pre class='text-xs'>" . htmlspecialchars(print_r($_POST, true)) . "</pre>";
echo "<h3 class='font-semibold mt-4 mb-2'>FILES Vars:</h3><pre class='text-xs'>" . htmlspecialchars(print_r($_FILES, true)) . "</pre>";
echo "</div>";
echo "</div>";
echo "</body></html>";
?>
