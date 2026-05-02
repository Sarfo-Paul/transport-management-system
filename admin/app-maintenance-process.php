<?php
require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false, 
    'message' => 'An unexpected error occurred',
    'errors' => []
];

try {
    // Check if request is POST
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Invalid request method");
    }

    // Validate required fields
    $requiredFields = ['vehicleId', 'maintenanceType', 'maintenanceDate', 'status'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $response['errors'][$field] = "This field is required";
        }
    }

    // If there are validation errors
    if (!empty($response['errors'])) {
        $response['message'] = "Validation errors occurred";
        echo json_encode($response);
        exit;
    }

    // Sanitize input data
    $vehicleId = intval($_POST['vehicleId']);
    $maintenanceType = $conn->real_escape_string(trim($_POST['maintenanceType']));
    $maintenanceDate = $conn->real_escape_string(trim($_POST['maintenanceDate']));
    $completionDate = !empty($_POST['completionDate']) ? $conn->real_escape_string(trim($_POST['completionDate'])) : null;
    $status = $conn->real_escape_string(trim($_POST['status']));
    $cost = isset($_POST['cost']) && is_numeric($_POST['cost']) ? floatval($_POST['cost']) : null;
    $technician = isset($_POST['technician']) ? $conn->real_escape_string(trim($_POST['technician'])) : null;
    $description = isset($_POST['description']) ? $conn->real_escape_string(trim($_POST['description'])) : null;

    // Insert into database
    $sql = "INSERT INTO vehicle_maintenance (vehicle_id, maintenance_type, description, maintenance_date, completion_date, cost, status, technician) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }

    $stmt->bind_param("issssdss", 
        $vehicleId, 
        $maintenanceType, 
        $description, 
        $maintenanceDate, 
        $completionDate, 
        $cost, 
        $status, 
        $technician
    );

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Maintenance record added successfully';
        $response['maintenance_id'] = $stmt->insert_id;
    } else {
        throw new Exception("Database execute error: " . $stmt->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Maintenance Process Error: " . $e->getMessage());
}

// Ensure connection is closed
if (isset($conn)) {
    $conn->close();
}

// Return JSON response
echo json_encode($response);
exit;
?>