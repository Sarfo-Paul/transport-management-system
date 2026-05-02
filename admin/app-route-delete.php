<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: app-route-list.php');
    exit();
}

$routeId = (int)$_GET['id'];

// Check if route exists
$stmt = $conn->prepare("SELECT route_name FROM routes WHERE route_id = ?");
$stmt->bind_param("i", $routeId);
$stmt->execute();
$result = $stmt->get_result();
$route = $result->fetch_assoc();
$stmt->close();

if (!$route) {
    header('Location: app-route-list.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete related records first (coordinates and schedules)
        $stmt = $conn->prepare("DELETE FROM route_coordinates WHERE route_id = ?");
        $stmt->bind_param("i", $routeId);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("DELETE FROM route_schedules WHERE route_id = ?");
        $stmt->bind_param("i", $routeId);
        $stmt->execute();
        $stmt->close();
        
        // Now delete the route
        $stmt = $conn->prepare("DELETE FROM routes WHERE route_id = ?");
        $stmt->bind_param("i", $routeId);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        
        $_SESSION['success_message'] = 'Route "' . htmlspecialchars($route['route_name']) . '" has been deleted successfully.';
        header('Location: app-route-list.php');
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = 'Error deleting route: ' . $e->getMessage();
        header("Location: app-route-view.php?id=$routeId");
        exit();
    }
}

?>



  