<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-teal-700">Recuperar Senha</h2>
                <p class="text-gray-600 mt-2">Digite seu e-mail e enviaremos um link para você redefinir sua senha.</p>
            </div>
            <form action="processa_esqueci_senha.php" method="POST">
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                    <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700">
                </div>
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded">
                    Enviar Link de Recuperação
                </button>
                <div class="text-center mt-6">
                    <a href="login.php" class="inline-block font-bold text-sm text-teal-600 hover:text-teal-800">&larr; Voltar para o Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
