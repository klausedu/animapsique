<?php
require_once 'config.php';
require_once 'includes/db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$paciente = null;

if (empty($token)) {
    $error = "Token de registro inválido ou ausente.";
} else {
    try {
        $pdo = conectar();
        $stmt = $pdo->prepare("SELECT id, nome, email FROM pacientes WHERE token_registro = ? AND token_expiracao > NOW() AND ativo = 0");
        $stmt->execute([$token]);
        $paciente = $stmt->fetch();

        if (!$paciente) {
            $error = "Este link de registro é inválido, expirou ou já foi utilizado. Por favor, solicite um novo link.";
        }
    } catch (PDOException $e) {
        error_log("Erro na página de registro: " . $e->getMessage());
        $error = "Ocorreu um erro no servidor. Tente novamente mais tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Completar Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-teal-700">Finalizar Cadastro</h2>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Atenção!</strong>
                    <span class="block sm:inline"> <?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php elseif ($paciente): ?>
                <p class="text-gray-600 text-center mb-6">Olá, <strong><?php echo htmlspecialchars($paciente['nome']); ?></strong>! Crie uma senha para acessar a plataforma.</p>
                <form action="processa_registro.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="mb-4">
                        <label for="senha" class="block text-gray-700 text-sm font-bold mb-2">Nova Senha</label>
                        <input type="password" id="senha" name="senha" required class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div class="mb-6">
                        <label for="confirma_senha" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Senha</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" required class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Ativar minha conta
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
