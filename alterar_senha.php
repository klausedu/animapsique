<?php
// Define o caminho base para incluir os ficheiros corretamente
$base_path = realpath(dirname(__FILE__) . '/../../');
require_once $base_path . '/config.php';

// Determina qual verificador de autenticação usar
if (strpos($_SERVER['REQUEST_URI'], '/psicologa/') !== false) {
    require_once $base_path . '/includes/auth_psicologa.php';
    $template_path = 'templates/header.php';
} else {
    require_once $base_path . '/includes/auth_paciente.php';
    $template_path = 'templates/header.php';
}

$page_title = 'Alterar Senha';
require_once $template_path;
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Alterar a Minha Senha</h2>

        <!-- Exibe mensagens de feedback -->
        <?php if (isset($_SESSION['senha_success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><p><?php echo $_SESSION['senha_success']; unset($_SESSION['senha_success']); ?></p></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['senha_error'])): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p><?php echo $_SESSION['senha_error']; unset($_SESSION['senha_error']); ?></p></div>
        <?php endif; ?>

        <form action="../../processa_alterar_senha.php" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="senha_antiga" class="block text-sm font-medium text-gray-700">Senha Antiga</label>
                    <input type="password" name="senha_antiga" id="senha_antiga" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="nova_senha" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                    <input type="password" name="nova_senha" id="nova_senha" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="confirma_nova_senha" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                    <input type="password" name="confirma_nova_senha" id="confirma_nova_senha" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                    Guardar Nova Senha
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
