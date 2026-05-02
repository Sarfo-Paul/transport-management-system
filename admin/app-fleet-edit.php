
<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: app-fleet-list.php");
    exit();
}

$vehicle_id = (int)$_GET['id'];

// Fetch vehicle details
$query = "SELECT * FROM vehicles WHERE vehicle_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    header("Location: app-fleet-list.php");
    exit();
}

$title = "Edit Vehicle: " . $vehicle['make'] . " " . $vehicle['model'];
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
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Edit Vehicle</h4>
                <div>
                    <a href="app-fleet-view.php?id=<?= $vehicle_id ?>" class="btn btn-info me-2">
                        <i class="bx bx-show me-1"></i> View Details
                    </a>
                    <a href="app-fleet-list.php" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Fleet
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Vehicle Information</h5>
        </div>
        <div class="card-body">
            <form action="process-vehicle.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="vehicle_id" value="<?= $vehicle_id ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                                <option value="Bus" <?= $vehicle['vehicle_type'] == 'Bus' ? 'selected' : '' ?>>Bus</option>
                                <option value="Minibus" <?= $vehicle['vehicle_type'] == 'Minibus' ? 'selected' : '' ?>>Minibus</option>
                                <option value="Van" <?= $vehicle['vehicle_type'] == 'Van' ? 'selected' : '' ?>>Van</option>
                                <option value="Truck" <?= $vehicle['vehicle_type'] == 'Truck' ? 'selected' : '' ?>>Truck</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="make" class="form-label">Make</label>
                            <input type="text" class="form-control" id="make" name="make" value="<?= htmlspecialchars($vehicle['make']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($vehicle['model']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" min="1900" max="<?= date('Y') + 1 ?>" value="<?= $vehicle['year'] ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="registration_number" class="form-label">Registration Number</label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= htmlspecialchars($vehicle['registration_number']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vin" class="form-label">VIN</label>
                            <input type="text" class="form-control" id="vin" name="vin" value="<?= htmlspecialchars($vehicle['vin']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= $vehicle['purchase_date'] ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacity (Passengers)</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" value="<?= $vehicle['capacity'] ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="fuel_type" name="fuel_type" required>
                                <option value="Petrol" <?= $vehicle['fuel_type'] == 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                                <option value="Diesel" <?= $vehicle['fuel_type'] == 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                <option value="Electric" <?= $vehicle['fuel_type'] == 'Electric' ? 'selected' : '' ?>>Electric</option>
                                <option value="Hybrid" <?= $vehicle['fuel_type'] == 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="current_mileage" class="form-label">Current Mileage (km)</label>
                            <input type="number" step="0.01" class="form-control" id="current_mileage" name="current_mileage" value="<?= $vehicle['current_mileage'] ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Active" <?= $vehicle['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Maintenance" <?= $vehicle['status'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                <option value="Out of Service" <?= $vehicle['status'] == 'Out of Service' ? 'selected' : '' ?>>Out of Service</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Vehicle Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <?php if ($vehicle['image_path']): ?>
                            <div class="form-text">
                                Current image: <a href="<?= $vehicle['image_path'] ?>" target="_blank">View</a>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage">
                                    <label class="form-check-label" for="removeImage">Remove image</label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="reset" class="btn btn-secondary">Reset Changes</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
// Display the filename when a file is selected
$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
</script>

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