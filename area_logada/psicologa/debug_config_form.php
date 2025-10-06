<?php
// --- DEBUG DE CONFIGURACOES_SITE.PHP ---
// ETAPA 1: O MÍNIMO ABSOLUTO

// Incluímos apenas o essencial para a sessão funcionar
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Debug - Etapa 1</title>
    </head>
<body>
    <div style="font-family: sans-serif; padding: 20px;">
        <h1>Debug: Teste da Estrutura Mínima do Formulário</h1>
        <p>Esta página contém apenas a tag do formulário e um botão. Ela aponta para o nosso `debug_receiver.php` que já sabemos que funciona.</p>

        <form action="debug_receiver.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="teste_simples" value="etapa1_ok">

            <button type="submit" style="padding: 10px 20px; font-size: 16px; background-color: #28a745; color: white; border: none; cursor: pointer;">
                Executar Teste da Etapa 1
            </button>
        </form>
    </div>
</body>
</html>
