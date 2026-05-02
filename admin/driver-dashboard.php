<?php
require_once __DIR__ . '/config.php';
// Enforce driver-only access
enforceRole(['driver']);

// Initialize variables
$error = null;
$driverName = '';

// Get the driver ID from session
$driverId = $_SESSION['user_id'];

try {
    // Get driver details using prepared statement
    $stmt = $conn->prepare("SELECT d.* FROM drivers d JOIN users u ON d.user_id = u.user_id WHERE u.user_id = ?");
    $stmt->bind_param("i", $driverId);
    $stmt->execute();
    $driver = $stmt->get_result()->fetch_assoc();
    
    if (!$driver) {
        throw new Exception("Driver not found");
    }

    // Set driver name
    $driverName = $driver['name'] ?? $driver['first_name'] ?? ''; // Adjust based on your DB structure

    // Rest of your existing code...
    // Get today's date and time
    $today = date('Y-m-d');
    $now = date('H:i:s');
    $dayOfWeek = date('l');

    // Get assigned vehicle
    $stmt = $conn->prepare("
        SELECT v.* 
        FROM vehicles v
        JOIN drivers d ON v.vehicle_id = d.assigned_vehicle_id
        WHERE d.driver_id = ?
    ");
    $stmt->bind_param("i", $driver['driver_id']);
    $stmt->execute();
    $vehicle = $stmt->get_result()->fetch_assoc();
    
    // Get today's trips with passenger count
    $stmt = $conn->prepare("
        SELECT rs.*, r.route_name, r.start_point, r.end_point,
               COUNT(b.booking_id) as passenger_count,
               v.capacity as vehicle_capacity
        FROM route_schedules rs
        JOIN routes r ON rs.route_id = r.route_id
        LEFT JOIN bookings b ON rs.schedule_id = b.schedule_id 
            AND b.travel_date = ? 
            AND b.status = 'Confirmed'
        LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
        WHERE rs.driver_id = ?
            AND rs.day_of_week = ?
        GROUP BY rs.schedule_id
        ORDER BY rs.departure_time
    ");
    $stmt->bind_param("sis", $today, $driver['driver_id'], $dayOfWeek);
    $stmt->execute();
    $todayTrips = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get current trip (if any)
    $currentTrip = null;
    foreach ($todayTrips as $trip) {
        if ($trip['departure_time'] <= $now && $trip['arrival_time'] >= $now) {
            $currentTrip = $trip;
            break;
        }
    }
    
    // Get trip statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_trips,
            SUM(CASE WHEN rs.arrival_time < ? THEN 1 ELSE 0 END) as completed_trips,
            SUM(CASE WHEN rs.departure_time > ? THEN 1 ELSE 0 END) as upcoming_trips
        FROM route_schedules rs
        WHERE rs.driver_id = ?
            AND rs.day_of_week = ?
    ");
    $stmt->bind_param("ssis", $now, $now, $driver['driver_id'], $dayOfWeek);
    $stmt->execute();
    $tripStats = $stmt->get_result()->fetch_assoc();
    
    // Get upcoming schedule (next 3 days)
    $stmt = $conn->prepare("
        SELECT rs.*, r.route_name, r.start_point, r.end_point
        FROM route_schedules rs
        JOIN routes r ON rs.route_id = r.route_id
        WHERE rs.driver_id = ?
            AND (
                rs.day_of_week > ? OR
                (rs.day_of_week = ? AND rs.departure_time > ?)
            )
        ORDER BY 
            FIELD(rs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
            rs.departure_time
        LIMIT 5
    ");
    $stmt->bind_param("isss", $driver['driver_id'], $dayOfWeek, $dayOfWeek, $now);
    $stmt->execute();
    $upcomingSchedule = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get vehicle maintenance status if vehicle exists
    $maintenanceStatus = null;
    if ($vehicle) {
        $stmt = $conn->prepare("
            SELECT * FROM vehicle_maintenance 
            WHERE vehicle_id = ?
            ORDER BY maintenance_date DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $vehicle['vehicle_id']);
        $stmt->execute();
        $maintenanceStatus = $stmt->get_result()->fetch_assoc();
    }

} catch (Exception $e) {
    error_log("Driver dashboard error: " . $e->getMessage());
    $error = "Failed to load dashboard data. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard | Transport System</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .progress-thin { height: 6px; }
        .avatar-initial { display: flex; align-items: center; justify-content: center; }
        .list-group-item { transition: all 0.2s ease; }
        .list-group-item:hover { background-color: #f8f9fa; }
        .trip-card { border-left: 4px solid; }
        .trip-card.completed { border-left-color: #28a745; }
        .trip-card.in-progress { border-left-color: #17a2b8; }
        .trip-card.upcoming { border-left-color: #ffc107; }
    </style>
</head>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <?php include 'includes/driver-menu.php'; ?>

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include 'includes/driver-navbar.php'; ?>

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
 <!-- Welcome Header for Driver Dashboard -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h4 class="card-title text-white mb-2">
              Welcome, Driver <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?>!
            </h4>
            <p class="card-text mb-4">
              <?php 
              $hour = date('G');
              if ($hour >= 5 && $hour < 12) {
                  echo "Good morning";
              } elseif ($hour >= 12 && $hour < 17) {
                  echo "Good afternoon";
              } else {
                  echo "Good evening";
              }
              ?>, your dashboard is ready.  
              Check today’s trips, schedules, and vehicle updates below.
            </p>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge bg-white text-primary me-2">
                <i class="bx bx-calendar me-1"></i> <?= date('l, F j, Y') ?>
              </span>
              <span class="badge bg-white text-primary">
                <i class="bx bx-time me-1"></i> 
                <span id="live-clock"><?= date('h:i:s A') ?></span>
              </span>
            </div>
          </div>
          <div class="col-md-4 text-center d-none d-md-block">
            <!-- (Optional: Driver avatar, vehicle icon, or illustration can be placed here later) -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Live clock update
function updateClock() {
  const now = new Date();
  let hours = now.getHours();
  const minutes = now.getMinutes().toString().padStart(2, '0');
  const seconds = now.getSeconds().toString().padStart(2, '0');
  const ampm = hours >= 12 ? 'PM' : 'AM';
  
  hours = hours % 12;
  hours = hours ? hours : 12; // convert 0 to 12
  
  document.getElementById('live-clock').textContent = 
    `${hours}:${minutes}:${seconds} ${ampm}`;
}

setInterval(updateClock, 1000);
updateClock(); // Initialize immediately
</script>


                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="fw-semibold d-block">Today's Trips</span>
                                                <h3 class="card-title mb-2 mt-2"><?= $tripStats['total_trips'] ?? 0 ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="bx bx-trip bx-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="fw-semibold d-block">Completed</span>
                                                <h3 class="card-title mb-2 mt-2"><?= $tripStats['completed_trips'] ?? 0 ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="bx bx-check-circle bx-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="fw-semibold d-block">Upcoming</span>
                                                <h3 class="card-title mb-2 mt-2"><?= $tripStats['upcoming_trips'] ?? 0 ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="bx bx-time bx-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="fw-semibold d-block">Vehicle Status</span>
                                                <h3 class="card-title mb-2 mt-2"><?= $vehicle['status'] ?? 'N/A' ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="bx bx-car bx-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Dashboard Content -->
                        <div class="row">
                            <!-- Assigned Trips Section -->
                            <div class="col-md-8 mb-4">
                                <div class="card">
                                    <h5 class="card-header d-flex justify-content-between align-items-center">
                                        <span>Today's Assigned Trips</span>

                                    </h5>
                                    <div class="card-body">
                                        <?php if (empty($todayTrips)): ?>
                                            <div class="alert alert-info">No trips scheduled for today</div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Trip ID</th>
                                                            <th>Route</th>
                                                            <th>Schedule</th>
                                                            <th>Passengers</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                        <?php foreach ($todayTrips as $trip): 
                                                            $status = '';
                                                            $badgeClass = '';
                                                            
                                                            if ($trip['arrival_time'] < $now) {
                                                                $status = 'Completed';
                                                                $badgeClass = 'success';
                                                            } elseif ($trip['departure_time'] <= $now && $trip['arrival_time'] >= $now) {
                                                                $status = 'In Progress';
                                                                $badgeClass = 'info';
                                                            } else {
                                                                $status = 'Upcoming';
                                                                $badgeClass = 'secondary';
                                                            }
                                                        ?>
                                                        <tr class="<?= $status === 'In Progress' ? 'table-active' : '' ?>">
                                                            <td>#TR-<?= str_pad($trip['schedule_id'], 3, '0', STR_PAD_LEFT) ?></td>
                                                            <td><?= htmlspecialchars($trip['route_name']) ?></td>
                                                            <td><?= date('g:i A', strtotime($trip['departure_time'])) ?> - <?= date('g:i A', strtotime($trip['arrival_time'])) ?></td>
                                                            <td><?= $trip['passenger_count'] ?>/<?= $trip['vehicle_capacity'] ?></td>
                                                            <td><span class="badge bg-<?= $badgeClass ?>"><?= $status ?></span></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-primary">Details</button>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions Section -->
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <h5 class="card-header">Trip Status Update</h5>
                                    <div class="card-body">
                                        <?php if ($currentTrip): ?>
                                            <div class="mb-4">
                                                <h6 class="mb-3">Current Trip: #TR-<?= str_pad($currentTrip['schedule_id'], 3, '0', STR_PAD_LEFT) ?></h6>
                                                <div class="progress mb-3 progress-thin">
                                                    <?php
                                                    $departure = strtotime($currentTrip['departure_time']);
                                                    $arrival = strtotime($currentTrip['arrival_time']);
                                                    $nowTime = time();
                                                    $progress = ($nowTime - $departure) / ($arrival - $departure) * 100;
                                                    $progress = min(max($progress, 0), 100);
                                                    ?>
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                         role="progressbar" 
                                                         style="width: <?= $progress ?>%;" 
                                                         aria-valuenow="<?= $progress ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100"></div>
                                                </div>
                                                <p class="mb-1"><strong>From:</strong> <?= htmlspecialchars($currentTrip['start_point']) ?></p>
                                                <p class="mb-1"><strong>To:</strong> <?= htmlspecialchars($currentTrip['end_point']) ?></p>
                                                <p class="mb-3"><strong>Passengers:</strong> <?= $currentTrip['passenger_count'] ?>/<?= $currentTrip['vehicle_capacity'] ?></p>
                                                
                                                <div class="btn-group w-100 mb-3">
                                                    <button class="btn btn-outline-success">Start Trip</button>
                                                    <button class="btn btn-outline-primary">On Route</button>
                                                    <button class="btn btn-outline-warning">Delayed</button>
                                                    <button class="btn btn-outline-danger">Cancel</button>
                                                </div>
                                                
                                                <button class="btn btn-success w-100">Complete Trip</button>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">No active trip at this time</div>
                                        <?php endif; ?>
                                        
                                        <div class="border-top pt-3"> 
    <h6 class="mb-3">Quick Actions</h6>
    <div class="d-grid gap-2">
        <a href="driver-report-issue.php" class="btn btn-outline-secondary">Report Issue</a>
    </div>
</div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Info Section -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <h5 class="card-header">Assigned Vehicle</h5>
                                    <div class="card-body">
                                        <?php if ($vehicle): ?>
                                            <div class="d-flex align-items-start">
                                                <div class="avatar me-3">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        <i class="bx bx-car bx-lg"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1"><?= htmlspecialchars($vehicle['make']) ?> <?= htmlspecialchars($vehicle['model']) ?> (<?= htmlspecialchars($vehicle['registration_number']) ?>)</h5>
                                                    <p class="mb-2"><?= ucfirst($vehicle['vehicle_type']) ?> • <?= $vehicle['capacity'] ?> Passenger Capacity</p>
                                                    
                                                    <div class="row mt-3">
                                                        <div class="col-6 mb-3">
                                                            <small class="text-muted d-block">Current Mileage</small>
                                                            <span class="fw-semibold"><?= number_format($vehicle['current_mileage'], 0) ?> km</span>
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <small class="text-muted d-block">Fuel Type</small>
                                                            <span class="fw-semibold"><?= $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : 'N/A' ?></span>
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <small class="text-muted d-block">Last Service</small>
                                                            <span class="fw-semibold">
                                                                <?= $maintenanceStatus ? date('M j, Y', strtotime($maintenanceStatus['maintenance_date'])) : 'N/A' ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <small class="text-muted d-block">Maintenance</small>
                                                            <span class="fw-semibold">
                                                                <?= $maintenanceStatus ? ucfirst($maintenanceStatus['status']) : 'No records' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
<a href="driver-vehicle.php" class="btn btn-outline-primary mt-2">View Full Details</a>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">No vehicle assigned</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                                  <!-- / Content -->

                    <?php include 'includes/footer.php'; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/scripts.php'; ?>
</body>
</html>