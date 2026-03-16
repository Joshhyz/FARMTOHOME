<?php
session_start();
include "config/database.php";

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["action"] == "login") {
        $email = trim($_POST["email"]);

        $checkUser = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

        if (mysqli_num_rows($checkUser) > 0) {
            $code = rand(100000, 999999);

            $_SESSION["verification_code"] = $code;
            $_SESSION["auth_email"] = $email;
            $_SESSION["auth_type"] = "login";

            // later: send email here
            $message = "Verification code sent to $email";
            header("Location: verify_code.php");
            exit();
        } else {
            $error = "No account found with that email. Please sign up first.";
        }
    }

    if ($_POST["action"] == "signup") {
        $role = trim($_POST["role"]);
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

        if (mysqli_num_rows($checkEmail) > 0) {
            $error = "This email is already registered. Please log in instead.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $code = rand(100000, 999999);

            $_SESSION["verification_code"] = $code;
            $_SESSION["auth_type"] = "signup";
            $_SESSION["signup_name"] = $name;
            $_SESSION["auth_email"] = $email;
            $_SESSION["signup_password"] = password_hash($password, PASSWORD_DEFAULT);
            $_SESSION["signup_role"] = $role;

            // later: send email here
            $message = "Verification code sent to $email";
            header("Location: verify_code.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FarmToHome Authentication</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>

<div class="auth-page">
    <a class="back-home" href="index.php">← Back to home</a>

    <div class="auth-box">
        <div class="auth-logo">🌱 <span>FarmToHome</span></div>

        <?php if ($mode == "login") { ?>
            <h1>Welcome Back</h1>
            <p class="subtitle">Login to your account</p>

            <?php if (!empty($error)) { ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST">
                <input type="hidden" name="action" value="login">

                <label>Email</label>
                <input type="email" name="email" placeholder="your@email.com" required>

                <button type="submit" class="main-btn">Send Verification Code</button>

                <p class="switch-text">
                    Don't have an account?
                    <a href="registration.php?mode=signup">Sign up</a>
                </p>
            </form>

        <?php } else { ?>
            <h1>Create Account</h1>
            <p class="subtitle">Join our community of farmers and buyers</p>

            <?php if (!empty($error)) { ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST">
                <input type="hidden" name="action" value="signup">

                <label>I want to</label>
                <div class="role-boxes">
                    <label class="role-card">
                        <input type="radio" name="role" value="buyer" checked>
                        <span>Buy Products</span>
                        <small>As a Buyer</small>
                    </label>

                    <label class="role-card">
                        <input type="radio" name="role" value="farmer">
                        <span>Sell Products</span>
                        <small>As a Farmer</small>
                    </label>
                </div>

                <label>Full Name</label>
                <input type="text" name="name" placeholder="John Doe" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="your@email.com" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm password" required>

                <button type="submit" class="main-btn">Create Account</button>

                <p class="switch-text">
                    Already have an account?
                    <a href="registration.php?mode=login">Login</a>
                </p>
            </form>
        <?php } ?>
    </div>
</div>

</body>
</html>