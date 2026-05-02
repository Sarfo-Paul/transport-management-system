<?php
require_once 'config.php';

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate and sanitize driver ID
$driverId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$driverId) {
    header('Location: app-driver-list.php?error=invalid_id');
    exit;
}

// Fetch driver data with error handling
try {
    // Get driver details
    $stmt = $conn->prepare("SELECT * FROM drivers WHERE driver_id = ?");
    $stmt->bind_param("i", $driverId);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();
    
    if (!$driver) {
        header('Location: app-driver-list.php?error=not_found');
        exit;
    }
    
    // Get assigned schedules
    $schedStmt = $conn->prepare("
        SELECT rs.*, r.route_name, v.make, v.model, v.registration_number
        FROM route_schedules rs
        LEFT JOIN routes r ON rs.route_id = r.route_id
        LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
        WHERE rs.driver_id = ?
        ORDER BY 
            FIELD(rs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            rs.departure_time
    ");
    $schedStmt->bind_param("i", $driverId);
    $schedStmt->execute();
    $schedResult = $schedStmt->get_result();
    $schedules = $schedResult->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred while fetching driver data. Please try again later.");
}

// Format dates
$licenseExpiry = $driver['license_expiry'] ? date('M d, Y', strtotime($driver['license_expiry'])) : 'N/A';
$hireDate = $driver['hire_date'] ? date('M d, Y', strtotime($driver['hire_date'])) : 'N/A';

// Status badge classes
$statusBadgeClasses = [
    'Active' => 'bg-label-success',
    'On Leave' => 'bg-label-warning',
    'Suspended' => 'bg-label-danger',
    'Terminated' => 'bg-label-secondary'
];
$statusClass = $statusBadgeClasses[$driver['status']] ?? 'bg-label-secondary';

// Schedule status badge classes
$scheduleBadgeClasses = [
    'Scheduled' => 'bg-label-primary',
    'Completed' => 'bg-label-success',
    'Cancelled' => 'bg-label-danger',
    'Delayed' => 'bg-label-warning'
];
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
<div class="container-xxl">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="fas fa-user me-2"></i> Driver Details
                    </h4>
                    <div>
                        <a href="app-driver-edit.php?id=<?= $driverId ?>" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="app-driver-list.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <!-- Driver Photo -->
                    <?php if ($driver['photo_path']): ?>
                        <div class="mb-3">
                            <img src="<?= htmlspecialchars($driver['photo_path']) ?>" 
                                 class="rounded-circle" 
                                 width="150" height="150"
                                 alt="Driver Photo">
                        </div>
                    <?php else: ?>
                        <div class="avatar mb-3">
                            <span class="avatar-initial rounded-circle bg-secondary" style="width: 150px; height: 150px; font-size: 3rem;">
                                <?= strtoupper(substr($driver['first_name'], 0, 1) . substr($driver['last_name'], 0, 1)) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Driver Name and Status -->
                    <h4 class="mb-2"><?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?></h4>
                    <span class="badge <?= $statusClass ?> mb-3"><?= $driver['status'] ?></span>
                    
                    <!-- Driver Information -->
                    <div class="text-start mt-4">
                        <div class="mb-2">
                            <strong>Driver ID:</strong> <?= $driver['driver_id'] ?>
                        </div>
                        
                        <div class="mb-2">
                            <strong>License Number:</strong> <?= htmlspecialchars($driver['license_number']) ?>
                        </div>
                        
                        <div class="mb-2">
                            <strong>License Expiry:</strong> <?= $licenseExpiry ?>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Contact:</strong> <?= htmlspecialchars($driver['contact_number']) ?>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Email:</strong> <?= $driver['email'] ? htmlspecialchars($driver['email']) : 'N/A' ?>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Hire Date:</strong> <?= $hireDate ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Schedules Card -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i> Assigned Schedules
                    </h4>
                </div>
                <div class="card-body">
                    <?php if ($schedules): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Route</th>
                                        <th>Vehicle</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedules as $schedule): 
                                        $scheduleStatusClass = [
                                            'Scheduled' => 'bg-primary',
                                            'Completed' => 'bg-success',
                                            'Cancelled' => 'bg-danger',
                                            'Delayed' => 'bg-warning'
                                        ][$schedule['status']] ?? 'bg-secondary';
                                    ?>
                                    <tr>
                                        <td><?= $schedule['day_of_week'] ?></td>
                                        <td>
                                            <?= date('g:i A', strtotime($schedule['departure_time'])) ?> - 
                                            <?= date('g:i A', strtotime($schedule['arrival_time'])) ?>
                                        </td>
                                        <td><?= htmlspecialchars($schedule['route_name']) ?></td>
                                        <td>
                                            <?php if ($schedule['make']): ?>
                                                <?= htmlspecialchars($schedule['make'] . ' ' . $schedule['model']) ?>
                                                <small class="text-muted d-block"><?= htmlspecialchars($schedule['registration_number']) ?></small>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $scheduleStatusClass ?>">
                                                <?= $schedule['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No assigned schedules found for this driver.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
                    
                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © <script>document.write(new Date().getFullYear())</script> <strong>TransPass Pro</strong>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="footer-link me-4">Help</a>
                                <a href="javascript:void(0);" class="footer-link me-4">Contact</a>
                                <a href="javascript:void(0);" class="footer-link">Privacy</a>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Initialize tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>