<?php
require_once 'config.php';

// Validate route ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: app-route-list.php');
    exit();
}

$routeId = (int)$_GET['id'];

// Get route details
$routeQuery = $conn->prepare("SELECT * FROM routes WHERE route_id = ?");
$routeQuery->bind_param("i", $routeId);
$routeQuery->execute();
$routeResult = $routeQuery->get_result();
$route = $routeResult->fetch_assoc();

if (!$route) {
    header('Location: app-route-list.php');
    exit();
}

// Get route coordinates for mapping
$coordQuery = $conn->prepare("SELECT * FROM route_coordinates WHERE route_id = ? ORDER BY sequence_order");
$coordQuery->bind_param("i", $routeId);
$coordQuery->execute();
$coordResult = $coordQuery->get_result();
$coordinates = $coordResult->fetch_all(MYSQLI_ASSOC);

// Get route schedules with vehicle and driver info
$scheduleQuery = $conn->prepare("SELECT rs.*, 
                               v.registration_number, v.vehicle_type, v.make, v.model,
                               CONCAT(d.first_name, ' ', d.last_name) as driver_name,
                               d.contact_number as driver_phone
                               FROM route_schedules rs
                               LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
                               LEFT JOIN drivers d ON rs.driver_id = d.driver_id
                               WHERE rs.route_id = ?
                               ORDER BY 
                                   FIELD(rs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                                   rs.departure_time");
$scheduleQuery->bind_param("i", $routeId);
$scheduleQuery->execute();
$scheduleResult = $scheduleQuery->get_result();
$schedules = $scheduleResult->fetch_all(MYSQLI_ASSOC);

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
                    <div class="content-wrapper">
                

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Route Details: <?= htmlspecialchars($route['route_name']) ?></h2>
        <div>
            <a href="app-route-edit.php?id=<?= $routeId ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="app-route-list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Basic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Route Name</th>
                            <td><?= htmlspecialchars($route['route_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Start Point</th>
                            <td><?= htmlspecialchars($route['start_point']) ?></td>
                        </tr>
                        <tr>
                            <th>End Point</th>
                            <td><?= htmlspecialchars($route['end_point']) ?></td>
                        </tr>
                        <tr>
                            <th>Distance</th>
                            <td><?= $route['distance'] ?> km</td>
                        </tr>
                        <tr>
                            <th>Estimated Duration</th>
                            <td><?= $route['estimated_duration'] ?> minutes</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-<?= getStatusBadge($route['status']) ?>">
                                    <?= $route['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td><?= nl2br(htmlspecialchars($route['description'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Route Map</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($coordinates)): ?>
                        <div id="routeMap" style="height: 300px;"></div>
                    <?php else: ?>
                        <div class="alert alert-info">No coordinates available for this route.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Route Coordinates</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($coordinates)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Landmark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coordinates as $coord): ?>
                                <tr>
                                    <td><?= $coord['sequence_order'] ?></td>
                                    <td><?= $coord['latitude'] ?></td>
                                    <td><?= $coord['longitude'] ?></td>
                                    <td><?= htmlspecialchars($coord['landmark']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No coordinates have been added for this route.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Schedules</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($schedules)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Departure Time</th>
                                <th>Arrival Time</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?= $schedule['day_of_week'] ?></td>
                                    <td><?= date('h:i A', strtotime($schedule['departure_time'])) ?></td>
                                    <td><?= date('h:i A', strtotime($schedule['arrival_time'])) ?></td>
                                    <td><?= $schedule['registration_number'] ?? 'Not assigned' ?></td>
                                    <td><?= $schedule['driver_name'] ?? 'Not assigned' ?></td>
                                    <td>
                                        <span class="badge badge-<?= getStatusBadge($schedule['status']) ?>">
                                            <?= $schedule['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No schedules have been created for this route.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($coordinates)): ?>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialize the map
    var map = L.map('routeMap').setView([<?= $coordinates[0]['latitude'] ?>, <?= $coordinates[0]['longitude'] ?>], 14);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Create an array of LatLng objects from the coordinates
    var routeCoordinates = [
        <?php foreach ($coordinates as $coord): ?>
            [<?= $coord['latitude'] ?>, <?= $coord['longitude'] ?>],
        <?php endforeach; ?>
    ];

    // Add a polyline to the map
    var routeLine = L.polyline(routeCoordinates, {color: 'blue'}).addTo(map);

    // Add markers for each coordinate point
    <?php foreach ($coordinates as $coord): ?>
        L.marker([<?= $coord['latitude'] ?>, <?= $coord['longitude'] ?>])
            .addTo(map)
            .bindPopup("<?= addslashes($coord['landmark'] ? htmlspecialchars($coord['landmark']) : 'Point ' . $coord['sequence_order']) ?>");
    <?php endforeach; ?>

    // Fit the map to the route bounds
    map.fitBounds(routeLine.getBounds());
</script>
<?php endif; ?>


                    <!-- / Content -->
                    
                    <?php include 'includes/footer.php'; ?>                    

                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
    <?php include 'includes/scripts.php'; ?>                    

    
    <script>
    // Add delete functionality
    document.querySelectorAll('.delete-route').forEach(btn => {
        btn.addEventListener('click', function() {
            const routeId = this.getAttribute('data-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete-route.php?id=' + routeId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'The route has been deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message || 'There was an error deleting the route.',
                                    'error'
                                );
                            }
                        });
                }
            });
        });
    });
    </script>
</body>
</html>