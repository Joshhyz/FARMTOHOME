<?php
session_start();
include "config/database.php";

$error = "";

if (!isset($_SESSION["verification_code"]) || !isset($_SESSION["auth_email"]) || !isset($_SESSION["auth_type"])) {
    header("Location: registration.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = trim($_POST["verification_code"]);
    $real_code = $_SESSION["verification_code"];
    $auth_type = $_SESSION["auth_type"];

    if ($entered_code == $real_code) {

        if ($auth_type == "login") {
            $email = $_SESSION["auth_email"];

            $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
            $user = mysqli_fetch_assoc($query);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["full_name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            unset($_SESSION["verification_code"], $_SESSION["auth_type"], $_SESSION["auth_email"]);

            header("Location: index.php");
            exit();
        }

        if ($auth_type == "signup") {
            $name = $_SESSION["signup_name"];
            $email = $_SESSION["auth_email"];
            $password = $_SESSION["signup_password"];
            $role = $_SESSION["signup_role"];

            mysqli_query($conn, "INSERT INTO users (full_name, email, password, role) 
                                 VALUES ('$name', '$email', '$password', '$role')");

            $new_id = mysqli_insert_id($conn);

            $_SESSION["user_id"] = $new_id;
            $_SESSION["user_name"] = $name;
            $_SESSION["user_email"] = $email;
            $_SESSION["user_role"] = $role;

            unset(
                $_SESSION["verification_code"],
                $_SESSION["auth_type"],
                $_SESSION["auth_email"],
                $_SESSION["signup_name"],
                $_SESSION["signup_password"],
                $_SESSION["signup_role"]
            );

            header("Location: index.php");
            exit();
        }

    } else {
        $error = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Code</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>

<div class="auth-page">
    <a class="back-home" href="registration.php">← Back</a>

    <div class="auth-box">
        <div class="auth-logo">🌱 <span>FarmToHome</span></div>

        <h1>Verify Your Code</h1>
        <p class="subtitle">
            Enter the 6-digit code sent to <strong><?php echo $_SESSION["auth_email"]; ?></strong>
        </p>

        <?php if (!empty($error)) { ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <label>Verification Code</label>
            <input type="text" name="verification_code" maxlength="6" placeholder="Enter 6-digit code" required>

            <button type="submit" class="main-btn">Verify Code</button>
        </form>

        <div class="demo-code-box">
            <strong>Demo Code:</strong> <?php echo $_SESSION["verification_code"]; ?>
        </div>
    </div>
</div>

</body>
</html>