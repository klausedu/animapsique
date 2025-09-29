<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Diretório de upload
define('UPLOAD_DIR', __DIR__ . '/../../uploads/site/');

/**
 * Apaga uma imagem antiga se ela existir.
 * @param string|null $nome_imagem O nome do ficheiro da imagem a ser apagada.
 */
function apagar_imagem_antiga($nome_imagem) {
    if (!empty($nome_imagem) && file_exists(UPLOAD_DIR . $nome_imagem)) {
        unlink(UPLOAD_DIR . $nome_imagem);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        $pdo = conectar();

        if ($acao === 'adicionar') {
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $link = trim($_POST['link'] ?? '');
            $nome_imagem = null;

            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem = $_FILES['imagem'];
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));

                if ($imagem['size'] > 5 * 1024 * 1024) { 
                    throw new Exception("O ficheiro da imagem é demasiado grande (máx 5MB)."); 
                }
                if (!in_array($extensao, $extensoes_permitidas)) { 
                    throw new Exception("Tipo de ficheiro de imagem inválido."); 
                }
                
                $nome_imagem = 'livro_' . uniqid('', true) . '.' . $extensao;
                if (!move_uploaded_file($imagem['tmp_name'], UPLOAD_DIR . $nome_imagem)) {
                    throw new Exception("Falha ao mover o ficheiro da imagem enviada.");
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, texto, link, imagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $texto, $link, $nome_imagem]);
            $_SESSION['mensagem_sucesso'] = "Livro adicionado com sucesso!";

        } elseif ($acao === 'editar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $link = trim($_POST['link'] ?? '');
            $imagem_atual = $_POST['imagem_atual'] ?? null;

            if (!$id) {
                throw new Exception("ID do livro inválido.");
            }

            $nome_nova_imagem = $imagem_atual;

            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem = $_FILES['imagem'];
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
                if ($imagem['size'] > 5 * 1024 * 1024) { throw new Exception("O ficheiro é demasiado grande."); }
                if (!in_array($extensao, $extensoes_permitidas)) { throw new Exception("Tipo de ficheiro inválido."); }
                
                $nome_nova_imagem = 'livro_' . uniqid('', true) . '.' . $extensao;
                if (move_uploaded_file($imagem['tmp_name'], UPLOAD_DIR . $nome_nova_imagem)) {
                    // Apaga a imagem antiga apenas se o novo upload for bem-sucedido
                    apagar_imagem_antiga($imagem_atual);
                } else {
                    throw new Exception("Falha ao mover o novo ficheiro enviado.");
                }
            }

            $stmt = $pdo->prepare("UPDATE livros SET titulo = ?, texto = ?, link = ?, imagem = ? WHERE id = ?");
            $stmt->execute([$titulo, $texto, $link, $nome_nova_imagem, $id]);
            $_SESSION['mensagem_sucesso'] = "Livro atualizado com sucesso!";

        } elseif ($acao === 'apagar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) { throw new Exception("ID do livro inválido."); }
            
            $stmt_select = $pdo->prepare("SELECT imagem FROM livros WHERE id = ?");
            $stmt_select->execute([$id]);
            $livro = $stmt_select->fetch(PDO::FETCH_ASSOC);
            if ($livro) { 
                apagar_imagem_antiga($livro['imagem']); 
            }
            
            $stmt_delete = $pdo->prepare("DELETE FROM livros WHERE id = ?");
            $stmt_delete->execute([$id]);
            $_SESSION['mensagem_sucesso'] = "Livro apagado com sucesso!";
        }

    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Ocorreu um erro: " . $e->getMessage();
    }

    header('Location: livros.php');
    exit;
}
?>
