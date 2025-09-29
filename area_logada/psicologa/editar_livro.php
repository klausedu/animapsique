<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: livros.php'); exit; }

$livro = null;
try {
    $pdo = conectar();
    $stmt = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
    $stmt->execute([$id]);
    $livro = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Erro ao buscar livro: " . $e->getMessage()); }

if (!$livro) { header('Location: livros.php'); exit; }

$page_title = 'Editar Livro';
require_once 'templates/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/hugerte@1.0.9/hugerte.min.js"></script>
<script>
  hugerte.init({ selector: 'textarea.hugerte-editor' });
</script>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Editar Livro</h1>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="processa_livro.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?php echo $livro['id']; ?>">
            <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($livro['imagem']); ?>">

            <div class="mb-4">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($livro['titulo']); ?>" required class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="texto">Descrição</label>
                <textarea id="texto" name="texto" class="hugerte-editor"><?php echo htmlspecialchars($livro['texto']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="link">Link</label>
                <input type="url" id="link" name="link" value="<?php echo htmlspecialchars($livro['link']); ?>" class="w-full p-2 border rounded">
            </div>
            <div class="mb-6">
                <label for="imagem">Nova Imagem</label>
                <?php if ($livro['imagem']): ?>
                    <img src="../../uploads/site/<?php echo $livro['imagem']; ?>" alt="Imagem atual" class="w-32 h-auto my-2">
                <?php endif; ?>
                <input type="file" id="imagem" name="imagem" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0" accept="image/*">
            </div>
            <button type="submit" class="w-full text-white p-3 rounded-md" style="background-color: var(--cor-primaria);">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
