<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer files
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/env_loader.php';

try {
    EnvLoader::load(__DIR__ . '/.env');
} catch (Exception $e) {
    // Continue if .env is missing, variables might be in the environment
}

function send_email($to_email, $to_name, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? 'sales@ictdilhara.lk';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT'] ?? 465;

        // Recipients
        $mail->setFrom($mail->Username, 'ICT with Dilhara Academy');
        $mail->addAddress($to_email, $to_name);

        // Content
        $mail->isHTML(false); // Can be set to true if HTML emails are needed
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error to a local file for debugging SMTP issues
        $log_message = "[" . date('Y-m-d H:i:s') . "] Mailer Error: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage() . "\n";
        file_put_contents(__DIR__ . '/mail_error.log', $log_message, FILE_APPEND);
        return false;
    }
}
?>
