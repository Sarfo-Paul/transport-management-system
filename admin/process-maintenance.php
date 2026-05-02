<?php
require_once 'config.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

$action = $_POST['action'] ?? '';
$vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;

// Validate required fields
$required_fields = ['maintenance_type', 'maintenance_date', 'status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("Error: Required field '$field' is missing.");
    }
}

if ($action === 'add' && $vehicle_id > 0) {
    // Add new maintenance record
    $query = "INSERT INTO vehicle_maintenance (
        vehicle_id, maintenance_type, description, maintenance_date, 
        completion_date, cost, status, technician, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "issssdsss",
        $vehicle_id,
        $_POST['maintenance_type'],
        $_POST['description'] ?? null,
        $_POST['maintenance_date'],
        $_POST['completion_date'] ?? null,
        $_POST['cost'] ?? null,
        $_POST['status'],
        $_POST['technician'] ?? null,
        $_POST['notes'] ?? null
    );
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Maintenance record added successfully!";
        header("Location: app-fleet-view.php?id=$vehicle_id");
    } else {
        die("Error adding maintenance record: " . $conn->error);
    }
} else {
    die("Invalid action or vehicle ID.");
}

$stmt->close();
$conn->close();
?>