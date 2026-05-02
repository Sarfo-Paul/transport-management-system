<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $driverId = $data['driver_id'] ?? null;
    
    if ($driverId) {
        try {
            $stmt = $conn->prepare("UPDATE drivers SET photo_path = NULL WHERE driver_id = ?");
            $stmt->bind_param("i", $driverId);
            $stmt->execute();
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Reset photo error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid driver ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}