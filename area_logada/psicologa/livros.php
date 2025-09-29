<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$livros = [];
try {
    $pdo = conectar();
    $stmt = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo ASC");
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar livros: " . $e->getMessage());
}

$page_title = 'Gerenciar Livros';
require_once 'templates/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/hugerte@1.0.9/hugerte.min.js"></script>
<script>
  hugerte.init({ selector: 'textarea.hugerte-editor' });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciar Livros</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Adicionar Novo Livro</h2>
        <form action="processa_livro.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="adicionar">
            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 font-medium mb-2">Título</label>
                <input type="text" id="titulo" name="titulo" required class="w-full px-3 py-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="texto" class="block text-gray-700 font-medium mb-2">Descrição</label>
                <textarea id="texto" name="texto" class="hugerte-editor"></textarea>
            </div>
            <div class="mb-4">
                <label for="link" class="block text-gray-700 font-medium mb-2">Link (Saiba Mais)</label>
                <input type="url" id="link" name="link" class="w-full px-3 py-2 border rounded-md">
            </div>
            <div class="mb-6">
                <label for="imagem" class="block text-gray-700 font-medium mb-2">Imagem do Livro</label>
                <input type="file" id="imagem" name="imagem" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/*">
            </div>
            <button type="submit" class="w-full text-white font-bold py-3 px-4 rounded-md" style="background-color: var(--cor-primaria);">Adicionar Livro</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Livros Cadastrados</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <?php foreach ($livros as $livro): ?>
                <tr>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($livro['titulo']); ?></td>
                    <td class="px-6 py-4 flex space-x-4">
                        <a href="editar_livro.php?id=<?php echo $livro['id']; ?>" class="text-teal-600">Editar</a>
                        <form action="processa_livro.php" method="POST" onsubmit="return confirm('Deseja apagar?');">
                            <input type="hidden" name="acao" value="apagar">
                            <input type="hidden" name="id" value="<?php echo $livro['id']; ?>">
                            <button type="submit" class="text-red-600">Apagar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
