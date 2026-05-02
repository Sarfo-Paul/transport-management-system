<?php
require_once 'auth_functions.php';
session_start();

if (!isset($_GET['email'])) {
    $_SESSION['error'] = 'No email provided';
    header('Location: register.php');
    exit();
}

$email = $_GET['email'];
$result = resendVerificationEmail($email);

if ($result['success']) {
    $_SESSION['success'] = $result['message'];
    $_SESSION['verify_email'] = $email; // Keep the email in session
} else {
    $_SESSION['error'] = $result['message'];
}

header('Location: verify_email.php?email=' . urlencode($email));
exit();
?> 
