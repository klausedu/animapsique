<?php
/**
 * Script de Logout
 *
 * Este ficheiro encerra a sessão atual do utilizador e redireciona-o
 * para a página de login.
 */

// Inclui o config.php para garantir que a sessão é iniciada antes de ser destruída.
require_once 'config.php';

// Remove todas as variáveis da sessão.
$_SESSION = array();

// Se desejar destruir completamente a sessão, apague também o cookie da sessão.
// Nota: Isto irá destruir a sessão e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão.
session_destroy();

// Redireciona para a página de login.
header('Location: login.php');
exit;
?>
