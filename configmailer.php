<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/Exception.php';

function sendVerificationCode($email, $code){

    $mail = new PHPMailer(true);

    try{

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // 🔴 CHANGE THIS
        $mail->Username = 'farmtohomee11@gmail.com
';
        $mail->Password = 'knwu jnqr jgrx ztkk
';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('farmtohomee11@gmail.com', 'FarmToHome');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'FarmToHome Verification Code';

        $mail->Body = "
            <h2>FarmToHome</h2>
            <p>Your verification code is:</p>
            <h1 style='letter-spacing:5px;'>$code</h1>
            <p>Do not share this code.</p>
        ";

        $mail->send();
        return true;

    }catch(Exception $e){
        return false;
    }
}