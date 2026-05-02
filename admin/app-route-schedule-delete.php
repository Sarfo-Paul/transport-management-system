<?php
require_once 'config.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['route_id']) || !is_numeric($_GET['route_id'])) {
    header('Location: app-route-list.php');
    exit();
}

$scheduleId = (int)$_GET['id'];
$routeId = (int)$_GET['route_id'];

// Validate schedule exists and belongs to this route
$stmt = $conn->prepare("SELECT schedule_id FROM route_schedules WHERE schedule_id = ? AND route_id = ?");
$stmt->bind_param("ii", $scheduleId, $routeId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->fetch_assoc()) {
    $_SESSION['error_message'] = 'Schedule not found or does not belong to this route.';
    header("Location: app-route-schedule.php?id=$routeId");
    exit();
}
$stmt->close();

try {
    $stmt = $conn->prepare("DELETE FROM route_schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success_message'] = 'Schedule deleted successfully!';
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Error deleting schedule: ' . $e->getMessage();
}

header("Location: app-route-schedule.php?id=$routeId");
exit();
?>