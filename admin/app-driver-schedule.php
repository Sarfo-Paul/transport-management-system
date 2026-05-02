<?php
require_once 'config.php';

// Fetch all drivers for dropdown
try {
    $drivers = $conn->query("
        SELECT driver_id, CONCAT(first_name, ' ', last_name) AS name, license_number 
        FROM drivers 
        WHERE status = 'Active'
        ORDER BY name
    ")->fetch_all(MYSQLI_ASSOC);
    
    $routes = $conn->query("SELECT route_id, route_name FROM routes WHERE status = 'Active' ORDER BY route_name")->fetch_all(MYSQLI_ASSOC);
    $vehicles = $conn->query("SELECT vehicle_id, make, model, registration_number FROM vehicles WHERE status = 'Active' ORDER BY make, model")->fetch_all(MYSQLI_ASSOC);
    
    // Fetch schedules
    $schedules = $conn->query("
        SELECT rs.*, d.first_name, d.last_name, d.license_number, 
               r.route_name, v.make, v.model, v.registration_number
        FROM route_schedules rs
        LEFT JOIN drivers d ON rs.driver_id = d.driver_id
        LEFT JOIN routes r ON rs.route_id = r.route_id
        LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
        ORDER BY rs.day_of_week, rs.departure_time
    ")->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize inputs
        $route_id = !empty($_POST['route_id']) ? (int)$_POST['route_id'] : null;
        $vehicle_id = !empty($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : null;
        $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
        $day_of_week = trim($_POST['day_of_week']);
        $departure_time = trim($_POST['departure_time']);
        $arrival_time = trim($_POST['arrival_time']);
        $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : null;
        $status = 'Scheduled'; // Default status

        // Prepare the statement
        $stmt = $conn->prepare("
            INSERT INTO route_schedules (
                route_id, vehicle_id, driver_id, day_of_week, 
                departure_time, arrival_time, status, notes
            ) VALUES (
                ?, ?, ?, ?,
                ?, ?, ?, ?
            )
        ");
        
        // Bind parameters
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
        
        // Execute the statement
        if ($stmt->execute()) {
            header('Location: app-driver-schedule.php');
            exit;
        } else {
            throw new Exception("Failed to execute query");
        }
    } catch (Exception $e) {
        $error = "Error adding schedule: " . $e->getMessage();
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
<div class="container-xxl">
    <div class="row">
        <!-- Add Schedule Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Add New Schedule
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="route_id" class="form-label">Route</label>
                            <select class="form-select" id="route_id" name="route_id">
                                <option value="">Select Route</option>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?= $route['route_id'] ?>">
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
                                    <option value="<?= $driver['driver_id'] ?>">
                                        <?= htmlspecialchars($driver['first_name']) . ' ' . $driver['last_name'] . ' (' . $driver['license_number'] . ')' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="vehicle_id" class="form-label">Vehicle</label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id">
                                <option value="">Select Vehicle</option>
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <option value="<?= $vehicle['vehicle_id'] ?>">
                                        <?= htmlspecialchars($vehicle['make']) . ' ' . $vehicle['model'] . ' (' . $vehicle['registration_number'] . ')' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Scheduled">Scheduled</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="Delayed">Delayed</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="departure_time" class="form-label">Departure Time</label>
                                <input type="time" class="form-control" id="departure_time" name="departure_time" required>
                            </div>
                            <div class="col-md-6">
                                <label for="arrival_time" class="form-label">Arrival Time</label>
                                <input type="time" class="form-control" id="arrival_time" name="arrival_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-1"></i> Add Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Schedule List Card -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>All Schedules
                        </h5>
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search schedules...">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-white">
                                <tr>
                                    <th class="ps-4">Day</th>
                                    <th>Time</th>
                                    <th>Route</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4" style="width: 80px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?= $schedule['day_of_week'] ?></td>
                                    <td>
                                        <div class="fw-semibold">
                                            <?= date('g:i A', strtotime($schedule['departure_time'])) ?> - 
                                            <?= date('g:i A', strtotime($schedule['arrival_time'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($schedule['route_name'] ?? 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <?php if ($schedule['first_name']): ?>
                                            <div><?= htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) ?></div>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($schedule['make']): ?>
                                            <div><?= htmlspecialchars($schedule['make'] . ' ' . $schedule['model']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($schedule['registration_number']) ?></small>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $badgeClass = [
                                            'Scheduled' => 'bg-primary',
                                            'Completed' => 'bg-success',
                                            'Cancelled' => 'bg-danger',
                                            'Delayed' => 'bg-warning text-dark'
                                        ][$schedule['status']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $schedule['status'] ?></span>
                                    </td>
                                    <td>
    <div class="dropdown">
        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
            <i class="bx bx-dots-vertical-rounded"></i>
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="app-schedule-view.php?id=<?= $schedule['schedule_id'] ?>">
                <i class="bx bx-show me-1"></i> View
            </a>
            <a class="dropdown-item" href="app-schedule-edit.php?id=<?= $schedule['schedule_id'] ?>">
                <i class="bx bx-edit-alt me-1"></i> Edit
            </a>
            <a class="dropdown-item" href="app-schedule-delete.php?id=<?= $schedule['schedule_id'] ?>">
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
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing 1 to 10 of <?= count($schedules) ?> entries
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/scripts.php'; ?>

    
    <script>
$(document).ready(function() {
    // Delete schedule
    $('.delete-schedule').click(function() {
        const scheduleId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/schedule-delete.php',
                    method: 'POST',
                    data: { id: scheduleId },
                    dataType: 'json'
                }).done(function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error!', 'Failed to delete schedule', 'error');
                });
            }
        });
    });
});
</script>
</body>
</html>