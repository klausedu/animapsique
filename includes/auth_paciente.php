<?php
/**
 * Verificador de Autenticação para a Área do Paciente.
 *
 * Este script verifica se o usuário está logado e se o tipo de usuário
 * é 'paciente'. Se não for, redireciona para a página de login.
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado E se o tipo é 'paciente'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'paciente') {
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['login_error'] = 'Acesso não autorizado. Por favor, faça o login.';

    // Garante o caminho correto para o login a partir de qualquer profundidade de pasta
    $base_url = defined('BASE_URL') ? BASE_URL : '/';
    header('Location: ' . $base_url . 'login.php');
    exit;
}

// Variáveis úteis para usar nas páginas do paciente
$paciente_id = $_SESSION['user_id'];
$paciente_nome = $_SESSION['user_nome'];
?>
