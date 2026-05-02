<?php
include 'config.php';

// Check if user is logged in and booking ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$bookingId = intval($_GET['id']);

// Verify the booking belongs to the user
$stmt = $conn->prepare("SELECT booking_id FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'Pending'");
$stmt->bind_param("ii", $bookingId, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    $_SESSION['error'] = "Booking not found or cannot be cancelled";
    header("Location: user-bookings.php");
    exit();
}

// Cancel the booking
$updateStmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
$updateStmt->bind_param("i", $bookingId);

if ($updateStmt->execute()) {
    $_SESSION['success'] = "Booking #BK-" . str_pad($bookingId, 4, '0', STR_PAD_LEFT) . " has been cancelled";
} else {
    $_SESSION['error'] = "Error cancelling booking";
}

header("Location: user-bookings.php");
exit();
?>