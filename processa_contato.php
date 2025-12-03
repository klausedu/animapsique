<?php
/*
===================================================================
Conteúdo para: processa_contato.php
===================================================================
*/

// Ativa a exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php'; // Usando config real
require_once 'includes/email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contato.php');
    exit;
}

// Coleta e sanitiza os dados do formulário
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$idade = filter_input(INPUT_POST, 'idade', FILTER_SANITIZE_NUMBER_INT);
$motivo = filter_input(INPUT_POST, 'motivo', FILTER_SANITIZE_STRING);

if (!$nome || !$email || !$telefone) {
    // Idealmente, adicionar uma mensagem de erro na sessão
    header('Location: contato.php?erro=invalido');
    exit;
}

// --- Prepara os e-mails ---

// 1. E-mail para a psicóloga (você)
$assunto_psicologa = "Nova Solicitação de Contato: " . $nome;
$corpo_psicologa = "
    <h1>Nova Solicitação de Primeira Sessão</h1>
    <p>Você recebeu um novo pedido de contato através do seu site.</p>
    <ul>
        <li><strong>Nome:</strong> {$nome}</li>
        <li><strong>E-mail:</strong> {$email}</li>
        <li><strong>Telefone:</strong> {$telefone}</li>
        <li><strong>Idade:</strong> {$idade}</li>
        <li><strong>Motivo da Busca:</strong><br>" . nl2br(htmlspecialchars($motivo)) . "</li>
    </ul>
";

// 2. E-mail de confirmação para o paciente
$assunto_paciente = "Confirmação de Recebimento de Contato";
$corpo_paciente = "
    <h1>Olá, {$nome}!</h1>
    <p>Recebi sua solicitação de contato e agradeço pelo seu interesse.</p>
    <p>Em breve, entrarei em contato com você através do e-mail ou telefone fornecido para conversarmos sobre o agendamento da sua primeira sessão.</p>
    <p>Atenciosamente,</p>
    <p><strong>Dra. Nara Helena Lopes</strong><br>Psicóloga Clínica</p>
";

// Envia os e-mails
// Nota: Em produção, usar as credenciais reais do config.php
$enviado_psicologa = enviar_email(SMTP_USER, 'Psicóloga', $assunto_psicologa, $corpo_psicologa);

if ($enviado_psicologa) {
    enviar_email($email, $nome, $assunto_paciente, $corpo_paciente);
    
    // Verifica se há uma URL de redirecionamento personalizada
    $redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL);
    
    if ($redirect) {
        header("Location: $redirect");
    } else {
        // Padrão: ir para página de confirmação
        header('Location: confirmacao.php');
    }
} else {
    // Se falhar, redireciona com uma mensagem de erro genérica
    header('Location: contato.php?erro=envio');
}
exit;
?>