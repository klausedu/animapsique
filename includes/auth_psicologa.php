<?php
/**
 * Verificador de Autenticação para a Área da Psicóloga.
 *
 * Este script verifica se o usuário está logado e se o tipo de usuário
 * é 'psicologa'. Se não for, redireciona para a página de login.
 *
 * Deve ser incluído no topo de todas as páginas restritas.
 */

// Garante que a sessão seja iniciada. O config.php já faz isso.
// Se este arquivo for incluído sem o config, a linha abaixo é um fallback.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado E se o tipo é 'psicologa'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'psicologa') {
    // Se não estiver autenticado corretamente, destrói a sessão por segurança
    session_unset();
    session_destroy();

    // Guarda uma mensagem de erro para exibir na página de login
    session_start();
    $_SESSION['login_error'] = 'Acesso não autorizado. Por favor, faça o login.';

    // Redireciona para a página de login.
    // O BASE_URL vem do config.php, garantindo o caminho correto.
    // Se config.php não estiver carregado, use um caminho relativo.
    if (defined('BASE_URL')) {
        header('Location: ' . BASE_URL . '/login.php');
    } else {
        // Fallback para o caso de o config.php não ter sido incluído antes
        header('Location: ../../login.php');
    }
    exit;
}

// Se chegou até aqui, o usuário está autenticado corretamente.
// Podemos definir variáveis úteis para usar nas páginas.
$psicologa_id = $_SESSION['user_id'];
$psicologa_nome = $_SESSION['user_nome'];

?>
