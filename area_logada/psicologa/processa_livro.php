<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

define('UPLOAD_DIR', __DIR__ . '/../../uploads/site/');

// ... (Função apagar_imagem_antiga - igual à de processa_publicacao.php)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    try {
        $pdo = conectar();
        if ($acao === 'adicionar' || $acao === 'editar') {
            // Lógica para upload de imagem (igual à de processa_publicacao.php)
            // ...
        }

        if ($acao === 'adicionar') {
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, texto, link, imagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['titulo'], $_POST['texto'], $_POST['link'], $nome_imagem]);
            $_SESSION['mensagem_sucesso'] = "Livro adicionado!";
        } elseif ($acao === 'editar') {
            $stmt = $pdo->prepare("UPDATE livros SET titulo=?, texto=?, link=?, imagem=? WHERE id=?");
            $stmt->execute([$_POST['titulo'], $_POST['texto'], $_POST['link'], $nome_nova_imagem, $_POST['id']]);
            $_SESSION['mensagem_sucesso'] = "Livro atualizado!";
        } elseif ($acao === 'apagar') {
            // Lógica para apagar (igual à de processa_publicacao.php)
            // ...
            $_SESSION['mensagem_sucesso'] = "Livro apagado!";
        }
    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro: " . $e->getMessage();
    }
    header('Location: livros.php');
    exit;
}
