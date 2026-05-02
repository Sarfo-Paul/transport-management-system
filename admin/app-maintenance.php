<?php
require_once 'config.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// -------------------- Get maintenance records with vehicle info --------------------
$maintenanceSql = "SELECT m.*, v.make, v.model, v.registration_number 
                   FROM vehicle_maintenance m
                   JOIN vehicles v ON m.vehicle_id = v.vehicle_id
                   ORDER BY m.maintenance_date DESC
                   LIMIT $limit OFFSET $offset";

try {
    $maintenanceResult = $conn->query($maintenanceSql);
    if ($maintenanceResult === false) {
        throw new Exception("Could not retrieve maintenance records: " . $conn->error);
    }
} catch (Exception $e) {
    $maintenanceError = $e->getMessage();
    echo "<p style='color:red;'>$maintenanceError</p>";
}

// -------------------- Get vehicles for dropdown --------------------
$vehiclesResult = $conn->query("SELECT vehicle_id, make, model, registration_number FROM vehicles");

// -------------------- Get total number of vehicles (for pagination) --------------------
$totalQuery = "SELECT COUNT(*) as total FROM vehicles";
$totalResult = $conn->query($totalQuery);

if ($totalResult && $row = $totalResult->fetch_assoc()) {
    $totalRows = $row['total'];
    $totalPages = ceil($totalRows / $limit);
} else {
    $totalRows = 0;
    $totalPages = 0;
    echo "<p style='color:red;'>Error retrieving total vehicles: " . $conn->error . "</p>";
}

// -------------------- Get vehicles with pagination --------------------
$sql = "SELECT * FROM vehicles ORDER BY vehicle_id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if ($result === false) {
    echo "<p style='color:red;'>Error retrieving vehicle list: " . $conn->error . "</p>";
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
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Vehicle Maintenance</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                                <i class="bx bx-plus me-1"></i> Add Maintenance Record
                            </button>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Vehicle</th>
                <th>Maintenance Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Cost</th>
                <th>Technician</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($maintenanceResult && $maintenanceResult->num_rows > 0): ?>
                <?php while($row = $maintenanceResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['make'] . ' ' . $row['model'] . ' (' . $row['registration_number'] . ')'); ?></td>
                        <td><?php echo htmlspecialchars($row['maintenance_type']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($row['maintenance_date'])); ?></td>
                        <td>
                            <?php 
                            $statusClass = '';
                            switch($row['status']) {
                                case 'Scheduled': $statusClass = 'badge bg-label-info'; break;
                                case 'In Progress': $statusClass = 'badge bg-label-warning'; break;
                                case 'Completed': $statusClass = 'badge bg-label-success'; break;
                                case 'Cancelled': $statusClass = 'badge bg-label-danger'; break;
                                default: $statusClass = 'badge bg-label-secondary';
                            }
                            ?>
                            <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                        </td>
                        <td><?php echo $row['cost'] ? 'GHS ' . number_format($row['cost'], 2) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($row['technician'] ?: 'N/A'); ?></td>
                       <td>
    <div class="dropdown">
        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
            <i class="bx bx-dots-vertical-rounded"></i>
        </button>
        <div class="dropdown-menu">
            <button class="dropdown-item view-maintenance" data-id="<?php echo $row['maintenance_id']; ?>">
                <i class="bx bx-show me-1"></i> View
            </button>
            <button class="dropdown-item edit-maintenance" data-id="<?php echo $row['maintenance_id']; ?>">
                <i class="bx bx-edit-alt me-1"></i> Edit
            </button>
            <button class="dropdown-item delete-maintenance" data-id="<?php echo $row['maintenance_id']; ?>">
                <i class="bx bx-trash me-1"></i> Delete
            </button>
         
    </div>
</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No maintenance records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Maintenance Modal -->
    <div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Maintenance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMaintenanceForm" action="app-maintenance-process.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicleId" class="form-label">Vehicle</label>
                                    <select class="form-select" id="vehicleId" name="vehicleId" required>
                                        <option value="">Select Vehicle</option>
                                        <?php while($vehicle = $vehiclesResult->fetch_assoc()): ?>
                                            <option value="<?php echo $vehicle['vehicle_id']; ?>">
                                                <?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['registration_number'] . ')'); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="maintenanceType" class="form-label">Maintenance Type</label>
                                    <select class="form-select" id="maintenanceType" name="maintenanceType" required>
                                        <option value="">Select Type</option>
                                        <option value="Routine">Routine</option>
                                        <option value="Repair">Repair</option>
                                        <option value="Inspection">Inspection</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="maintenanceDate" class="form-label">Maintenance Date</label>
                                    <input type="date" class="form-control" id="maintenanceDate" name="maintenanceDate" required>
                                </div>
                                <div class="mb-3">
                                    <label for="completionDate" class="form-label">Completion Date</label>
                                    <input type="date" class="form-control" id="completionDate" name="completionDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Scheduled">Scheduled</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Cost (GHS)</label>
                                    <input type="number" class="form-control" id="cost" name="cost" step="0.01" min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="technician" class="form-label">Technician</label>
                                    <input type="text" class="form-control" id="technician" name="technician">
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Maintenance Modal -->
    <div class="modal fade" id="viewMaintenanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maintenance Record Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="maintenanceDetails">
                    <!-- Details will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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

    
    <script>
   document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.view-maintenance').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            // Directly load the maintenance-view.php with the ID parameter
            window.location.href = 'app-maintenance-view.php?id=' + id;
        });
    });
});
        // Edit maintenance record (similar to view but with form)
        $('.edit-maintenance').click(function() {
            var maintenanceId = $(this).data('id');
            // Load edit form via AJAX or redirect to edit page
            window.location.href = 'app-maintenance-edit.php?id=' + maintenanceId;
        });

        // Delete maintenance record
        $('.delete-maintenance').click(function() {
            if (confirm('Are you sure you want to delete this maintenance record?')) {
                var maintenanceId = $(this).data('id');
                window.location.href = 'app-maintenance-delete.php?id=' + maintenanceId;
            }
        });

        // Form submission for adding maintenance
        $('#addMaintenanceForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
    </script>
</body>
</html>