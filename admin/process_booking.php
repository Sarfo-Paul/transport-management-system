<?php
require_once 'includes/db_connection.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $route_id = $_POST['route_id'];
    $schedule_id = $_POST['schedule_id'] ?? null;
    $travel_date = $_POST['travel_date'];
    $passenger_count = $_POST['passenger_count'];
    $notes = $_POST['notes'] ?? '';
    
    $stmt = $pdo->prepare("
        INSERT INTO bookings 
        (user_id, route_id, schedule_id, booking_date, travel_date, passenger_count, notes, status)
        VALUES (?, ?, ?, NOW(), ?, ?, ?, 'Pending')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $route_id,
        $schedule_id,
        $travel_date,
        $passenger_count,
        $notes
    ]);
    
    $_SESSION['success'] = "Booking submitted successfully!";
    header("Location: user-bookings.php");
    exit();
}
?>