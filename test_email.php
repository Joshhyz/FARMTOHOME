<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2; // Enable debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'farmtohomee11@gmail.com';
    $mail->Password = 'knwu jnqr jgrx ztkk'; // YOUR APP PASSWORD HERE
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('farmtohomee11@gmail.com', 'FarmToHome');
    $mail->addAddress('test@example.com');
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email';

    echo "Attempting to send email...\n";
    $mail->send();
    echo "Email sent successfully!";
    
} catch (Exception $e) {
    echo "SMTP Error: " . $mail->ErrorInfo . "\n";
    echo "Exception: " . $e->getMessage();
}
?>
