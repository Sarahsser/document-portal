<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendNotification($email, $full_name, $document_type, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com';  // ← fill this in
        $mail->Password = 'your_app_password';      // ← fill this in
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'Document Portal');
        $mail->addAddress($email, $full_name);
        $mail->isHTML(true);
        $mail->Subject = 'Document Status Update';
        $mail->Body = "
            <p>Dear $full_name,</p>
            <p>$message</p>
            <p>Document: <strong>$document_type</strong></p>
            <p>Thank you for using the Document Portal.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        throw new Exception("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>