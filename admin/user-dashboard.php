<?php
require_once __DIR__ . '/config.php';
// Enforce student/staff access only
enforceRole(['student','staff']);

// Initialize variables
$error = null;
$bookings = [];
$upcomingBookings = [];
$routes = [];
$schedules = [];
$frequentRoute = "No frequent route"; // Default value
$userId = (int)$_SESSION['user_id'];

// Get user details for personalization
$userDetails = getUserData($userId);
if (!$userDetails) {
    $error = "User not found. Please log in again.";
    session_destroy();
    header('Location: login.php');
    exit();
}

try {
    // Get all bookings with improved query
    $stmt = $conn->prepare("
        SELECT b.*, r.route_name, r.start_point, r.end_point,
               rs.departure_time, rs.arrival_time, rs.day_of_week,
               v.registration_number, v.vehicle_type,
               CONCAT(d.first_name, ' ', d.last_name) AS driver_name
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN route_schedules rs ON b.schedule_id = rs.schedule_id
        LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
        LEFT JOIN drivers d ON rs.driver_id = d.driver_id
        WHERE b.user_id = ?
        ORDER BY b.travel_date DESC, rs.departure_time DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get upcoming bookings (next 7 days) with more details
    $stmt = $conn->prepare("
        SELECT b.*, r.route_name, r.start_point, r.end_point,
               rs.departure_time, rs.arrival_time, rs.day_of_week,
               v.registration_number, v.vehicle_type,
               CONCAT(d.first_name, ' ', d.last_name) AS driver_name
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN route_schedules rs ON b.schedule_id = rs.schedule_id
        LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
        LEFT JOIN drivers d ON rs.driver_id = d.driver_id
        WHERE b.user_id = ? 
        AND b.travel_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        AND b.status = 'Confirmed'
        ORDER BY b.travel_date, rs.departure_time
        LIMIT 3
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $upcomingBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get all active routes for the map
    $routes = $conn->query("
        SELECT r.route_id, r.route_name, r.start_point, r.end_point
        FROM routes r
        WHERE r.status = 'Active'
        ORDER BY r.route_name
    ")->fetch_all(MYSQLI_ASSOC);

    // Get user's most frequent route
    $stmt = $conn->prepare("
        SELECT r.route_name, r.start_point, r.end_point, COUNT(*) as booking_count
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        WHERE b.user_id = ?
        GROUP BY b.route_id
        ORDER BY booking_count DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $frequentRouteResult = $stmt->get_result()->fetch_assoc();
    
    if ($frequentRouteResult) {
        $frequentRoute = htmlspecialchars($frequentRouteResult['route_name']);
    }

} catch (Exception $e) {
    error_log("User dashboard error: " . $e->getMessage());
    $error = "Failed to load dashboard data. Please try again later.";
    // Consider sending an email to admin for critical errors
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Transport System</title>
    <?php include 'includes/header.php'; ?>
    <!-- Add Leaflet CSS for map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        .booking-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .booking-card.confirmed { border-left-color: #28a745; }
        .booking-card.pending { border-left-color: #ffc107; }
        .booking-card.cancelled { border-left-color: #dc3545; }
        .route-badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.5rem;
        }
        .timetable-day {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }
        #routeMap {
            height: 400px;
            width: 100%;
            border-radius: 8px;
        }
        .map-container {
            position: relative;
        }
        .fullscreen-map {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: white;
        }
        .close-fullscreen {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10000;
            background: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/user-menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/user-navbar.php'; ?>
                
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Welcome Header for User Dashboard -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h4 class="card-title text-white mb-2">
              Welcome, <?= htmlspecialchars($_SESSION['first_name'] ?? 'User') ?>!
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
              Manage your transport bookings, check routes, and view upcoming schedules below.
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
            <!-- (Optional: User avatar, campus transport illustration, or icon can go here) -->
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

<?php if(isset($error)): ?>
    <div class="alert alert-danger mt-3"><?= $error ?></div>
<?php endif; ?>

                        
                        
                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Upcoming Trips</h6>
                                                <h3 class="mb-0"><?= count($upcomingBookings) ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Total Bookings</h6>
                                                <h3 class="mb-0"><?= count($bookings) ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="bx bx-list-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Active Routes</h6>
                                                <h3 class="mb-0"><?= count($routes ?? []) ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-2">Frequent Route</h6>
                                                <h3 class="mb-0"><?= $frequentRoute ?></h3>
                                            </div>
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="bx bx-trending-up"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upcoming Trips -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Your Upcoming Trips</h5>
                                        <a href="user-bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                                    </div>
                                    <div class="card-body">
                                        <?php if(empty($upcomingBookings)): ?>
                                            <div class="alert alert-info">You have no upcoming trips. <a href="user-bookings-new.php">Book now</a></div>
                                        <?php else: ?>
                                            <div class="row">
                                                <?php foreach($upcomingBookings as $booking): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card booking-card <?= strtolower($booking['status'] ?? '') ?>">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="badge bg-<?= 
                                                                    ($booking['status'] ?? '') == 'Confirmed' ? 'success' : 
                                                                    (($booking['status'] ?? '') == 'Pending' ? 'warning' : 'danger') 
                                                                ?>">
                                                                    <?= $booking['status'] ?? 'Unknown' ?>
                                                                </span>
                                                                <small class="text-muted">#BK-<?= str_pad($booking['booking_id'] ?? 0, 4, '0', STR_PAD_LEFT) ?></small>
                                                            </div>
                                                            <h6 class="mb-2"><?= htmlspecialchars($booking['route_name'] ?? 'Unknown Route') ?></h6>
                                                            <div class="mb-1">
                                                                <small class="text-muted">Date:</small>
                                                                <strong><?= date('D, M j', strtotime($booking['travel_date'] ?? 'now')) ?></strong>
                                                            </div>
                                                            <div class="mb-1">
                                                                <small class="text-muted">Time:</small>
                                                                <strong><?= date('g:i A', strtotime($booking['departure_time'] ?? 'now')) ?></strong>
                                                            </div>
                                                            <div class="mt-3">
                                                                <a href="booking-details.php?id=<?= $booking['booking_id'] ?? 0 ?>" class="btn btn-sm btn-outline-primary w-100">
                                                                    View Details
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Map Section -->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Transport Route Map</h5>
                                        <button id="fullscreenBtn" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-fullscreen me-1"></i> View Full Map
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="map-container">
                                            <div id="routeMap"></div>
                                        </div>
                                        <div class="mt-3">
                                            <p class="text-muted">Click on any route to see details. Zoom in/out to explore.</p>
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
    
    <!-- Add Leaflet JS for map -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize any user dashboard scripts
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize the map
        var map = L.map('routeMap').setView([5.6500, -0.1833], 14); // Default to UG coordinates
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add markers and lines for each route
        <?php foreach($routes as $route): ?>
            // This is a simplified example - you'll need actual coordinates for your routes
            var startCoords = [5.6500 + (Math.random() * 0.01), -0.1833 + (Math.random() * 0.01)];
            var endCoords = [5.6500 + (Math.random() * 0.01), -0.1833 + (Math.random() * 0.01)];
            
            // Add start marker
            L.marker(startCoords)
                .addTo(map)
                .bindPopup("Start: <?= htmlspecialchars($route['start_point']) ?>");
            
            // Add end marker
            L.marker(endCoords)
                .addTo(map)
                .bindPopup("End: <?= htmlspecialchars($route['end_point']) ?>");
            
            // Add line between points
            L.polyline([startCoords, endCoords], {color: 'blue'})
                .addTo(map)
                .bindPopup("Route: <?= htmlspecialchars($route['route_name']) ?>");
        <?php endforeach; ?>
        
        // Fullscreen map functionality
        $('#fullscreenBtn').click(function() {
            // Create a fullscreen map container
            var fullscreenMap = $('<div class="fullscreen-map" id="fullscreenMap"></div>');
            var closeBtn = $('<div class="close-fullscreen">Close Map &times;</div>');
            
            // Add to body
            $('body').append(fullscreenMap);
            fullscreenMap.append(closeBtn);
            
            // Initialize fullscreen map
            var fsMap = L.map('fullscreenMap').setView(map.getCenter(), map.getZoom());
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(fsMap);
            
            // Copy all markers and lines to fullscreen map
            <?php foreach($routes as $route): ?>
                var fsStartCoords = [5.6500 + (Math.random() * 0.01), -0.1833 + (Math.random() * 0.01)];
                var fsEndCoords = [5.6500 + (Math.random() * 0.01), -0.1833 + (Math.random() * 0.01)];
                
                L.marker(fsStartCoords)
                    .addTo(fsMap)
                    .bindPopup("Start: <?= htmlspecialchars($route['start_point']) ?>");
                
                L.marker(fsEndCoords)
                    .addTo(fsMap)
                    .bindPopup("End: <?= htmlspecialchars($route['end_point']) ?>");
                
                L.polyline([fsStartCoords, fsEndCoords], {color: 'blue'})
                    .addTo(fsMap)
                    .bindPopup("Route: <?= htmlspecialchars($route['route_name']) ?>");
            <?php endforeach; ?>
            
            // Close button functionality
            closeBtn.click(function() {
                fullscreenMap.remove();
                fsMap.remove();
            });
        });
    });
    </script>
</body>
</html>