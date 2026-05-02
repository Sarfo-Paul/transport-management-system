<?php
require_once __DIR__ . '/config.php';

try {
    // Get the schedule ID from query parameter
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Schedule ID is required');
    }
    
    $scheduleId = (int)$_GET['id'];
    
    // First verify the schedule exists
    $stmt = $conn->prepare("SELECT schedule_id FROM route_schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('No schedule found with that ID');
    }
    
    // Delete the schedule record
    $stmt = $conn->prepare("DELETE FROM route_schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to delete schedule');
    }
    
    // Redirect back with success message
    header('Location: app-driver-schedule.php?success=Schedule deleted successfully');
    exit();
    
} catch (Exception $e) {
    // On error, redirect back with error message
    header('Location: app-driver-schedule.php?error=' . urlencode($e->getMessage()));
    exit();
}