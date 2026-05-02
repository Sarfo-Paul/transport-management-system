<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if (!isset($_GET['route_id'])) {
    echo json_encode([]);
    exit();
}

$routeId = (int)$_GET['route_id'];
$travelDate = $_GET['travel_date'] ?? null;

// Get day of week if travel date is provided
$dayOfWeek = null;
if ($travelDate) {
    $timestamp = strtotime($travelDate);
    $dayOfWeek = date('l', $timestamp);
}

$query = "SELECT rs.schedule_id, rs.day_of_week, rs.departure_time, rs.arrival_time, 
          v.registration_number, CONCAT(d.first_name, ' ', d.last_name) AS driver_name
          FROM route_schedules rs
          LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
          LEFT JOIN drivers d ON rs.driver_id = d.driver_id
          WHERE rs.route_id = ? AND rs.status = 'Scheduled'";

// Add day of week filter if travel date is provided
if ($dayOfWeek) {
    $query .= " AND rs.day_of_week = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $routeId, $dayOfWeek);
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $routeId);
}

$stmt->execute();
$result = $stmt->get_result();
$schedules = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($schedules);
?>