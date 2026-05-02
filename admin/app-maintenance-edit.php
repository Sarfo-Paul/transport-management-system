<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: app-maintenance.php");
    exit();
}

$maintenanceId = intval($_GET['id']);
$errors = [];
$success = false;

// Get maintenance record
$sql = "SELECT * FROM vehicle_maintenance WHERE maintenance_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maintenanceId);
$stmt->execute();
$result = $stmt->get_result();
$maintenance = $result->fetch_assoc();

if (!$maintenance) {
    header("Location: app-maintenance.php");
    exit();
}

// Get all vehicles for dropdown
$vehiclesQuery = "SELECT vehicle_id, make, model, registration_number FROM vehicles ORDER BY make, model";
$vehiclesResult = $conn->query($vehiclesQuery);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $vehicleId = intval($_POST['vehicleId']);
    $maintenanceType = $conn->real_escape_string($_POST['maintenanceType']);
    $maintenanceDate = $conn->real_escape_string($_POST['maintenanceDate']);
    $completionDate = isset($_POST['completionDate']) && !empty($_POST['completionDate']) ? $conn->real_escape_string($_POST['completionDate']) : null;
    $status = $conn->real_escape_string($_POST['status']);
    $cost = isset($_POST['cost']) && !empty($_POST['cost']) ? floatval($_POST['cost']) : null;
    $technician = isset($_POST['technician']) ? $conn->real_escape_string($_POST['technician']) : null;
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : null;
    $notes = isset($_POST['notes']) ? $conn->real_escape_string($_POST['notes']) : null;

    // Update database
    $sql = "UPDATE vehicle_maintenance SET 
            vehicle_id = ?, 
            maintenance_type = ?, 
            description = ?, 
            maintenance_date = ?, 
            completion_date = ?, 
            cost = ?, 
            status = ?, 
            technician = ?,
            notes = ?
            WHERE maintenance_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssdsssi", $vehicleId, $maintenanceType, $description, $maintenanceDate, $completionDate, $cost, $status, $technician, $notes, $maintenanceId);
    
    if ($stmt->execute()) {
        $success = true;
        header("Location: app-maintenance.php?success=1");
        exit();
    } else {
        $errors[] = "Error updating maintenance record: " . $conn->error;
    }
}
?>
     <?php include 'includes/header.php'; ?>                    
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
       
        <?php include 'includes/menu.php'; ?>                    

            
            <!-- Layout container -->
            <div class="layout-page">
            <?php include 'includes/navbar.php'; ?>
                
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
              
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Edit Maintenance Record</h5>
                            <a href="app-maintenance.php" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-chevron-left me-1"></i> Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form id="editMaintenanceForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $maintenanceId); ?>" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="vehicleId" class="form-label">Vehicle</label>
                                            <select class="form-select" id="vehicleId" name="vehicleId" required>
                                                <option value="">Select Vehicle</option>
                                                <?php while($vehicle = $vehiclesResult->fetch_assoc()): ?>
                                                    <option value="<?php echo $vehicle['vehicle_id']; ?>" <?php echo $vehicle['vehicle_id'] == $maintenance['vehicle_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['registration_number'] . ')'); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="maintenanceType" class="form-label">Maintenance Type</label>
                                            <select class="form-select" id="maintenanceType" name="maintenanceType" required>
                                                <option value="">Select Type</option>
                                                <option value="Routine" <?php echo $maintenance['maintenance_type'] == 'Routine' ? 'selected' : ''; ?>>Routine</option>
                                                <option value="Repair" <?php echo $maintenance['maintenance_type'] == 'Repair' ? 'selected' : ''; ?>>Repair</option>
                                                <option value="Inspection" <?php echo $maintenance['maintenance_type'] == 'Inspection' ? 'selected' : ''; ?>>Inspection</option>
                                                <option value="Other" <?php echo $maintenance['maintenance_type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="maintenanceDate" class="form-label">Maintenance Date</label>
                                            <input type="date" class="form-control" id="maintenanceDate" name="maintenanceDate" value="<?php echo htmlspecialchars($maintenance['maintenance_date']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="completionDate" class="form-label">Completion Date</label>
                                            <input type="date" class="form-control" id="completionDate" name="completionDate" value="<?php echo htmlspecialchars($maintenance['completion_date']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="Scheduled" <?php echo $maintenance['status'] == 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                                <option value="In Progress" <?php echo $maintenance['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Completed" <?php echo $maintenance['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="Cancelled" <?php echo $maintenance['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cost" class="form-label">Cost (GHS)</label>
                                            <input type="number" class="form-control" id="cost" name="cost" step="0.01" min="0" value="<?php echo htmlspecialchars($maintenance['cost']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="technician" class="form-label">Technician</label>
                                            <input type="text" class="form-control" id="technician" name="technician" value="<?php echo htmlspecialchars($maintenance['technician']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($maintenance['description']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($maintenance['notes']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">Update Record</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    <!-- / Content -->
                    
                    <?php include 'includes/footer.php'; ?>                    

                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
    <?php include 'includes/scripts.php'; ?>                    

 
</body>
</html>