<?php
// Inicia a sessão para podermos ver as mensagens
session_start();

// Desativa a visualização de erros para o utilizador final, mas regista-os
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

echo "<h1>Diagnóstico de Submissão</h1>";
echo "<p>Se está a ver esta página, o acesso ao ficheiro PHP funcionou. O problema está nos dados enviados ou no processamento.</p>";

echo "<h2>Dados Recebidos (\$_POST):</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Ficheiros Recebidos (\$_FILES):</h2>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

// Tenta uma operação simples de ficheiro para verificar permissões
$teste_dir = __DIR__ . '/../../uploads/site/';
echo "<h2>Teste de Escrita no Diretório:</h2>";
echo "<p>A tentar escrever em: " . realpath($teste_dir) . "</p>";

if (is_writable($teste_dir)) {
    echo "<p style='color:green;'>Sucesso! O diretório tem permissão de escrita.</p>";
} else {
    echo "<p style='color:red;'>Falha! O diretório não tem permissão de escrita. Verifique as permissões (devem ser 755 ou 775).</p>";
}

exit;
?>
