<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

// Simple notifications feed for navbars
// ?scope=user|driver
$scope = isset($_GET['scope']) ? $_GET['scope'] : 'user';
$items = [];

try {
    if ($scope === 'driver') {
        // Latest driver reports as notifications for drivers
        $stmt = $conn->prepare("SELECT dr.title, dr.status, dr.report_date AS time FROM driver_reports dr ORDER BY dr.report_date DESC LIMIT 5");
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $items[] = [
                'title' => $row['title'],
                'message' => 'Status: ' . $row['status'],
                'time' => date('M d, H:i', strtotime($row['time']))
            ];
        }
        $stmt->close();
    } else {
        // User notifications: latest bookings for the logged-in user if available, else general recent bookings
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT b.booking_id, b.status, b.travel_date AS time FROM bookings b WHERE b.user_id = ? ORDER BY b.created_at DESC LIMIT 5");
            $stmt->bind_param('i', $_SESSION['user_id']);
        } else {
            $stmt = $conn->prepare("SELECT b.booking_id, b.status, b.travel_date AS time FROM bookings b ORDER BY b.created_at DESC LIMIT 5");
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $items[] = [
                'title' => 'Booking #' . $row['booking_id'],
                'message' => 'Status: ' . $row['status'],
                'time' => date('M d', strtotime($row['time']))
            ];
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Silent failure but return empty list
}

echo json_encode(['items' => $items]);



