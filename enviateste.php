<?php
// Inclui os ficheiros necessários
require_once 'config.php';
require_once 'includes/email.php';

$mensagem_feedback = '';

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_teste = filter_input(INPUT_POST, 'email_teste', FILTER_VALIDATE_EMAIL);

    if ($email_teste) {
        $assunto = "E-mail de Teste da Plataforma";
        $corpo = "<h1>Olá!</h1><p>Este é um e-mail de teste enviado a partir do seu site para verificar se as configurações de SMTP estão a funcionar corretamente.</p><p>Se recebeu este e-mail, está tudo a funcionar!</p>";

        if (enviar_email($email_teste, 'Destinatário de Teste', $assunto, $corpo)) {
            $mensagem_feedback = "<p class='text-green-600'>E-mail de teste enviado com sucesso para {$email_teste}!</p>";
        } else {
            $mensagem_feedback = "<p class='text-red-600'>Falha ao enviar o e-mail. Verifique as configurações no config.php e o log de erros do servidor.</p>";
        }
    } else {
        $mensagem_feedback = "<p class='text-red-600'>Por favor, insira um endereço de e-mail válido.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Envio de E-mail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-teal-700">Teste de Envio de E-mail</h2>
                <p class="text-gray-600 mt-2">Use este formulário para verificar se as suas configurações de e-mail (SMTP) estão corretas.</p>
            </div>

            <?php if ($mensagem_feedback): ?>
                <div class="mb-6 p-4 rounded-md <?php echo strpos($mensagem_feedback, 'sucesso') !== false ? 'bg-green-100' : 'bg-red-100'; ?>">
                    <?php echo $mensagem_feedback; ?>
                </div>
            <?php endif; ?>

            <form action="enviateste.php" method="POST">
                <div class="mb-6">
                    <label for="email_teste" class="block text-gray-700 text-sm font-bold mb-2">Enviar e-mail de teste para:</label>
                    <input type="email" id="email_teste" name="email_teste" required class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="digite.o.email@aqui.com">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Enviar E-mail
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
