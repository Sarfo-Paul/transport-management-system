<?php
require_once 'config.php';

$scheduleId = $_GET['id'] ?? null;

if (!$scheduleId) {
    header('Location: app-driver-schedule.php');
    exit;
}

// Fetch schedule data
try {
    $stmt = $conn->prepare("
        SELECT rs.*, d.first_name, d.last_name, d.license_number,
               r.route_name, v.make, v.model, v.registration_number
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
        header('Location: app-driver-schedule.php');
        exit;
    }
    
    // Fetch all drivers, routes, vehicles for dropdowns
    $drivers = $conn->query("
        SELECT driver_id, CONCAT(first_name, ' ', last_name) AS name, license_number 
        FROM drivers 
        WHERE status = 'Active'
        ORDER BY name
    ")->fetch_all(MYSQLI_ASSOC);
    
    $routes = $conn->query("SELECT route_id, route_name FROM routes WHERE status = 'Active' ORDER BY route_name")->fetch_all(MYSQLI_ASSOC);
    
    $vehicles = $conn->query("SELECT vehicle_id, make, model, registration_number FROM vehicles WHERE status = 'Active' ORDER BY make, model")->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("
            UPDATE route_schedules SET
                route_id = ?,
                vehicle_id = ?,
                driver_id = ?,
                day_of_week = ?,
                departure_time = ?,
                arrival_time = ?,
                status = ?,
                notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE schedule_id = ?
        ");
        
        // Sanitize and validate inputs
        $route_id = !empty($_POST['route_id']) ? (int)$_POST['route_id'] : null;
        $vehicle_id = !empty($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : null;
        $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
        $day_of_week = trim($_POST['day_of_week']);
        $departure_time = trim($_POST['departure_time']);
        $arrival_time = trim($_POST['arrival_time']);
        $status = trim($_POST['status']);
        $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : null;
        
        $stmt->bind_param(
            "iiisssssi",
            $route_id,
            $vehicle_id,
            $driver_id,
            $day_of_week,
            $departure_time,
            $arrival_time,
            $status,
            $notes,
            $scheduleId
        );
        
        if ($stmt->execute()) {
            header('Location: app-driver-schedule.php');
            exit;
        } else {
            throw new Exception("Failed to execute query");
        }
    } catch (Exception $e) {
        $error = "Error updating schedule: " . $e->getMessage();
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
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Edit Schedule</h5>
                                        <a href="app-driver-schedule.php" class="btn btn-sm btn-outline-secondary">
                                            <i class="bx bx-chevron-left me-1"></i> Back to Schedules
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <?php if (isset($error)): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                        <?php endif; ?>
                                        
                                        <form method="POST">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="route_id" class="form-label">Route</label>
                                                        <select class="form-select" id="route_id" name="route_id">
                                                            <option value="">Select Route</option>
                                                            <?php foreach ($routes as $route): ?>
                                                                <option value="<?= $route['route_id'] ?>" 
                                                                    <?= ($route['route_id'] == $schedule['route_id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($route['route_name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="driver_id" class="form-label">Driver</label>
                                                        <select class="form-select" id="driver_id" name="driver_id">
                                                            <option value="">Select Driver</option>
                                                            <?php foreach ($drivers as $driver): ?>
                                                                <option value="<?= $driver['driver_id'] ?>" 
                                                                    <?= ($driver['driver_id'] == $schedule['driver_id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($driver['name'] . ' (' . $driver['license_number'] . ')') ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="vehicle_id" class="form-label">Vehicle</label>
                                                        <select class="form-select" id="vehicle_id" name="vehicle_id">
                                                            <option value="">Select Vehicle</option>
                                                            <?php foreach ($vehicles as $vehicle): ?>
                                                                <option value="<?= $vehicle['vehicle_id'] ?>" 
                                                                    <?= ($vehicle['vehicle_id'] == $schedule['vehicle_id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['registration_number'] . ')') ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="day_of_week" class="form-label">Day of Week</label>
                                                        <select class="form-select" id="day_of_week" name="day_of_week" required>
                                                            <option value="">Select Day</option>
                                                            <option value="Monday" <?= ($schedule['day_of_week'] == 'Monday') ? 'selected' : '' ?>>Monday</option>
                                                            <option value="Tuesday" <?= ($schedule['day_of_week'] == 'Tuesday') ? 'selected' : '' ?>>Tuesday</option>
                                                            <option value="Wednesday" <?= ($schedule['day_of_week'] == 'Wednesday') ? 'selected' : '' ?>>Wednesday</option>
                                                            <option value="Thursday" <?= ($schedule['day_of_week'] == 'Thursday') ? 'selected' : '' ?>>Thursday</option>
                                                            <option value="Friday" <?= ($schedule['day_of_week'] == 'Friday') ? 'selected' : '' ?>>Friday</option>
                                                            <option value="Saturday" <?= ($schedule['day_of_week'] == 'Saturday') ? 'selected' : '' ?>>Saturday</option>
                                                            <option value="Sunday" <?= ($schedule['day_of_week'] == 'Sunday') ? 'selected' : '' ?>>Sunday</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="departure_time" class="form-label">Departure Time</label>
                                                            <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                                                   value="<?= substr($schedule['departure_time'], 0, 5) ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="arrival_time" class="form-label">Arrival Time</label>
                                                            <input type="time" class="form-control" id="arrival_time" name="arrival_time" 
                                                                   value="<?= substr($schedule['arrival_time'], 0, 5) ?>" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select class="form-select" id="status" name="status" required>
                                                            <option value="Scheduled" <?= ($schedule['status'] == 'Scheduled') ? 'selected' : '' ?>>Scheduled</option>
                                                            <option value="Completed" <?= ($schedule['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                                            <option value="Cancelled" <?= ($schedule['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                                            <option value="Delayed" <?= ($schedule['status'] == 'Delayed') ? 'selected' : '' ?>>Delayed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="notes" class="form-label">Notes</label>
                                                <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($schedule['notes'] ?? '') ?></textarea>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bx bx-save me-1"></i> Update Schedule
                                                </button>
                                                
                                                <button type="button" class="btn btn-danger delete-schedule" data-id="<?= $scheduleId ?>">
                                                    <i class="bx bx-trash me-1"></i> Delete Schedule
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Schedule Details Card -->
                                <div class="card schedule-card schedule-<?= strtolower($schedule['status']) ?>">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Schedule Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Driver:</label>
                                                    <p class="form-control-static">
                                                        <?= $schedule['first_name'] ? htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) : 'N/A' ?>
                                                        <?php if ($schedule['license_number']): ?>
                                                            <br><small class="text-muted">License: <?= htmlspecialchars($schedule['license_number']) ?></small>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Vehicle:</label>
                                                    <p class="form-control-static">
                                                        <?= $schedule['make'] ? htmlspecialchars($schedule['make'] . ' ' . $schedule['model']) : 'N/A' ?>
                                                        <?php if ($schedule['registration_number']): ?>
                                                            <br><small class="text-muted">Reg: <?= htmlspecialchars($schedule['registration_number']) ?></small>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Route:</label>
                                                    <p class="form-control-static">
                                                        <?= $schedule['route_name'] ? htmlspecialchars($schedule['route_name']) : 'N/A' ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Status:</label>
                                                    <p class="form-control-static">
                                                        <?php 
                                                        $badgeClass = [
                                                            'Scheduled' => 'bg-label-primary',
                                                            'Completed' => 'bg-label-success',
                                                            'Cancelled' => 'bg-label-danger',
                                                            'Delayed' => 'bg-label-warning'
                                                        ][$schedule['status']] ?? 'bg-label-secondary';
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= $schedule['status'] ?></span>
                                                    </p>
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

    
    <script>
    $(document).ready(function() {
        // Delete schedule confirmation
        $('.delete-schedule').click(function() {
            const scheduleId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'app-schedule-delete.php?id=' + scheduleId;
                }
            });
        });
    });
    </script>
</body>
</html>