<?php
// ======================================================================
// SECÇÃO DE DIAGNÓSTICO
// ======================================================================
// O código abaixo serve para testar o acesso ao ficheiro e as permissões.
// Ele irá parar a execução antes de tentar processar os dados.
// ======================================================================

// Inicia a sessão para podermos ver as mensagens
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Desativa a visualização de erros para o utilizador final, mas regista-os
error_reporting(E_ALL);
ini_set('display_errors', 1); // Temporariamente ligado para ver erros no ecrã
ini_set('log_errors', 1);

echo "<h1>Diagnóstico de Submissão</h1>";
echo "<p>Se está a ver esta página, o acesso ao ficheiro PHP funcionou. O problema está nos dados enviados ou no processamento original.</p>";

echo "<h2>Dados Recebidos (\$_POST):</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Ficheiros Recebidos (\$_FILES):</h2>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

// Tenta uma operação simples de ficheiro para verificar permissões
$teste_dir = __DIR__ . '/../../uploads/site/';
echo "<h2>Teste de Escrita no Diretório:</h2>";
$caminho_real_dir = realpath($teste_dir);

if ($caminho_real_dir) {
    echo "<p>A tentar escrever em: " . $caminho_real_dir . "</p>";
    if (is_writable($caminho_real_dir)) {
        echo "<p style='color:green; font-weight:bold;'>Sucesso! O diretório tem permissão de escrita.</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>Falha! O diretório não tem permissão de escrita. Verifique as permissões (devem ser 755 ou 775).</p>";
    }
} else {
    echo "<p style='color:red; font-weight:bold;'>Falha! O diretório especificado ('" . htmlspecialchars($teste_dir) . "') não foi encontrado.</p>";
}


// A execução para aqui intencionalmente para análise.
// Depois de diagnosticar, apague ou comente esta secção.
exit;


// ======================================================================
// CÓDIGO ORIGINAL (NÃO SERÁ EXECUTADO DURANTE O DIAGNÓSTICO)
// ======================================================================
require_once '../../config.php';
require_once '../../includes/auth_psicologa.php';
require_once '../../includes/db.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: configuracoes_site.php');
    exit;
}

// Diretório para onde as imagens serão enviadas - JÁ CORRIGIDO
define('UPLOAD_DIR', __DIR__ . '/../../uploads/site/');

// Função para processar o upload de uma imagem
function processar_upload($file_info, $imagem_atual) {
    // Verifica se há erro no upload
    if ($file_info['error'] !== UPLOAD_ERR_OK) {
        // Se não for UPLOAD_ERR_NO_FILE, é um erro real.
        if ($file_info['error'] !== UPLOAD_ERR_NO_FILE) {
             $_SESSION['mensagem_erro'] = "Erro no upload da imagem: " . $file_info['error'];
        }
        return $imagem_atual; // Retorna a imagem antiga se não houver novo upload ou se houver erro
    }

    // Validação do tipo de ficheiro
    $mime_type = mime_content_type($file_info['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime_type, $allowed_types)) {
        $_SESSION['mensagem_erro'] = "Tipo de ficheiro inválido. Apenas JPG, PNG e GIF são permitidos.";
        return $imagem_atual;
    }

    // Gera um nome de ficheiro único para evitar conflitos
    $extension = pathinfo($file_info['name'], PATHINFO_EXTENSION);
    $novo_nome = uniqid('site_', true) . '.' . $extension;
    $caminho_destino = UPLOAD_DIR . $novo_nome;

    // Move o ficheiro para o diretório de uploads
    if (move_uploaded_file($file_info['tmp_name'], $caminho_destino)) {
        // Se o upload for bem-sucedido, apaga a imagem antiga se ela existir
        if ($imagem_atual && file_exists(UPLOAD_DIR . $imagem_atual)) {
            unlink(UPLOAD_DIR . $imagem_atual);
        }
        return $novo_nome; // Retorna o nome do novo ficheiro
    } else {
        $_SESSION['mensagem_erro'] = "Falha ao mover o ficheiro enviado.";
        return $imagem_atual; // Retorna a imagem antiga em caso de falha
    }
}

// Inicia a transação com a base de dados
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

    // Itera sobre cada secção de conteúdo de texto
    if (isset($_POST['conteudo'])) {
        foreach ($_POST['conteudo'] as $secao => $campos) {
            $titulo = isset($campos['titulo']) ? trim($campos['titulo']) : null;
            $texto = isset($campos['texto']) ? trim($campos['texto']) : null;
            
            // Lida com a imagem (nova ou a atual)
            $imagem_atual = isset($campos['imagem_atual']) ? trim($campos['imagem_atual']) : null;
            $imagem_nova_info = isset($_FILES['conteudo_imagem']['name'][$secao]) ? [
                'name' => $_FILES['conteudo_imagem']['name'][$secao],
                'type' => $_FILES['conteudo_imagem']['type'][$secao],
                'tmp_name' => $_FILES['conteudo_imagem']['tmp_name'][$secao],
                'error' => $_FILES['conteudo_imagem']['error'][$secao],
                'size' => $_FILES['conteudo_imagem']['size'][$secao],
            ] : null;

            $imagem_final = $imagem_atual;
            if ($imagem_nova_info) {
                 $imagem_final = processar_upload($imagem_nova_info, $imagem_atual);
            }
            
            $stmt->execute([
                ':secao' => $secao,
                ':titulo' => $titulo,
                ':texto' => $texto,
                ':imagem' => $imagem_final
            ]);
        }
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
