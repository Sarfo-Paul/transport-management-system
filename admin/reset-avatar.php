<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    
    if ($userId && $userId == $_SESSION['user_id']) {
        try {
            // Get current photo path
            $stmt = $conn->prepare("SELECT photo_path FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            // Update to default avatar
            $update = $conn->prepare("UPDATE users SET photo_path = 'default-avatar.jpg' WHERE user_id = ?");
            $update->bind_param("i", $userId);
            $update->execute();
            
            // Delete old photo if it exists and isn't default
            if ($result && $result['photo_path'] && $result['photo_path'] !== 'default-avatar.jpg') {
                $uploadDir = 'assets/img/avatars/';
                @unlink($uploadDir . $result['photo_path']);
            }
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Reset avatar error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>