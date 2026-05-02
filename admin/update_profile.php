<?php
require_once 'includes/db_connection.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $_SESSION['user_id']]);
    
    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: user-profile.php");
    exit();
}
?>