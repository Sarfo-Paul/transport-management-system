<?php
require_once __DIR__ . '/config.php';

try {
    // Get the raw POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['id']) || empty($input['id'])) {
        throw new Exception('Driver ID is required');
    }
    
    $driverId = $input['id'];
    
    // First get photo path to delete the file
    $stmt = $conn->prepare("SELECT photo_path FROM drivers WHERE driver_id = ?");
    $stmt->bind_param("i", $driverId);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();
    
    // Delete photo file if exists
    if ($driver && !empty($driver['photo_path'])) {
        $photoPath = __DIR__ . '/' . ltrim($driver['photo_path'], '/');
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }
    
    // Delete the driver record
    $stmt = $conn->prepare("DELETE FROM drivers WHERE driver_id = ?");
    $stmt->bind_param("i", $driverId);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->affected_rows === 0) {
        throw new Exception('No driver found with that ID');
    }
    
    // Instead of JSON response, redirect back with success message
    header('Location: app-driver-list.php?success=Driver deleted successfully');
    exit();
    
} catch (Exception $e) {
    // On error, redirect back with error message
    header('Location: app-driver-list.php?error=' . urlencode($e->getMessage()));
    exit();
}