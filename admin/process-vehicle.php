<?php
require_once 'config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

$action = $_POST['action'] ?? '';
$vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;

// Required fields
$required_fields = [
    'vehicle_type', 'make', 'model', 'year', 'registration_number', 
    'vin', 'purchase_date', 'capacity', 'fuel_type', 'status'
];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') {
        die("Error: Required field '$field' is missing.");
    }
}

// Process image upload
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/vehicles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = 'vehicle_' . time() . '.' . strtolower($file_ext);
    $target_file = $upload_dir . $file_name;

    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        die("Error: File is not an image.");
    }

    if ($_FILES['image']['size'] > 5000000) {
        die("Error: Image is too large. Maximum size is 5MB.");
    }

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        die("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file;
    } else {
        die("Error: There was an error uploading your file.");
    }
}

// Assign POST variables to named variables
$vehicle_type = $_POST['vehicle_type'];
$make = $_POST['make'];
$model = $_POST['model'];
$year = $_POST['year'];
$registration_number = $_POST['registration_number'];
$vin = $_POST['vin'];
$purchase_date = $_POST['purchase_date'];
$capacity = $_POST['capacity'];
$fuel_type = $_POST['fuel_type'];
$current_mileage = $_POST['current_mileage'] ?? 0;

// Validate numeric fields
if (!preg_match('/^\d{4}$/', $year) || (int)$year < 1900 || (int)$year > (int)date('Y') + 1) {
    die('Error: Year must be a valid 4-digit year');
}
if (!is_numeric($capacity) || (int)$capacity < 1) {
    die('Error: Capacity must be a positive integer');
}
if ($current_mileage !== '' && !is_numeric($current_mileage)) {
    die('Error: Current mileage must be numeric');
}

// Validate dates
foreach (['purchase_date' => $purchase_date] as $label => $val) {
    $d = DateTime::createFromFormat('Y-m-d', $val);
    if (!($d && $d->format('Y-m-d') === $val)) {
        die("Error: $label must be a valid date (YYYY-MM-DD)");
    }
}
$status = $_POST['status'];

if ($action === 'add') {
    $query = "INSERT INTO vehicles (
        vehicle_type, make, model, year, registration_number, vin,
        purchase_date, capacity, fuel_type, current_mileage, status, image_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssissssisss",
        $vehicle_type, $make, $model, $year, $registration_number, $vin,
        $purchase_date, $capacity, $fuel_type, $current_mileage, $status, $image_path
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Vehicle added successfully!";
        header("Location: app-fleet-view.php?id=" . $stmt->insert_id);
        exit();
    } else {
        die("Error adding vehicle: " . $conn->error);
    }

} elseif ($action === 'edit' && $vehicle_id > 0) {
    // Fetch current image
    $current_image = null;
    $get_query = "SELECT image_path FROM vehicles WHERE vehicle_id = ?";
    $get_stmt = $conn->prepare($get_query);
    $get_stmt->bind_param("i", $vehicle_id);
    $get_stmt->execute();
    $get_stmt->bind_result($current_image);
    $get_stmt->fetch();
    $get_stmt->close();

    // Remove old image if requested
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == 1) {
        if ($current_image && file_exists($current_image)) {
            unlink($current_image);
        }
        $current_image = null;
    }

    $final_image_path = $image_path ?: $current_image;

    $query = "UPDATE vehicles SET 
        vehicle_type = ?, make = ?, model = ?, year = ?, registration_number = ?, vin = ?,
        purchase_date = ?, capacity = ?, fuel_type = ?, current_mileage = ?, status = ?, image_path = ?
        WHERE vehicle_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssissssisssi",
        $vehicle_type, $make, $model, $year, $registration_number, $vin,
        $purchase_date, $capacity, $fuel_type, $current_mileage, $status, $final_image_path, $vehicle_id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Vehicle updated successfully!";
        header("Location: app-fleet-view.php?id=$vehicle_id");
        exit();
    } else {
        die("Error updating vehicle: " . $conn->error);
    }
} else {
    die("Invalid action or vehicle ID.");
}

$stmt->close();
$conn->close();
?>
