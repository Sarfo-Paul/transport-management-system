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

// Fetch maintenance history
$maintenance_query = "SELECT * FROM vehicle_maintenance WHERE vehicle_id = ? ORDER BY maintenance_date DESC";
$stmt = $conn->prepare($maintenance_query);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$maintenance_history = $stmt->get_result();

$title = "View Vehicle: " . $vehicle['make'] . " " . $vehicle['model'];
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
                <h4 class="fw-bold mb-0">Vehicle Details</h4>
                <a href="app-fleet-list.php" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Fleet
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <?php if ($vehicle['image_path']): ?>
                    <div class="text-center mb-4">
                        <img src="<?= $vehicle['image_path'] ?>" class="img-fluid rounded" style="max-height: 200px;" alt="Vehicle Image">
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Vehicle ID:</strong> <?= $vehicle['vehicle_id'] ?></p>
                            <p><strong>Type:</strong> <?= $vehicle['vehicle_type'] ?></p>
                            <p><strong>Make:</strong> <?= $vehicle['make'] ?></p>
                            <p><strong>Model:</strong> <?= $vehicle['model'] ?></p>
                            <p><strong>Year:</strong> <?= $vehicle['year'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Registration:</strong> <?= $vehicle['registration_number'] ?></p>
                            <p><strong>VIN:</strong> <?= $vehicle['vin'] ?></p>
                            <p><strong>Fuel Type:</strong> <?= $vehicle['fuel_type'] ?></p>
                            <p><strong>Capacity:</strong> <?= $vehicle['capacity'] ?> passengers</p>
                            <p><strong>Mileage:</strong> <?= number_format($vehicle['current_mileage'], 2) ?> km</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Status:</strong> 
                                <span class="badge <?= 
                                    $vehicle['status'] == 'Active' ? 'bg-label-success' : 
                                    ($vehicle['status'] == 'Maintenance' ? 'bg-label-warning' : 'bg-label-danger') 
                                ?>">
                                    <?= $vehicle['status'] ?>
                                </span>
                            </p>
                            <p><strong>Purchase Date:</strong> <?= date('M j, Y', strtotime($vehicle['purchase_date'])) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="app-fleet-edit.php?id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-primary">Edit Vehicle</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Maintenance History</h5>
                </div>
                <div class="card-body">
                    <?php if ($maintenance_history->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($maintenance = $maintenance_history->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($maintenance['maintenance_date'])) ?></td>
                                    <td><?= $maintenance['maintenance_type'] ?></td>
                                    <td>
                                        <span class="badge <?= 
                                            $maintenance['status'] == 'Completed' ? 'bg-label-success' : 
                                            ($maintenance['status'] == 'In Progress' ? 'bg-label-warning' : 
                                            ($maintenance['status'] == 'Cancelled' ? 'bg-label-danger' : 'bg-label-primary')) 
                                        ?>">
                                            <?= $maintenance['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= $maintenance['cost'] ? 'GHS ' . number_format($maintenance['cost'], 2) : 'N/A' ?></td>
                                    <td>
                                        <a href="app-maintenance-view.php?id=<?= $maintenance['maintenance_id'] ?>" class="btn btn-sm btn-label-info">
                                            <i class="bx bx-show me-1"></i> Details
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No maintenance records found for this vehicle.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $('.view-maintenance').click(function() {
        var maintenance_id = $(this).data('id');
        
        $.ajax({
            url: 'get-maintenance-details.php',
            type: 'GET',
            data: { id: maintenance_id },
            success: function(response) {
                $('#maintenanceDetailsContent').html(response);
                $('#maintenanceDetailsModal').modal('show');
            },
            error: function() {
                alert('Error loading maintenance details.');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>                    

                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
    
    <?php include 'includes/scripts.php'; ?>                    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
</html>
<?php
$conn->close();
?>