<?php
// Ativa exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Envio de E-mail</h1>";

// 1. Verifica se config.php existe
if (!file_exists('config.php')) {
    die("<p style='color:red'>ERRO: Arquivo config.php não encontrado!</p>");
}
echo "<p style='color:green'>OK: config.php encontrado.</p>";

require_once 'config.php';

// 2. Verifica constantes
$constantes = ['SMTP_HOST', 'SMTP_USER', 'SMTP_PASS', 'SMTP_PORT'];
foreach ($constantes as $constante) {
    if (!defined($constante)) {
        die("<p style='color:red'>ERRO: Constante $constante não definida em config.php</p>");
    }
}
echo "<p style='color:green'>OK: Constantes de configuração definidas.</p>";

// 3. Verifica PHPMailer
if (!file_exists('vendor/autoload.php')) {
    die("<p style='color:red'>ERRO: vendor/autoload.php não encontrado. Execute 'composer install'.</p>");
}
require_once 'vendor/autoload.php';
echo "<p style='color:green'>OK: PHPMailer encontrado.</p>";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configurações do Servidor
    $mail->SMTPDebug = 2; // Habilita debug detalhado
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // Remetente e Destinatário
    $mail->setFrom(EMAIL_FROM, 'Teste Diagnostico');
    $mail->addAddress(SMTP_USER); // Envia para o próprio remetente para testar

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Teste de Diagnostico - AnimaPsique';
    $mail->Body    = 'Se você recebeu este e-mail, a configuração SMTP está correta!';

    echo "<h3>Tentando enviar e-mail...</h3>";
    echo "<pre style='background:#f0f0f0; padding:10px; border:1px solid #ccc'>";
    $mail->send();
    echo "</pre>";
    
    echo "<h2 style='color:green'>SUCESSO: E-mail enviado corretamente!</h2>";
} catch (Exception $e) {
    echo "</pre>";
    echo "<h2 style='color:red'>FALHA: Não foi possível enviar o e-mail.</h2>";
    echo "<p><strong>Erro:</strong> {$mail->ErrorInfo}</p>";
    echo "<hr>";
    echo "<h3>Dicas de Solução:</h3>";
    echo "<ul>";
    echo "<li>Verifique se o SMTP_HOST está correto (ex: smtp.hostinger.com)</li>";
    echo "<li>Verifique se o SMTP_USER é o e-mail completo</li>";
    echo "<li>Verifique se a senha está correta</li>";
    echo "<li>Verifique a porta (geralmente 587 para TLS ou 465 para SSL)</li>";
    echo "</ul>";
}
?>
