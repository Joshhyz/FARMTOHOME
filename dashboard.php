<?php
$cookieLifetime = 60 * 60 * 24 * 30; // 30 days
session_set_cookie_params([
    'lifetime' => $cookieLifetime,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: registration.php?mode=login");
    exit();
}

$userRole = strtolower(trim($_SESSION["user_role"] ?? "buyer"));

if ($userRole === "buyer") {
    header("Location: buyerdashboard.php");
    exit();
}

if ($userRole === "farmer" || $userRole === "seller") {
    header("Location: sellerdashboard.php");
    exit();
}

// Default fallback
header("Location: buyerdashboard.php");
exit();
