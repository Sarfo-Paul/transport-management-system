<?php
require_once __DIR__ . '/config.php';

// Check authentication and driver role
if (!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

// Get driver information
$driver_id = $_SESSION['user_id'];
$driver_query = "SELECT d.driver_id, d.first_name, d.last_name, d.license_number 
                 FROM drivers d 
                 JOIN users u ON d.user_id = u.user_id 
                 WHERE u.user_id = ?";
$stmt = $conn->prepare($driver_query);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$driver_result = $stmt->get_result();

if ($driver_result->num_rows === 0) {
    die("Driver not found");
}

$driver = $driver_result->fetch_assoc();
$driver_name = $driver['first_name'] . ' ' . $driver['last_name'];

// Get upcoming schedules for this driver
$current_date = date('Y-m-d');
$schedules_query = "SELECT 
                    rs.schedule_id, 
                    r.route_name, 
                    r.start_point, 
                    r.end_point, 
                    rs.day_of_week, 
                    rs.departure_time, 
                    rs.arrival_time,
                    v.make,
                    v.model,
                    v.registration_number,
                    COUNT(b.booking_id) as passenger_count
                FROM route_schedules rs
                JOIN routes r ON rs.route_id = r.route_id
                LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
                LEFT JOIN bookings b ON rs.schedule_id = b.schedule_id AND b.status = 'Confirmed'
                WHERE rs.driver_id = ?
                GROUP BY rs.schedule_id
                ORDER BY 
                    FIELD(rs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                    rs.departure_time";

$stmt = $conn->prepare($schedules_query);
$stmt->bind_param("i", $driver['driver_id']);
$stmt->execute();
$schedules_result = $stmt->get_result();

include 'includes/header.php';
?>

    <style>
        .schedule-card {
            transition: all 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
        }
        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-title {
            color: #566a7f;
        }
        .driver-info-card {
            margin-bottom: 20px;
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
                            <h4 class="fw-bold mb-0">Driver Schedule</h4>
                            <a href="driver-dashboard.php" class="btn btn-primary">
                                <i class="bx bx-chevron-left me-1"></i> Back to Dashboard
                            </a>
                        </div>

                        <!-- Driver Information -->
                        <div class="card mb-4 driver-info-card">
                            <div class="card-header">
                                <h5 class="mb-0">Driver Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="mb-1">Name</h6>
                                            <p><?php echo htmlspecialchars($driver_name); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="mb-1">License Number</h6>
                                            <p><?php echo htmlspecialchars($driver['license_number']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Schedules -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Upcoming Schedules</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($schedules_result->num_rows > 0): ?>
                                    <div class="row">
                                        <?php while ($schedule = $schedules_result->fetch_assoc()): ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card schedule-card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($schedule['route_name']); ?></h5>
                                                        <h6 class="card-subtitle mb-3 text-muted">
                                                            <?php echo htmlspecialchars($schedule['start_point']); ?> to <?php echo htmlspecialchars($schedule['end_point']); ?>
                                                        </h6>
                                                        <div class="mb-2">
                                                            <span class="badge bg-label-primary me-2">
                                                                <i class="bx bx-calendar me-1"></i> <?php echo htmlspecialchars($schedule['day_of_week']); ?>
                                                            </span>
                                                            <span class="badge bg-label-info">
                                                                <i class="bx bx-time me-1"></i> 
                                                                <?php echo date('g:i A', strtotime($schedule['departure_time'])); ?> - 
                                                                <?php echo date('g:i A', strtotime($schedule['arrival_time'])); ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <i class="bx bx-car me-2"></i>
                                                            <?php if ($schedule['make']): ?>
                                                                <?php echo htmlspecialchars($schedule['make'] . ' ' . $schedule['model'] . ' (' . $schedule['registration_number'] . ')'); ?>
                                                            <?php else: ?>
                                                                <span class="text-warning">Not assigned</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <div>
                                                            <i class="bx bx-group me-2"></i>
                                                            <?php echo htmlspecialchars($schedule['passenger_count']); ?> passengers
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        You have no upcoming schedules assigned to you.
                                    </div>
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
</body>
</html>

<?php
$conn->close();
?>