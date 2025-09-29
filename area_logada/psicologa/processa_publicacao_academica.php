<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        $pdo = conectar();

        if ($acao === 'adicionar') {
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $link = trim($_POST['link'] ?? ''); // Pega o link do formulário

            if (empty($titulo)) {
                throw new Exception("O título é obrigatório.");
            }
            
            $stmt = $pdo->prepare("INSERT INTO publicacoes_academicas (titulo, texto, link) VALUES (?, ?, ?)");
            $stmt->execute([$titulo, $texto, $link]);
            $_SESSION['mensagem_sucesso'] = "Publicação adicionada com sucesso!";

        } elseif ($acao === 'editar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $link = trim($_POST['link'] ?? ''); // Pega o link do formulário

            if (!$id || empty($titulo)) {
                throw new Exception("Dados inválidos para edição.");
            }

            $stmt = $pdo->prepare("UPDATE publicacoes_academicas SET titulo = ?, texto = ?, link = ? WHERE id = ?");
            $stmt->execute([$titulo, $texto, $link, $id]);
            $_SESSION['mensagem_sucesso'] = "Publicação atualizada com sucesso!";

        } elseif ($acao === 'apagar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) { 
                throw new Exception("ID inválido."); 
            }
            
            $stmt = $pdo->prepare("DELETE FROM publicacoes_academicas WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['mensagem_sucesso'] = "Publicação apagada com sucesso!";
        }

    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Ocorreu um erro: " . $e->getMessage();
    }

    header('Location: publicacoes_academicas.php');
    exit;
}
?>
