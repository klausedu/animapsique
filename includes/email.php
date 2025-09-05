<?php
/*
===================================================================
Conteúdo para: includes/email.php
===================================================================
*/

// Inclui as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$base_path = realpath(dirname(__FILE__) . '/..');

// Verifica se o autoload do Composer existe
$autoload_path = $base_path . '/vendor/autoload.php';
if (!file_exists($autoload_path)) {
    // Se não existir, interrompe a execução e mostra um erro claro
    die("Erro Crítico: A biblioteca PHPMailer não foi encontrada. Por favor, instale-a utilizando o Composer. Caminho esperado: " . $autoload_path);
}
require_once $autoload_path;
require_once $base_path . '/config.php';

/**
 * Envia um e-mail usando as configurações definidas em config.php
 *
 * @param string $para_email O e-mail do destinatário.
 * @param string $para_nome O nome do destinatário.
 * @param string $assunto O assunto do e-mail.
 * @param string $corpo_html O conteúdo do e-mail em HTML.
 * @return bool Retorna true em caso de sucesso, false em caso de falha.
 */
function enviar_email(string $para_email, string $para_nome, string $assunto, string $corpo_html): bool
{
    $mail = new PHPMailer(true);

    try {
        // Configurações do Servidor
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Remetente e Destinatário
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($para_email, $para_nome);
        $mail->addReplyTo(SMTP_USER, 'Contato Plataforma');

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $corpo_html;
        $mail->AltBody = strip_tags($corpo_html);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Guarda o erro detalhado no log do servidor para que o possa consultar
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
