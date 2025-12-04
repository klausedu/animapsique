<?php

/**
 * Arquivo de Configuração Principal - EXEMPLO
 * 
 * INSTRUÇÕES:
 * 1. Copie este arquivo para config.php
 * 2. Preencha os valores corretos
 * 3. NUNCA faça commit do config.php (está no .gitignore)
 */

// Inicia a sessão em todas as páginas que incluírem este arquivo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- CONFIGURAÇÕES DO BANCO DE DADOS ---
// Servidor do banco de dados (geralmente 'localhost' na Hostinger)
define('DB_HOST', 'localhost');

// Nome do seu banco de dados
define('DB_NAME', 'SEU_BANCO_DE_DADOS');

// Usuário do banco de dados
define('DB_USER', 'SEU_USUARIO');

// Senha do banco de dados
define('DB_PASS', 'SUA_SENHA_AQUI');

// Charset da conexão
define('DB_CHARSET', 'utf8mb4');


// --- CONFIGURAÇÕES DE E-MAIL (SMTP) ---
// Use as informações fornecidas pela Hostinger ou seu provedor de e-mail

// Servidor SMTP
define('SMTP_HOST', 'smtp.hostinger.com');

// Usuário SMTP (geralmente seu e-mail completo)
define('SMTP_USER', 'contato@seudominio.com.br');

// Senha do seu e-mail
define('SMTP_PASS', 'SUA_SENHA_EMAIL_AQUI');

// Porta SMTP (587 para TLS é o mais comum)
define('SMTP_PORT', 465);

// Tipo de segurança ('tls' ou 'ssl')
define('SMTP_SECURE', 'ssl');

// E-mail do remetente para notificações do sistema
define('EMAIL_FROM', 'contato@seudominio.com.br');

// Nome do remetente
define('EMAIL_FROM_NAME', 'Plataforma AnimaPsique');


// --- CONFIGURAÇÕES GERAIS DO SITE ---
// URL completa do seu site (com https://)
define('BASE_URL', 'https://seudominio.com.br');

// Fuso horário para datas e horas
date_default_timezone_set('America/Sao_Paulo');


// --- CHAVES DE SEGURANÇA ---
// Use um gerador de chaves online para criar uma string aleatória e segura
// Exemplo: https://randomkeygen.com/
define('SECRET_KEY', 'GERE_UMA_CHAVE_ALEATORIA_AQUI');

// --- GOOGLE ADS ---
// Preencha com o ID e Label da sua conversão (ex: AW-123456789/AbCdEfGhIjK)
// Se não tiver, deixe vazio ou com null
define('GOOGLE_ADS_CONVERSION_ID', 'AW-17777197300'); 
define('GOOGLE_ADS_CONVERSION_LABEL', '2ZE2CI2ZjcwbEPSB6pxC');

?>
