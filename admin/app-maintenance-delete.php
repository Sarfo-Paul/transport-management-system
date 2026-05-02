<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: app-maintenance.php");
    exit();
}

$maintenanceId = intval($_GET['id']);

// Delete the maintenance record
$sql = "DELETE FROM vehicle_maintenance WHERE maintenance_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maintenanceId);

if ($stmt->execute()) {
    header("Location: app-maintenance.php?success=1");
} else {
    header("Location: app-maintenance.php?error=delete_failed");
}

exit();
?>