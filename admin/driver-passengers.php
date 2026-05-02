<?php
require_once __DIR__ . '/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication and driver role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Debugging: Print session data
error_log("Session user_id: " . $_SESSION['user_id']);

// Get today's date
$today = date('Y-m-d');
$currentTime = date('H:i:s');
$currentDay = date('l'); // Full day name (e.g., "Monday")
$error = null;
$currentTrip = null;
$passengers = [];

try {
    // Debug database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // First get the driver_id from the user_id
    $stmt = $conn->prepare("SELECT driver_id FROM drivers WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();
    
    if (!$driver) {
        $error = "Driver profile not found";
    } else {
        error_log("Found driver ID: " . $driver['driver_id']);
        
        // Debug: Print current day and time
        error_log("Current day: $currentDay, Current time: $currentTime");
        
        // Get driver's current trip
        $query = "
            SELECT 
                rs.schedule_id,
                r.route_id,
                r.route_name,
                rs.departure_time,
                rs.arrival_time,
                v.registration_number,
                v.make,
                v.model
            FROM route_schedules rs
            JOIN routes r ON rs.route_id = r.route_id
            LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
            WHERE rs.driver_id = ?
            AND rs.day_of_week = ?
            AND ? BETWEEN rs.departure_time AND rs.arrival_time
            LIMIT 1
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("iss", $driver['driver_id'], $currentDay, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentTrip = $result->fetch_assoc();
        
        if ($currentTrip) {
            error_log("Found current trip: " . print_r($currentTrip, true));
            
            // Get passengers for current trip
            $query = "
                SELECT 
                    b.booking_id,
                    u.first_name,
                    u.last_name,
                    u.university_id,
                    u.phone,
                    b.passenger_count,
                    b.status,
                    b.pickup_point,
                    b.dropoff_point
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                WHERE b.schedule_id = ?
                AND b.travel_date = ?
                AND b.status = 'Confirmed'
                ORDER BY b.booking_id
            ";
            
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("is", $currentTrip['schedule_id'], $today);
            $stmt->execute();
            $result = $stmt->get_result();
            $passengers = $result->fetch_all(MYSQLI_ASSOC);
            
            error_log("Found " . count($passengers) . " passengers for this trip");
        } else {
            error_log("No current trip found for driver");
        }
    }
} catch (Exception $e) {
    error_log("Passenger list error: " . $e->getMessage());
    $error = "Failed to load passenger data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger List | Driver Portal</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .passenger-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .passenger-card {
            transition: all 0.2s ease;
        }
        .passenger-card:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/driver-menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/driver-navbar.php'; ?>
                
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0">Passenger List</h4>
                            <a href="driver-dashboard.php" class="btn btn-primary">
                                <i class="bx bx-chevron-left me-1"></i> Back to Dashboard
                            </a>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <!-- Current Trip Info -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Current Trip Information</h5>
                                <?php if($currentTrip): ?>
                                    <span class="badge bg-primary">Active Trip</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if($currentTrip): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <h6 class="mb-1">Route</h6>
                                                <p><?= htmlspecialchars($currentTrip['route_name']) ?></p>
                                            </div>
                                            <div class="mb-3">
                                                <h6 class="mb-1">Schedule</h6>
                                                <p>
                                                    <?= date('g:i A', strtotime($currentTrip['departure_time'])) ?> - 
                                                    <?= date('g:i A', strtotime($currentTrip['arrival_time'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <h6 class="mb-1">Vehicle</h6>
                                                <p>
                                                    <?= htmlspecialchars($currentTrip['registration_number']) ?> 
                                                    (<?= htmlspecialchars($currentTrip['make']) ?> <?= htmlspecialchars($currentTrip['model']) ?>)
                                                </p>
                                            </div>
                                            <div class="mb-3">
                                                <h6 class="mb-1">Date</h6>
                                                <p><?= date('l, F j, Y', strtotime($today)) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">No active trip at this time</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Passenger Stats -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="fw-semibold d-block">Total Passengers</span>
                                                <h3 class="card-title mb-2 mt-2"><?= count($passengers) ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="bx bx-user bx-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passenger List -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Passenger List</h5>
                                <div class="d-flex">
                                    <input type="text" id="searchPassengers" class="form-control form-control-sm" placeholder="Search passengers..." style="width: 200px;">
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($passengers)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="passengersTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;"></th>
                                                    <th>Passenger</th>
                                                    <th>University ID</th>
                                                    <th>Contact</th>
                                                    <th>Passengers</th>
                                                    <th>Pickup Point</th>
                                                    <th>Dropoff Point</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($passengers as $passenger): ?>
                                                    <tr class="passenger-card">
                                                        <td>
                                                            <img src="assets/img/avatars/default-user.png" alt="Passenger" class="passenger-photo">
                                                        </td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']) ?></strong>
                                                        </td>
                                                        <td><?= htmlspecialchars($passenger['university_id']) ?></td>
                                                        <td><?= htmlspecialchars($passenger['phone']) ?></td>
                                                        <td><?= $passenger['passenger_count'] ?></td>
                                                        <td><?= htmlspecialchars($passenger['pickup_point']) ?></td>
                                                        <td><?= htmlspecialchars($passenger['dropoff_point']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">No passengers found for current trip</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php include 'includes/footer.php'; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/scripts.php'; ?>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable for passenger search
        $('#passengersTable').DataTable({
            responsive: true,
            dom: 'rt<"bottom"ip>',
            pageLength: 10
        });
    });
    </script>
</body>
</html>