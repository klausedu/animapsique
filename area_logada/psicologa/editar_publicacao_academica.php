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
    $stmt = $pdo->prepare("SELECT id, titulo, texto FROM publicacoes_academicas WHERE id = ?");
    $stmt->execute([$id]);
    $publicacao = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar publicação acadêmica: " . $e->getMessage());
}

if (!$publicacao) {
    $_SESSION['mensagem_erro'] = "Publicação não encontrada.";
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
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 400,
  });
</script>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Publicação Acadêmica</h1>
        <a href="publicacoes_academicas.php" class="text-sm font-medium text-[var(--cor-primaria)] hover:opacity-80 transition-opacity">&larr; Voltar para a lista</a>
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
                <label for="texto_publicacao" class="block text-gray-700 font-medium mb-2">Texto da Publicação</label>
                <textarea id="texto_publicacao" name="texto" class="hugerte-editor"><?php echo htmlspecialchars($publicacao['texto']); ?></textarea>
            </div>
            <button type="submit" class="w-full text-white font-bold py-3 px-4 rounded-md transition-colors" style="background-color: var(--cor-primaria);">Guardar Alterações</button>
        </form>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
