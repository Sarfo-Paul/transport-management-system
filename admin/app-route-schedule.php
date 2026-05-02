<?php
require_once 'config.php';

// Fetch all routes
$routes = $conn->query("SELECT route_id, route_name FROM routes WHERE status = 'Active' ORDER BY route_name")->fetch_all(MYSQLI_ASSOC);

// Fetch all drivers
$drivers = $conn->query("
    SELECT driver_id, CONCAT(first_name, ' ', last_name) AS name, license_number 
    FROM drivers 
    WHERE status = 'Active'
    ORDER BY name
")->fetch_all(MYSQLI_ASSOC);

// Fetch all vehicles
$vehicles = $conn->query("SELECT vehicle_id, make, model, registration_number FROM vehicles WHERE status = 'Active' ORDER BY make, model")->fetch_all(MYSQLI_ASSOC);

// Fetch all schedules with route, driver, and vehicle information
$schedules = $conn->query("
    SELECT rs.*, 
           r.route_name, 
           CONCAT(d.first_name, ' ', d.last_name) AS driver_name,
           d.license_number,
           v.make, v.model, v.registration_number
    FROM route_schedules rs
    LEFT JOIN routes r ON rs.route_id = r.route_id
    LEFT JOIN drivers d ON rs.driver_id = d.driver_id
    LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
    ORDER BY rs.day_of_week, rs.departure_time
")->fetch_all(MYSQLI_ASSOC);

// Handle form submission for adding new schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    try {
        $route_id = !empty($_POST['route_id']) ? (int)$_POST['route_id'] : null;
        $vehicle_id = !empty($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : null;
        $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
        $day_of_week = trim($_POST['day_of_week']);
        $departure_time = trim($_POST['departure_time']);
        $arrival_time = trim($_POST['arrival_time']);
        $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : null;
        $status = 'Scheduled';

        $stmt = $conn->prepare("
            INSERT INTO route_schedules (
                route_id, vehicle_id, driver_id, day_of_week, 
                departure_time, arrival_time, status, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "iiisssss",
            $route_id,
            $vehicle_id,
            $driver_id,
            $day_of_week,
            $departure_time,
            $arrival_time,
            $status,
            $notes
        );
        
        if ($stmt->execute()) {
            header('Location: app-schedule-combined.php');
            exit;
        } else {
            $error = "Failed to add schedule";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle schedule deletion
if (isset($_GET['delete_id'])) {
    $schedule_id = (int)$_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM route_schedules WHERE schedule_id = ?");
        $stmt->bind_param("i", $schedule_id);
        if ($stmt->execute()) {
            header('Location: app-schedule-combined.php');
            exit;
        }
    } catch (Exception $e) {
        $error = "Error deleting schedule: " . $e->getMessage();
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
                <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Route Schedules</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
            <i class="bx bx-plus me-1"></i> Add Schedule
        </button>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Route</th>
                            <th>Driver</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?= htmlspecialchars($schedule['day_of_week']) ?></td>
                            <td>
                                <?= date('g:i A', strtotime($schedule['departure_time'])) ?> - 
                                <?= date('g:i A', strtotime($schedule['arrival_time'])) ?>
                            </td>
                            <td><?= htmlspecialchars($schedule['route_name'] ?? 'N/A') ?></td>
                            <td>
                                <?= htmlspecialchars($schedule['driver_name'] ?? 'N/A') ?>
                                <?php if ($schedule['license_number']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($schedule['license_number']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($schedule['make']): ?>
                                    <?= htmlspecialchars($schedule['make'] . ' ' . $schedule['model']) ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($schedule['registration_number']) ?></small>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= [
                                    'Scheduled' => 'primary',
                                    'Completed' => 'success',
                                    'Cancelled' => 'danger',
                                    'Delayed' => 'warning'
                                ][$schedule['status']] ?? 'primary' ?>">
                                    <?= $schedule['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="app-schedule-edit.php?id=<?= $schedule['schedule_id'] ?>">
                                            <i class="bx bx-edit me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="app-schedule-combined.php?delete_id=<?= $schedule['schedule_id'] ?>" 
                                           onclick="return confirm('Are you sure?')">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

                    <!-- Add Schedule Modal -->
                    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form method="POST" action="">
                                    <input type="hidden" name="add_schedule" value="1">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Schedule</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="route_id" class="form-label">Route</label>
                                                <select class="form-select" id="route_id" name="route_id" required>
                                                    <option value="">Select Route</option>
                                                    <?php foreach ($routes as $route): ?>
                                                        <option value="<?= $route['route_id'] ?>">
                                                            <?= htmlspecialchars($route['route_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="driver_id" class="form-label">Driver</label>
                                                <select class="form-select" id="driver_id" name="driver_id">
                                                    <option value="">Select Driver</option>
                                                    <?php foreach ($drivers as $driver): ?>
                                                        <option value="<?= $driver['driver_id'] ?>">
                                                            <?= htmlspecialchars($driver['name'] . ' (' . $driver['license_number'] . ')') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="vehicle_id" class="form-label">Vehicle</label>
                                                <select class="form-select" id="vehicle_id" name="vehicle_id">
                                                    <option value="">Select Vehicle</option>
                                                    <?php foreach ($vehicles as $vehicle): ?>
                                                        <option value="<?= $vehicle['vehicle_id'] ?>">
                                                            <?= htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['registration_number'] . ')') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="day_of_week" class="form-label">Day of Week</label>
                                                <select class="form-select" id="day_of_week" name="day_of_week" required>
                                                    <option value="">Select Day</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="departure_time" class="form-label">Departure Time</label>
                                                <input type="time" class="form-control" id="departure_time" name="departure_time" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="arrival_time" class="form-label">Arrival Time</label>
                                                <input type="time" class="form-control" id="arrival_time" name="arrival_time" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="Scheduled">Scheduled</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                                <option value="Delayed">Delayed</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <?php include 'includes/footer.php'; ?>                    

                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <?php include 'includes/scripts.php'; ?>                    

    
    <!-- DataTables JS -->
    <script src="assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#schedulesTable').DataTable({
            responsive: true,
            order: [0, 'asc'], [1, 'asc'] // Sort by day then by time
        });
        
        // Form validation
        $('form').submit(function() {
            const departure = $('#departure_time').val();
            const arrival = $('#arrival_time').val();
            
            if (departure >= arrival) {
                alert('Arrival time must be after departure time');
                return false;
            }
            return true;
        });
    });
    </script>
</body>
</html>