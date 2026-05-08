<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/SMTP.php';
require __DIR__ . '/phpmailer/PHPMailer-master/PHPMailer-master/src/Exception.php';

// Get existing OTP for email or return null if none exists
function getExistingLoginOTP($email) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $email);
    $query = mysqli_query($conn, "SELECT login_otp FROM users WHERE email = '$email'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $result = mysqli_fetch_assoc($query);
        return $result['login_otp'] ?? null;
    }
    return null;
}

// Save OTP to database
function saveLoginOTP($email, $code) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $email);
    $code = mysqli_real_escape_string($conn, $code);
    
    $query = mysqli_query($conn, "UPDATE users SET login_otp = '$code' WHERE email = '$email'");
    return $query;
}

function sendVerificationCode($email, $code){

    $mail = new PHPMailer(true);

    try{

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // 🔴 CHANGE THIS
        $mail->Username = 'farmtohomee11@gmail.com';
        $mail->Password = 'knwu jnqr jgrx ztkk';

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
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}