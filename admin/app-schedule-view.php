<?php
require_once __DIR__ . '/config.php';

$scheduleId = $_GET['id'] ?? null;

if (!$scheduleId) {
    header('Location: app-driver-schedule.php?error=Schedule ID is required');
    exit;
}

// Fetch schedule data
try {
    $stmt = $conn->prepare("
        SELECT rs.*, d.first_name, d.last_name, d.license_number, d.contact_number,
               r.route_name, r.start_point, r.end_point, r.distance, r.estimated_duration,
               v.make, v.model, v.registration_number, v.vehicle_type
        FROM route_schedules rs
        LEFT JOIN drivers d ON rs.driver_id = d.driver_id
        LEFT JOIN routes r ON rs.route_id = r.route_id
        LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
        WHERE rs.schedule_id = ?
    ");
    
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();
    
    if (!$schedule) {
        header('Location: app-driver-schedule.php?error=Schedule not found');
        exit;
    }
} catch (Exception $e) {
    header('Location: app-driver-schedule.php?error=' . urlencode($e->getMessage()));
    exit;
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
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Schedule Details</h5>
                                        <div>
                                            <a href="app-schedule-edit.php?id=<?= $schedule['schedule_id'] ?>" class="btn btn-sm btn-primary me-2">
                                                <i class="bx bx-edit me-1"></i> Edit
                                            </a>
                                            <a href="app-driver-schedule.php" class="btn btn-sm btn-outline-secondary">
                                                <i class="bx bx-chevron-left me-1"></i> Back to Schedules
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card detail-card">
                                                    <div class="card-body">
                                                        <h6 class="mb-3">Schedule Information</h6>
                                                        <div class="row mb-2">
                                                            <div class="col-md-4"><strong>Day:</strong></div>
                                                            <div class="col-md-8"><?= $schedule['day_of_week'] ?></div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-4"><strong>Time:</strong></div>
                                                            <div class="col-md-8">
                                                                <?= date('g:i A', strtotime($schedule['departure_time'])) ?> - 
                                                                <?= date('g:i A', strtotime($schedule['arrival_time'])) ?>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-4"><strong>Status:</strong></div>
                                                            <div class="col-md-8">
                                                                <?php 
                                                                $badgeClass = [
                                                                    'Scheduled' => 'bg-label-primary',
                                                                    'Completed' => 'bg-label-success',
                                                                    'Cancelled' => 'bg-label-danger',
                                                                    'Delayed' => 'bg-label-warning'
                                                                ][$schedule['status']] ?? 'bg-label-secondary';
                                                                ?>
                                                                <span class="badge schedule-badge <?= $badgeClass ?>"><?= $schedule['status'] ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-4"><strong>Notes:</strong></div>
                                                            <div class="col-md-8"><?= $schedule['notes'] ? htmlspecialchars($schedule['notes']) : 'N/A' ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card detail-card">
                                                    <div class="card-body">
                                                        <h6 class="mb-3">Route Information</h6>
                                                        <?php if ($schedule['route_name']): ?>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Route:</strong></div>
                                                                <div class="col-md-8"><?= htmlspecialchars($schedule['route_name']) ?></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>From:</strong></div>
                                                                <div class="col-md-8"><?= htmlspecialchars($schedule['start_point']) ?></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>To:</strong></div>
                                                                <div class="col-md-8"><?= htmlspecialchars($schedule['end_point']) ?></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Distance:</strong></div>
                                                                <div class="col-md-8"><?= $schedule['distance'] ?> km</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Duration:</strong></div>
                                                                <div class="col-md-8"><?= $schedule['estimated_duration'] ?> minutes</div>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-info">No route assigned</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card detail-card">
                                                    <div class="card-body">
                                                        <h6 class="mb-3">Driver Information</h6>
                                                        <?php if ($schedule['first_name']): ?>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Driver:</strong></div>
                                                                <div class="col-md-8">
                                                                    <?= htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) ?>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>License:</strong></div>
                                                                <div class="col-md-8"><?= htmlspecialchars($schedule['license_number']) ?></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Contact:</strong></div>
                                                                <div class="col-md-8"><?= htmlspecialchars($schedule['contact_number']) ?></div>
                                                            </div>
                                                            <div class="text-center mt-3">
                                                                <a href="app-driver-view.php?id=<?= $schedule['driver_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                    View Driver Profile
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-info">No driver assigned</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="card detail-card">
                                                    <div class="card-body">
                                                        <h6 class="mb-3">Vehicle Information</h6>
                                                        <?php if ($schedule['make']): ?>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Vehicle:</strong></div>
                                                                <div class="col-md-8">
                                                                    <?= htmlspecialchars($schedule['vehicle_type'] . ' - ' . $schedule['make'] . ' ' . $schedule['model']) ?>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Reg. No:</strong></div>
                                                                <div class="col-md-8"><?= htmlspecialchars($schedule['registration_number']) ?></div>
                                                            </div>
                                                            <div class="text-center mt-3">
                                                                <a href="app-fleet-view.php?id=<?= $schedule['vehicle_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                    View Vehicle Details
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-info">No vehicle assigned</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <?php include 'includes/footer.php'; ?>                    


                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <?php include 'includes/scripts.php'; ?>                    


</body>
</html>