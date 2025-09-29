<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: publicacoes_academicas.php');
    exit;
}

$publicacao = null;
try {
    $pdo = conectar();
    // Busca o link junto com os outros dados
    $stmt = $pdo->prepare("SELECT id, titulo, texto, link FROM publicacoes_academicas WHERE id = ?");
    $stmt->execute([$id]);
    $publicacao = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicação: " . $e->getMessage());
}

if (!$publicacao) {
    header('Location: publicacoes_academicas.php');
    exit;
}

$page_title = 'Editar Publicação Acadêmica';
require_once 'templates/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/hugerte@1.0.9/hugerte.min.js"></script>
<script>
  hugerte.init({
    selector: 'textarea.hugerte-editor',
    height: 400,
  });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Publicação</h1>
        <a href="publicacoes_academicas.php" class="text-sm font-medium text-[var(--cor-primaria)] hover:opacity-80">&larr; Voltar</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="processa_publicacao_academica.php" method="POST">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?php echo $publicacao['id']; ?>">
            
            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 font-medium mb-2">Título</label>
                <input type="text" id="titulo" name="titulo" required class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($publicacao['titulo']); ?>">
            </div>
            <div class="mb-4">
                <label for="texto_publicacao" class="block text-gray-700 font-medium mb-2">Texto</label>
                <textarea id="texto_publicacao" name="texto" class="hugerte-editor"><?php echo htmlspecialchars($publicacao['texto']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="link" class="block text-gray-700 font-medium mb-2">Link (Saiba Mais)</label>
                <input type="url" id="link" name="link" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($publicacao['link']); ?>">
            </div>
            <button type="submit" class="w-full text-white font-bold py-3 px-4 rounded-md" style="background-color: var(--cor-primaria);">Guardar Alterações</button>
        </form>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
