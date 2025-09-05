<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Diretório de upload
define('UPLOAD_DIR', __DIR__ . '/../../uploads/site/');

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
            // ... (código existente para adicionar - não precisa de ser alterado)
            $titulo = trim($_POST['titulo'] ?? '');
            $resumo = trim($_POST['resumo'] ?? '');
            $link = trim($_POST['link'] ?? '');
            $nome_imagem = null;

            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem = $_FILES['imagem'];
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
                $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
                if ($imagem['size'] > 5 * 1024 * 1024) { throw new Exception("O ficheiro é demasiado grande."); }
                if (!in_array($extensao, $extensoes_permitidas)) { throw new Exception("Tipo de ficheiro inválido."); }
                
                $nome_imagem = uniqid('pub_', true) . '.' . $extensao;
                if (!move_uploaded_file($imagem['tmp_name'], UPLOAD_DIR . $nome_imagem)) {
                    throw new Exception("Falha ao mover o ficheiro enviado.");
                }
            }
            $stmt = $pdo->prepare("INSERT INTO publicacoes (titulo, resumo, link, imagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $resumo, $link, $nome_imagem]);
            $_SESSION['mensagem_sucesso'] = "Publicação adicionada com sucesso!";

        } elseif ($acao === 'editar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $titulo = trim($_POST['titulo'] ?? '');
            $resumo = trim($_POST['resumo'] ?? '');
            $link = trim($_POST['link'] ?? '');
            $imagem_atual = $_POST['imagem_atual'] ?? null;

            if (!$id) {
                throw new Exception("ID da publicação inválido.");
            }

            $nome_nova_imagem = $imagem_atual; // Por defeito, mantém a imagem atual

            // Verifica se uma nova imagem foi enviada
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem = $_FILES['imagem'];
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
                $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
                if ($imagem['size'] > 5 * 1024 * 1024) { throw new Exception("O ficheiro é demasiado grande."); }
                if (!in_array($extensao, $extensoes_permitidas)) { throw new Exception("Tipo de ficheiro inválido."); }
                
                $nome_nova_imagem = uniqid('pub_', true) . '.' . $extensao;
                if (!move_uploaded_file($imagem['tmp_name'], UPLOAD_DIR . $nome_nova_imagem)) {
                    throw new Exception("Falha ao mover o novo ficheiro enviado.");
                }

                // Apaga a imagem antiga apenas se o novo upload for bem-sucedido
                apagar_imagem_antiga($imagem_atual);
            }

            $stmt = $pdo->prepare("UPDATE publicacoes SET titulo = ?, resumo = ?, link = ?, imagem = ? WHERE id = ?");
            $stmt->execute([$titulo, $resumo, $link, $nome_nova_imagem, $id]);
            $_SESSION['mensagem_sucesso'] = "Publicação atualizada com sucesso!";


        } elseif ($acao === 'apagar') {
             // ... (código existente para apagar - não precisa de ser alterado)
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) { throw new Exception("ID da publicação inválido."); }
            
            $stmt_select = $pdo->prepare("SELECT imagem FROM publicacoes WHERE id = ?");
            $stmt_select->execute([$id]);
            $publicacao = $stmt_select->fetch(PDO::FETCH_ASSOC);
            if ($publicacao) { apagar_imagem_antiga($publicacao['imagem']); }
            
            $stmt_delete = $pdo->prepare("DELETE FROM publicacoes WHERE id = ?");
            $stmt_delete->execute([$id]);
            $_SESSION['mensagem_sucesso'] = "Publicação apagada com sucesso!";
        }

    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Ocorreu um erro: " . $e->getMessage();
    }

    header('Location: publicacoes.php');
    exit;
}

