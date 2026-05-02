<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: app-fleet-list.php");
    exit();
}

$vehicleId = intval($_GET['id']);

// Get vehicle image path to delete the file
$sql = "SELECT image_path FROM vehicles WHERE vehicle_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

// Delete the vehicle
$sql = "DELETE FROM vehicles WHERE vehicle_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicleId);

if ($stmt->execute()) {
    // Delete the image file if exists
    if (!empty($vehicle['image_path']) && file_exists($vehicle['image_path'])) {
        unlink($vehicle['image_path']);
    }
    header("Location: app-fleet-list.php?success=1");
} else {
    header("Location: app-fleet-list.php?error=delete_failed");
}

exit();
?>