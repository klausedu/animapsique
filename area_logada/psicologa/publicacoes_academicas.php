<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$publicacoes = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, titulo FROM publicacoes_academicas ORDER BY titulo ASC");
    $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicações acadêmicas: " . $e->getMessage());
}

$page_title = 'Gerenciar Publicações Acadêmicas';
require_once 'templates/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/hugerte@1.0.9/hugerte.min.js"></script>
<script>
  hugerte.init({
    selector: 'textarea.hugerte-editor',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 300,
  });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciar Publicações Acadêmicas</h1>

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['mensagem_sucesso']; ?></span>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['mensagem_erro']; ?></span>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Adicionar Nova Publicação</h2>
        <form action="processa_publicacao_academica.php" method="POST">
            <input type="hidden" name="acao" value="adicionar">
            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 font-medium mb-2">Título</label>
                <input type="text" id="titulo" name="titulo" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="texto_publicacao_add" class="block text-gray-700 font-medium mb-2">Texto da Publicação</label>
                <textarea id="texto_publicacao_add" name="texto" class="hugerte-editor"></textarea>
            </div>
            <div class="mb-4">
                <label for="link" class="block text-gray-700 font-medium mb-2">Link (Saiba Mais)</label>
                <input type="url" id="link" name="link" placeholder="https://exemplo.com/artigo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <button type="submit" class="w-full text-white font-bold py-3 px-4 rounded-md transition-colors" style="background-color: var(--cor-primaria);">Adicionar Publicação</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Publicações Existentes</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($publicacoes as $pub): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pub['titulo']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-4">
                            <a href="editar_publicacao_academica.php?id=<?php echo $pub['id']; ?>" class="text-[var(--cor-primaria)] hover:opacity-80">Editar</a>
                            <form action="processa_publicacao_academica.php" method="POST" onsubmit="return confirm('Tem certeza?');" class="inline">
                                <input type="hidden" name="acao" value="apagar">
                                <input type="hidden" name="id" value="<?php echo $pub['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">Apagar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
