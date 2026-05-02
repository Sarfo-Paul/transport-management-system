<?php
require_once 'config.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: app-route-list.php');
    exit();
}

$routeId = (int)$_GET['id'];

// Get route details
$stmt = $conn->prepare("SELECT * FROM routes WHERE route_id = ?");
$stmt->bind_param("i", $routeId);
$stmt->execute();
$result = $stmt->get_result();
$route = $result->fetch_assoc();
$stmt->close();

if (!$route) {
    header('Location: app-route-list.php');
    exit();
}

// Get route coordinates
$stmt = $conn->prepare("SELECT * FROM route_coordinates WHERE route_id = ? ORDER BY sequence_order");
$stmt->bind_param("i", $routeId);
$stmt->execute();
$result = $stmt->get_result();
$coordinates = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Function to get status badge class
function getStatusBadge($status) {
    switch ($status) {
        case 'Active':
        case 'Completed':
        case 'Scheduled':
            return 'success';
        case 'Inactive':
        case 'Cancelled':
            return 'danger';
        case 'Under Review':
        case 'Delayed':
            return 'warning';
        case 'Maintenance':
        case 'In Progress':
            return 'info';
        default:
            return 'secondary';
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
        <h4 class="fw-bold">Route Map: <?= htmlspecialchars($route['route_name']) ?></h4>
        <div>
            <a href="app-route-edit.php?id=<?= $routeId ?>" class="btn btn-outline-warning">
                <i class="bx bx-edit me-1"></i> Edit
            </a>
            <a href="app-route-view.php?id=<?= $routeId ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <?php if (!empty($coordinates)): ?>
                <div id="routeMap" style="height: 500px; background-color: #f8f9fa;"></div>
            <?php else: ?>
                <div class="alert alert-info m-4">No coordinates available for this route.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Route Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Route Name</label>
                        <p class="form-control-static"><?= htmlspecialchars($route['route_name']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Point</label>
                        <p class="form-control-static"><?= htmlspecialchars($route['start_point']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Point</label>
                        <p class="form-control-static"><?= htmlspecialchars($route['end_point']) ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Distance</label>
                        <p class="form-control-static"><?= $route['distance'] ?> km</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <p class="form-control-static"><?= $route['estimated_duration'] ?> minutes</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <p>
                            <span class="badge bg-<?= getStatusBadge($route['status']) ?>">
                                <?= $route['status'] ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
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

    
    <!-- Leaflet JS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    
   <!-- Add this right after your Leaflet JS includes -->
<style>
    /* Ensure map container has dimensions */
    #allRoutesMap {
        height: 600px;
        width: 100%;
        background-color: #eee; /* So we can see if container exists */
    }
    
    /* Debugging styles */
    .debug-panel {
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 1000;
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
</style>

<!-- Add this debug panel inside your map container -->
<div class="debug-panel" id="debugPanel">
    <h6>Debug Information</h6>
    <div id="debugInfo"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug output
    const debugInfo = document.getElementById('debugInfo');
    debugInfo.innerHTML += '<p>DOM loaded, initializing map...</p>';
    
    try {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            throw new Error('Leaflet not loaded - check JS includes');
        }
        debugInfo.innerHTML += '<p>Leaflet loaded successfully</p>';
        
        // Create map
        var map = L.map('allRoutesMap').setView([0, 0], 2);
        debugInfo.innerHTML += '<p>Map created</p>';
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        debugInfo.innerHTML += '<p>Tile layer added</p>';
        
        // Check if we have route data
        debugInfo.innerHTML += `<p>Routes in PHP: ${<?php echo count($routes); ?>}</p>`;
        debugInfo.innerHTML += `<p>Coordinates in PHP: ${<?php echo count($allCoordinates); ?>}</p>`;
        
        // Define colors for different routes
        var colors = [
            '#7367F0', '#EA5455', '#FF9F43', '#28C76F', '#00CFE8', 
            '#A8AAAE', '#FF85AC', '#5A8DEE', '#FDAC41', '#39DA8A'
        ];
        
        // Create layer group for all routes
        var allRoutesLayer = L.layerGroup().addTo(map);
        
        // Array to store all valid coordinates for bounds calculation
        var allValidCoords = [];
        
        // Process each route from PHP
        <?php foreach ($routes as $routeId => $route): ?>
            <?php if (isset($allCoordinates[$routeId]) && count($allCoordinates[$routeId]) > 0): ?>
                debugInfo.innerHTML += '<p>Processing route <?php echo $routeId; ?></p>';
                
                // Get color for this route
                var color = colors[<?php echo $routeId % count($colors); ?>];
                
                // Add to legend
                var legendItem = document.createElement('div');
                legendItem.className = 'legend-item';
                legendItem.innerHTML = `
                    <div class="legend-color" style="background-color: ${color};"></div>
                    <span><?php echo htmlspecialchars($route['route_name']); ?></span>
                `;
                document.getElementById('routeLegend').appendChild(legendItem);
                
                // Collect valid coordinates for this route
                var routeCoords = [];
                <?php foreach ($allCoordinates[$routeId] as $index => $coord): ?>
                    <?php 
                    // Validate coordinates before adding
                    $lat = $coord['latitude'];
                    $lng = $coord['longitude'];
                    if (is_numeric($lat) && is_numeric($lng) && 
                        $lat >= -90 && $lat <= 90 &&
                        $lng >= -180 && $lng <= 180): ?>
                        debugInfo.innerHTML += '<p>Valid coord <?php echo $index; ?>: <?php echo $lat; ?>, <?php echo $lng; ?></p>';
                        routeCoords.push([<?php echo $lat; ?>, <?php echo $lng; ?>]);
                    <?php else: ?>
                        debugInfo.innerHTML += '<p style="color:red">Invalid coord <?php echo $index; ?>: <?php echo $lat; ?>, <?php echo $lng; ?></p>';
                    <?php endif; ?>
                <?php endforeach; ?>
                
                // Only proceed if we have at least 2 valid coordinates
                if (routeCoords.length >= 2) {
                    debugInfo.innerHTML += `<p>Adding polyline with ${routeCoords.length} points</p>`;
                    
                    // Add polyline
                    var polyline = L.polyline(routeCoords, {
                        color: color,
                        weight: 4,
                        opacity: 0.8
                    }).addTo(allRoutesLayer);
                    
                    // Add to bounds calculation
                    allValidCoords = allValidCoords.concat(routeCoords);
                    
                    // Add start and end markers
                    L.marker(routeCoords[0], {
                        icon: L.divIcon({
                            html: '<div class="stop-marker">S</div>',
                            iconSize: [24, 24],
                            className: 'stop-marker-icon'
                        })
                    }).bindPopup(`<b>Start: <?php echo htmlspecialchars($route['start_point']); ?></b>`).addTo(allRoutesLayer);
                    
                    L.marker(routeCoords[routeCoords.length - 1], {
                        icon: L.divIcon({
                            html: '<div class="stop-marker">E</div>',
                            iconSize: [24, 24],
                            className: 'stop-marker-icon'
                        })
                    }).bindPopup(`<b>End: <?php echo htmlspecialchars($route['end_point']); ?></b>`).addTo(allRoutesLayer);
                } else {
                    debugInfo.innerHTML += `<p style="color:orange">Not enough valid coordinates for route <?php echo $routeId; ?> (${routeCoords.length})</p>`;
                }
            <?php endif; ?>
        <?php endforeach; ?>
        
        // Fit map to show all routes if we have valid coordinates
        if (allValidCoords.length > 0) {
            debugInfo.innerHTML += `<p>Fitting map to ${allValidCoords.length} coordinates</p>`;
            
            // Create a LatLngBounds object
            var bounds = L.latLngBounds(allValidCoords);
            
            // Add some padding to the bounds
            map.fitBounds(bounds, {padding: [50, 50]});
            
            // Set maximum zoom level to ensure all routes remain visible
            map.setMaxBounds(bounds);
        } else {
            debugInfo.innerHTML += '<p style="color:red">No valid coordinates found - showing default view</p>';
            // Set default view to University of Ghana if no coordinates
            map.setView([5.6500, -0.1833], 13); // UG coordinates
        }
        
        // Add layer control
        var baseLayers = {
            "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
            "Satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}')
        };
        
        var overlayLayers = {
            "All Routes": allRoutesLayer
        };
        
        L.control.layers(baseLayers, overlayLayers).addTo(map);
        
        // Add button to reset view
        L.control({
            position: 'topright'
        }).onAdd = function(map) {
            var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = '<a href="#" title="Reset View" style="line-height:26px;"><i class="bx bx-globe"></i></a>';
            div.onclick = function() {
                if (allValidCoords.length > 0) {
                    map.fitBounds(L.latLngBounds(allValidCoords), {padding: [50, 50]});
                }
                return false;
            };
            return div;
        }.addTo(map);
        
        debugInfo.innerHTML += '<p style="color:green">Map initialization complete</p>';
        
    } catch (error) {
        debugInfo.innerHTML += `<p style="color:red">Error: ${error.message}</p>`;
        console.error(error);
    }
});
</script>
</body>

</html>