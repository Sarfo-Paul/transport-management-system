<?php
require_once 'config.php'; // Using config.php instead of db-connect.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: app-route-list.php');
    exit();
}

$action = $_POST['action'] ?? '';
$routeId = isset($_POST['route_id']) ? (int)$_POST['route_id'] : 0;

// Validate route exists
$stmt = $conn->prepare("SELECT route_id FROM routes WHERE route_id = ?");
$stmt->bind_param("i", $routeId);
$stmt->execute();
$result = $stmt->get_result();
if (!$result->fetch_assoc()) {
    $_SESSION['error_message'] = 'Invalid route specified.';
    header("Location: app-route-list.php");
    exit();
}
$stmt->close();

if ($action === 'add') {
    // Validate and add new schedule
    $dayOfWeek = $_POST['day_of_week'] ?? '';
    $departureTime = $_POST['departure_time'] ?? '';
    $arrivalTime = $_POST['arrival_time'] ?? '';
    $vehicleId = !empty($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : null;
    $driverId = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
    $status = $_POST['status'] ?? 'Scheduled';
    $notes = $_POST['notes'] ?? '';

    // Validate required fields
    if (empty($dayOfWeek) || empty($departureTime) || empty($arrivalTime)) {
        $_SESSION['error_message'] = 'Day of week, departure time, and arrival time are required.';
        header("Location: app-route-schedule.php?id=$routeId");
        exit();
    }

    // Validate vehicle exists if provided
    if ($vehicleId) {
        $stmt = $conn->prepare("SELECT vehicle_id FROM vehicles WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicleId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            $_SESSION['error_message'] = 'Selected vehicle does not exist.';
            header("Location: app-route-schedule.php?id=$routeId");
            exit();
        }
        $stmt->close();
    }

    // Validate driver exists if provided
    if ($driverId) {
        $stmt = $conn->prepare("SELECT driver_id FROM drivers WHERE driver_id = ?");
        $stmt->bind_param("i", $driverId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            $_SESSION['error_message'] = 'Selected driver does not exist.';
            header("Location: app-route-schedule.php?id=$routeId");
            exit();
        }
        $stmt->close();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO route_schedules 
                              (route_id, day_of_week, departure_time, arrival_time, vehicle_id, driver_id, status, notes)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiiss", 
            $routeId,
            $dayOfWeek,
            $departureTime,
            $arrivalTime,
            $vehicleId,
            $driverId,
            $status,
            $notes
        );
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = 'Schedule added successfully!';
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error adding schedule: ' . $e->getMessage();
    }
} elseif ($action === 'edit') {
    // Validate and update existing schedule
    $scheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;
    $dayOfWeek = $_POST['day_of_week'] ?? '';
    $departureTime = $_POST['departure_time'] ?? '';
    $arrivalTime = $_POST['arrival_time'] ?? '';
    $vehicleId = !empty($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : null;
    $driverId = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
    $status = $_POST['status'] ?? 'Scheduled';
    $notes = $_POST['notes'] ?? '';

    // Validate required fields
    if (empty($scheduleId) || empty($dayOfWeek) || empty($departureTime) || empty($arrivalTime)) {
        $_SESSION['error_message'] = 'Invalid schedule data provided.';
        header("Location: app-route-schedule.php?id=$routeId");
        exit();
    }

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

    // Validate vehicle exists if provided
    if ($vehicleId) {
        $stmt = $conn->prepare("SELECT vehicle_id FROM vehicles WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicleId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            $_SESSION['error_message'] = 'Selected vehicle does not exist.';
            header("Location: app-route-schedule.php?id=$routeId");
            exit();
        }
        $stmt->close();
    }

    // Validate driver exists if provided
    if ($driverId) {
        $stmt = $conn->prepare("SELECT driver_id FROM drivers WHERE driver_id = ?");
        $stmt->bind_param("i", $driverId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            $_SESSION['error_message'] = 'Selected driver does not exist.';
            header("Location: app-route-schedule.php?id=$routeId");
            exit();
        }
        $stmt->close();
    }

    try {
        $stmt = $conn->prepare("UPDATE route_schedules SET
                              day_of_week = ?,
                              departure_time = ?,
                              arrival_time = ?,
                              vehicle_id = ?,
                              driver_id = ?,
                              status = ?,
                              notes = ?
                              WHERE schedule_id = ?");
        $stmt->bind_param("sssiissi",
            $dayOfWeek,
            $departureTime,
            $arrivalTime,
            $vehicleId,
            $driverId,
            $status,
            $notes,
            $scheduleId
        );
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = 'Schedule updated successfully!';
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error updating schedule: ' . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = 'Invalid action specified.';
}

header("Location: app-route-schedule.php?id=$routeId");
exit();
?>