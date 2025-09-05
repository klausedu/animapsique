<?php

/**

 * Arquivo de Configuração Principal

 *

 * Defina aqui todas as constantes e configurações globais do sistema.

 * Substitua os valores 'SUA_CONFIGURACAO' pelos dados corretos do seu ambiente.

 */



// Inicia a sessão em todas as páginas que incluírem este arquivo

if (session_status() == PHP_SESSION_NONE) {

    session_start();

}



// --- CONFIGURAÇÕES DO BANCO DE DADOS ---

// Servidor do banco de dados (geralmente 'localhost' na Hostinger)

define('DB_HOST', 'localhost');

// Nome do seu banco de dados

define('DB_NAME', 'u200613309_animapsique02');

// Usuário do banco de dados

define('DB_USER', 'u200613309_adminuser');

// Senha do banco de dados

define('DB_PASS', 'Gostodoce00!123');

// Charset da conexão

define('DB_CHARSET', 'utf8mb4');





// --- CONFIGURAÇÕES DE E-MAIL (SMTP) ---

// Use as informações fornecidas pela Hostinger ou seu provedor de e-mail

// Servidor SMTP

define('SMTP_HOST', 'smtp.hostinger.com');

// Usuário SMTP (geralmente seu e-mail completo)

define('SMTP_USER', 'contato@psicologar.com.br');

// Senha do seu e-mail

define('SMTP_PASS', 'Gostodoce00!123');

// Porta SMTP (587 para TLS é o mais comum)

define('SMTP_PORT', 465);

// Tipo de segurança ('tls' ou 'ssl')

define('SMTP_SECURE', 'ssl');

// E-mail do remetente para notificações do sistema

define('EMAIL_FROM', 'contato@psicologar.com.br');

// Nome do remetente

define('EMAIL_FROM_NAME', 'Plataforma AnimaPsique');





// --- CONFIGURAÇÕES GERAIS DO SITE ---

// URL completa do seu site (com https://)

define('BASE_URL', 'https://maroon-oyster-227506.hostingersite.com');

// Fuso horário para datas e horas

date_default_timezone_set('America/Sao_Paulo');



// --- CHAVES DE SEGURANÇA ---

// Use um gerador de chaves online para criar uma string aleatória e segura

define('SECRET_KEY', '$2y$10$0ve.bxEgeCpjx226L6RYIeDEmAvRLlHaog4Qtqf2td0wSS9RxZH3G');

// --- CONFIGURAÇÕES DO WHEREBY EMBEDDED---
// Obtenha sua chave de API em https://whereby.com/user/api-keys
define('WHEREBY_API_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmFwcGVhci5pbiIsImF1ZCI6Imh0dHBzOi8vYXBpLmFwcGVhci5pbi92MSIsImV4cCI6OTAwNzE5OTI1NDc0MDk5MSwiaWF0IjoxNzU3MDkxMjk4LCJvcmdhbml6YXRpb25JZCI6MzI0MjAzLCJqdGkiOiI1Nzk2MmQ0ZC05YWY1LTQ1NDctODE0MS04NmZlMDljYjE0NDIifQ.w5J6YPkpiShWZFZtEaD6N3_PGZsuz9FdvKMCPcebpHc');

?>


