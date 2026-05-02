<?php
require_once __DIR__ . '/config.php';
// Enforce admin-only access
enforceRole(['administrator']);

// Get counts for dashboard cards
$stats = [];
try {
    $queries = [
        'bookings' => "SELECT COUNT(*) FROM bookings WHERE status='Confirmed'",
        'drivers' => "SELECT COUNT(*) FROM drivers WHERE status='Active'",
        'vehicles' => "SELECT COUNT(*) FROM vehicles WHERE status='Active'",
        'routes' => "SELECT COUNT(*) FROM routes WHERE status='Active'",
        'today_bookings' => "SELECT COUNT(*) FROM bookings WHERE travel_date=CURDATE()",
        'pending_bookings' => "SELECT COUNT(*) FROM bookings WHERE status='Pending'"
    ];
    
    foreach ($queries as $key => $sql) {
        $result = $conn->query($sql);
        $stats[$key] = $result->fetch_row()[0];
    }
    
    // Get recent bookings
    $recentBookings = $conn->query("
        SELECT b.*, r.route_name, u.first_name, u.last_name 
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN users u ON b.user_id = u.user_id
        ORDER BY b.booking_date DESC LIMIT 5
    ")->fetch_all(MYSQLI_ASSOC);
    
    // Get upcoming schedules
    $upcomingSchedules = $conn->query("
        SELECT rs.*, r.route_name, d.first_name, d.last_name
        FROM route_schedules rs
        JOIN routes r ON rs.route_id = r.route_id
        LEFT JOIN drivers d ON rs.driver_id = d.driver_id
        WHERE rs.day_of_week = DAYNAME(CURDATE())
        AND rs.status = 'Scheduled'
        ORDER BY rs.departure_time
    ")->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "Failed to load dashboard data";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Transport Management System</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/navbar.php'; ?>
                
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Welcome Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h4 class="card-title text-white mb-2">Welcome, Administrator!</h4>
                                                <p class="card-text mb-4">
                                                    <?php 
                                                    $hour = date('G');
                                                    if ($hour >= 5 && $hour < 12) {
                                                        echo "Good morning!";
                                                    } elseif ($hour >= 12 && $hour < 17) {
                                                        echo "Good afternoon!";
                                                    } else {
                                                        echo "Good evening!";
                                                    }
                                                    ?>
                                                    Here's what's happening with your transport system today.
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
                            hours = hours ? hours : 12; // the hour '0' should be '12'
                            
                            document.getElementById('live-clock').textContent = 
                                `${hours}:${minutes}:${seconds} ${ampm}`;
                        }

                        setInterval(updateClock, 1000);
                        updateClock(); // Initialize immediately
                        </script>
                        
                        <h4 class="fw-bold mb-4">Dashboard Overview</h4>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Today's Bookings</h6>
                                                <h2 class="mb-0"><?= $stats['today_bookings'] ?></h2>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="bx bx-calendar-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Active Drivers</h6>
                                                <h2 class="mb-0"><?= $stats['drivers'] ?></h2>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="bx bx-user"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Active Vehicles</h6>
                                                <h2 class="mb-0"><?= $stats['vehicles'] ?></h2>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="bx bx-car"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Active Routes</h6>
                                                <h2 class="mb-0"><?= $stats['routes'] ?></h2>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Pending Bookings</h6>
                                                <h2 class="mb-0"><?= $stats['pending_bookings'] ?></h2>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-danger rounded">
                                                    <i class="bx bx-time"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Total Bookings</h6>
                                                <h2 class="mb-0"><?= $stats['bookings'] ?></h2>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-secondary rounded">
                                                    <i class="bx bx-list-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Bookings & Upcoming Schedules -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Recent Bookings</h5>
                                        <a href="app-booking-list.php" class="btn btn-sm btn-outline-primary">View All</a>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>User</th>
                                                        <th>Route</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($recentBookings as $booking): ?>
                                                    <tr>
                                                        <td>#<?= $booking['booking_id'] ?></td>
                                                        <td><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></td>
                                                        <td><?= htmlspecialchars($booking['route_name']) ?></td>
                                                        <td><?= date('M d', strtotime($booking['travel_date'])) ?></td>
                                                        <td>
                                                            <span class="badge bg-<?= 
                                                                $booking['status'] == 'Confirmed' ? 'success' : 
                                                                ($booking['status'] == 'Pending' ? 'warning' : 'danger') 
                                                            ?>">
                                                                <?= $booking['status'] ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Today's Schedules</h5>
                                        <a href="app-driver-schedule.php" class="btn btn-sm btn-outline-primary">View All</a>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Route</th>
                                                        <th>Time</th>
                                                        <th>Driver</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($upcomingSchedules as $schedule): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($schedule['route_name']) ?></td>
                                                        <td>
                                                            <?= date('g:i A', strtotime($schedule['departure_time'])) ?> - 
                                                            <?= date('g:i A', strtotime($schedule['arrival_time'])) ?>
                                                        </td>
                                                        <td>
                                                            <?= $schedule['first_name'] ? 
                                                                htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) : 
                                                                'Not Assigned' ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary"><?= $schedule['status'] ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php if(empty($upcomingSchedules)): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4">No schedules for today</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Recent Reports Widget -->
<div class="col-md-6 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Reports</h5>
            <a href="app-reports-list.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Get recent reports (5 user and 5 driver reports)
                        $recent_reports = $conn->query("
                            (SELECT 'user' as type, report_id, title, status, report_date 
                             FROM user_reports 
                             ORDER BY report_date DESC LIMIT 5)
                            UNION ALL
                            (SELECT 'driver' as type, report_id, title, status, report_date 
                             FROM driver_reports 
                             ORDER BY report_date DESC LIMIT 5)
                            ORDER BY report_date DESC LIMIT 5
                        ")->fetch_all(MYSQLI_ASSOC);
                        
                        foreach($recent_reports as $report): 
                        ?>
                        <tr>
                            <td><?= strtoupper($report['type'][0]) ?>R-<?= $report['report_id'] ?></td>
                            <td>
                                <span class="badge bg-<?= $report['type'] === 'user' ? 'primary' : 'success' ?>">
                                    <?= ucfirst($report['type']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars(substr($report['title'], 0, 20)) . (strlen($report['title']) > 20 ? '...' : '') ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $report['status'] === 'Pending' ? 'secondary' : 
                                    ($report['status'] === 'In Progress' ? 'primary' : 
                                    ($report['status'] === 'Resolved' ? 'success' : 'danger')) 
                                ?>">
                                    <?= $report['status'] ?>
                                </span>
                            </td>
                            <td><?= date('M d', strtotime($report['report_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($recent_reports)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No recent reports</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
                        
                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-sm-6 col-md-3">
                                                <a href="app-route-schedule.php" class="btn btn-outline-primary w-100 h-100 py-3">
                                                    <i class="bx bx-time fs-4 mb-2"></i><br>
                                                    Check Schedule
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <a href="app-driver-add.php" class="btn btn-outline-success w-100 h-100 py-3">
                                                    <i class="bx bx-user-plus fs-4 mb-2"></i><br>
                                                    Add Driver
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <a href="app-fleet-add.php" class="btn btn-outline-info w-100 h-100 py-3">
                                                    <i class="bx bx-car fs-4 mb-2"></i><br>
                                                    Add Vehicle
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <a href="app-route-add.php" class="btn btn-outline-warning w-100 h-100 py-3">
                                                    <i class="bx bx-map fs-4 mb-2"></i><br>
                                                    Add Route
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        // Initialize any dashboard-specific scripts
    });
    </script>
</body>
</html>