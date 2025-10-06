<?php
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: configuracoes_site.php');
    exit;
}

define('UPLOAD_DIR', __DIR__ . '/../../uploads/site/');

function processar_upload($file_info, $imagem_atual) {
    if (!isset($file_info['error']) || $file_info['error'] !== UPLOAD_ERR_OK) {
        if (isset($file_info['error']) && $file_info['error'] !== UPLOAD_ERR_NO_FILE) {
             $_SESSION['mensagem_erro'] = "Erro no upload da imagem: " . $file_info['error'];
        }
        return $imagem_atual;
    }
    $mime_type = mime_content_type($file_info['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime_type, $allowed_types)) {
        $_SESSION['mensagem_erro'] = "Tipo de ficheiro inválido. Apenas JPG, PNG e GIF são permitidos.";
        return $imagem_atual;
    }
    $extension = pathinfo($file_info['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid('site_', true) . '.' . $extension;
    $caminho_destino = UPLOAD_DIR . $novo_nome;
    if (move_uploaded_file($file_info['tmp_name'], $caminho_destino)) {
        if ($imagem_atual && file_exists(UPLOAD_DIR . $imagem_atual)) {
            unlink(UPLOAD_DIR . $imagem_atual);
        }
        return $novo_nome;
    } else {
        $_SESSION['mensagem_erro'] = "Falha ao mover o ficheiro enviado.";
        return $imagem_atual;
    }
}

// ======================================================================
// LÓGICA CORRIGIDA PARA PROCESSAR OS DADOS DO FORMULÁRIO
// ======================================================================
$dados_organizados = [];

// 1. Processa os campos de texto (titulo, texto, etc.)
foreach ($_POST as $key => $value) {
    if (strpos($key, 'conteudo_') === 0) {
        // Remove o prefixo 'conteudo_'
        $key_sem_prefixo = substr($key, 9);
        
        // Encontra a posição do último underscore
        $pos_ultimo_underscore = strrpos($key_sem_prefixo, '_');

        if ($pos_ultimo_underscore !== false) {
            // A secção é tudo antes do último underscore
            $secao = substr($key_sem_prefixo, 0, $pos_ultimo_underscore);
            // O campo é tudo depois do último underscore
            $campo = substr($key_sem_prefixo, $pos_ultimo_underscore + 1);
            
            $dados_organizados[$secao][$campo] = trim($value);
        }
    }
}

// 2. Processa as imagens (novas e atuais)
foreach ($_FILES as $key => $file_info) {
    if (strpos($key, 'imagem_') === 0) {
        // A secção é o nome do campo sem o prefixo 'imagem_'
        $secao = substr($key, 7);
        
        $imagem_atual_key = 'imagem_atual_' . $secao;
        $imagem_atual = $_POST[$imagem_atual_key] ?? null;
        
        $imagem_final = processar_upload($file_info, $imagem_atual);
        $dados_organizados[$secao]['imagem'] = $imagem_final;
    }
}
// ======================================================================

try {
    $pdo = conectar();
    $pdo->beginTransaction();

    $sql = "INSERT INTO conteudo_site (secao, titulo, texto, imagem) 
            VALUES (:secao, :titulo, :texto, :imagem)
            ON DUPLICATE KEY UPDATE 
                titulo = VALUES(titulo), 
                texto = VALUES(texto), 
                imagem = VALUES(imagem)";
    
    $stmt = $pdo->prepare($sql);

    foreach ($dados_organizados as $secao => $campos) {
        $imagem_final = $campos['imagem'] ?? ($_POST['imagem_atual_' . $secao] ?? null);

        $stmt->execute([
            ':secao' => $secao,
            ':titulo' => $campos['titulo'] ?? null,
            ':texto' => $campos['texto'] ?? null,
            ':imagem' => $imagem_final
        ]);
    }

    $pdo->commit();
    $_SESSION['mensagem_sucesso'] = "As configurações foram guardadas com sucesso!";

} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['mensagem_erro'] = "Erro ao guardar as configurações: " . $e->getMessage();
}

$active_tab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'geral';
header('Location: configuracoes_site.php?tab=' . urlencode($active_tab));
exit;
?>
