<?php
// ======================================================================
// CÓDIGO DE DIAGNÓSTICO
// ======================================================================
echo "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'><title>Debug de Dados Recebidos</title>";
echo "<style>body { font-family: sans-serif; padding: 20px; background-color: #f4f4f9; } .container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); } h1 { color: #333; } pre { background-color: #eee; padding: 15px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; }</style>";
echo "</head><body><div class='container'>";
echo "<h1>Debug dos Dados Recebidos</h1>";
echo "<p>Estes são os dados que o formulário enviou para serem guardados. Se os campos da sua aba ativa estiverem aqui, o problema está na lógica de gravação. Se estiverem em falta, o problema está no JavaScript do formulário.</p>";

echo "<h2>Dados de Texto (\$_POST):</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Dados de Ficheiros (\$_FILES):</h2>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

echo "</div></body></html>";

// A execução para aqui intencionalmente para análise.
exit;


// ======================================================================
// SEU CÓDIGO ORIGINAL (NÃO SERÁ EXECUTADO DURANTE O DEBUG)
// ======================================================================
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';
// ... (o resto do seu ficheiro salvar_opcoes.php)
?>
