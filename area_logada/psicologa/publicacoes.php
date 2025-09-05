<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$publicacoes = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, titulo, resumo, link, imagem FROM publicacoes ORDER BY id DESC");
    $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicações: " . $e->getMessage());
}

require_once 'templates/header.php';
?>
<!-- Adicionar o script do editor de Rich Text com a sua chave de API -->
<script src="https://cdn.tiny.cloud/1/j9iwoh1j7j4qho7h8elm4scjtv3733q34tylzc7ggbf9ux3e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea.tinymce-editor',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 300,
  });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerir Publicações</h1>

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

    <!-- Formulário para Adicionar Nova Publicação -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Adicionar Nova Publicação</h2>
        <form action="processa_publicacao.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="adicionar">
            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 font-medium mb-2">Título</label>
                <input type="text" id="titulo" name="titulo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--cor-primaria)]">
            </div>
            <div class="mb-4">
                <label for="texto_artigo_add" class="block text-gray-700 font-medium mb-2">Texto do Artigo</label>
                <textarea id="texto_artigo_add" name="resumo" class="tinymce-editor"></textarea>
            </div>
            <div class="mb-4">
                <label for="link_compra_add" class="block text-gray-700 font-medium mb-2">Link externo de compra (opcional)</label>
                <input type="url" id="link_compra_add" name="link" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--cor-primaria)]" placeholder="https://loja.exemplo.com/livro">
            </div>
            <div class="mb-6">
                <label for="imagem" class="block text-gray-700 font-medium mb-2">Imagem da Capa</label>
                <input type="file" id="imagem" name="imagem" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-[var(--cor-primaria)] hover:file:bg-gray-100" accept="image/jpeg, image/png, image/gif">
                <p class="text-xs text-gray-500 mt-1">Recomendado: imagem retangular (ex: 1200x630 pixels).</p>
            </div>
            <button type="submit" class="w-full text-white font-bold py-3 px-4 rounded-md transition-colors duration-300" style="background-color: var(--cor-primaria);">Adicionar Publicação</button>
        </form>
    </div>

    <!-- Lista de Publicações Existentes -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Publicações Existentes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($publicacoes as $pub): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (!empty($pub['imagem'])): ?>
                                    <img src="../../uploads/site/<?php echo htmlspecialchars($pub['imagem']); ?>" alt="Capa" class="h-12 w-20 object-cover rounded">
                                <?php else: ?>
                                    <div class="h-12 w-20 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">Sem Imagem</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pub['titulo']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-4">
                                <a href="editar_publicacao.php?id=<?php echo $pub['id']; ?>" class="text-[var(--cor-primaria)] hover:opacity-80">Editar</a>
                                <form action="processa_publicacao.php" method="POST" onsubmit="return confirm('Tem a certeza que deseja apagar esta publicação?');" class="inline">
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
</div>

<?php require_once 'templates/footer.php'; ?>

