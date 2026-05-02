<?php
require_once 'config.php';

// Check if maintenance ID is provided
if (!isset($_GET['id'])) {
    die('Maintenance ID not provided');
}

$maintenanceId = intval($_GET['id']);

// Get maintenance record details - make sure to include odometer_reading if it exists
$sql = "SELECT m.*, v.make, v.model, v.registration_number 
        FROM vehicle_maintenance m
        JOIN vehicles v ON m.vehicle_id = v.vehicle_id
        WHERE m.maintenance_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maintenanceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Maintenance record not found');
}

$maintenance = $result->fetch_assoc();
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
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Maintenance Record Details</h5>
                                        <a href="app-maintenance.php" class="btn btn-sm btn-secondary">
                                            <i class="bx bx-arrow-back me-1"></i> Back to List
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>Vehicle Information</h5>
                                                <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($maintenance['make'] . ' ' . $maintenance['model'] . ' (' . $maintenance['registration_number'] . ')'); ?></p>
                                                
                                                <h5 class="mt-4">Maintenance Details</h5>
                                                <p><strong>Type:</strong> <?php echo htmlspecialchars($maintenance['maintenance_type']); ?></p>
                                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($maintenance['maintenance_date'])); ?></p>
                                                <p><strong>Completion Date:</strong> <?php echo !empty($maintenance['completion_date']) ? date('F j, Y', strtotime($maintenance['completion_date'])) : 'N/A'; ?></p>
                                                <p><strong>Status:</strong> 
                                                    <?php 
                                                    $statusClass = '';
                                                    switch($maintenance['status']) {
                                                        case 'Scheduled': $statusClass = 'badge bg-label-info'; break;
                                                        case 'In Progress': $statusClass = 'badge bg-label-warning'; break;
                                                        case 'Completed': $statusClass = 'badge bg-label-success'; break;
                                                        case 'Cancelled': $statusClass = 'badge bg-label-danger'; break;
                                                        default: $statusClass = 'badge bg-label-secondary';
                                                    }
                                                    ?>
                                                    <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($maintenance['status']); ?></span>
                                                </p>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <h5>Service Information</h5>
                                                <p><strong>Technician:</strong> <?php echo htmlspecialchars($maintenance['technician'] ?? 'N/A'); ?></p>
                                                <p><strong>Cost:</strong> <?php echo isset($maintenance['cost']) ? 'GHS ' . number_format($maintenance['cost'], 2) : 'N/A'; ?></p>
                                                <p><strong>Odometer Reading:</strong> 
                                                    <?php 
                                                    if (isset($maintenance['odometer_reading']) && $maintenance['odometer_reading'] !== null) {
                                                        echo number_format($maintenance['odometer_reading']) . ' km';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </p>
                                                
                                                <h5 class="mt-4">Description</h5>
                                                <p><?php echo nl2br(htmlspecialchars($maintenance['description'] ?? 'No description provided')); ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($maintenance['attachment_path'])): ?>
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <h5>Attachments</h5>
                                                <a href="<?php echo htmlspecialchars($maintenance['attachment_path']); ?>" class="btn btn-outline-primary" target="_blank">
                                                    <i class="bx bx-file me-1"></i> View Attachment
                                                </a>
                                            </div>
                                        </div>
                                        <?php endif; ?>
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