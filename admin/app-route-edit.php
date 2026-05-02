<?php
require_once 'config.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: app-route-list.php');
    exit();
}

$routeId = (int)$_GET['id'];
$errors = [];

// Get existing route data
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

// Get existing coordinates
$stmt = $conn->prepare("SELECT * FROM route_coordinates WHERE route_id = ? ORDER BY sequence_order");
$stmt->bind_param("i", $routeId);
$stmt->execute();
$result = $stmt->get_result();
$existingCoordinates = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $route['route_name'] = trim($_POST['route_name']);
    $route['start_point'] = trim($_POST['start_point']);
    $route['end_point'] = trim($_POST['end_point']);
    $route['distance'] = filter_input(INPUT_POST, 'distance', FILTER_VALIDATE_FLOAT);
    $route['estimated_duration'] = filter_input(INPUT_POST, 'estimated_duration', FILTER_VALIDATE_INT);
    $route['description'] = trim($_POST['description']);
    $route['status'] = $_POST['status'];

    // Validate inputs
    if (empty($route['route_name'])) {
        $errors['route_name'] = 'Route name is required';
    }

    if (empty($route['start_point'])) {
        $errors['start_point'] = 'Start point is required';
    }

    if (empty($route['end_point'])) {
        $errors['end_point'] = 'End point is required';
    }

    if ($route['distance'] === false || $route['distance'] <= 0) {
        $errors['distance'] = 'Please enter a valid distance (greater than 0)';
    }

    if ($route['estimated_duration'] === false || $route['estimated_duration'] <= 0) {
        $errors['estimated_duration'] = 'Please enter a valid duration (greater than 0 minutes)';
    }

    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Update route
$route_name = $route['route_name'];
$start_point = $route['start_point'];
$end_point = $route['end_point'];
$distance = $route['distance'];
$estimated_duration = $route['estimated_duration'];
$description = $route['description'];
$status = $route['status'];

$stmt = $conn->prepare("UPDATE routes SET 
                      route_name = ?, 
                      start_point = ?, 
                      end_point = ?, 
                      distance = ?, 
                      estimated_duration = ?, 
                      description = ?, 
                      status = ? 
                      WHERE route_id = ?");
$stmt->bind_param("sssdissi", 
    $route_name,
    $start_point,
    $end_point,
    $distance,
    $estimated_duration,
    $description,
    $status,
    $routeId
);
$stmt->execute();
$stmt->close();

// Process coordinates if provided
if (!empty($_POST['coordinates'])) {
    // First delete existing coordinates
    $stmt = $conn->prepare("DELETE FROM route_coordinates WHERE route_id = ?");
    $stmt->bind_param("i", $routeId);
    $stmt->execute();
    $stmt->close();

    // Insert new coordinates
    $coordinates = json_decode($_POST['coordinates'], true);
    if (is_array($coordinates)) {
        $stmt = $conn->prepare("INSERT INTO route_coordinates (route_id, latitude, longitude, sequence_order, landmark) 
                              VALUES (?, ?, ?, ?, ?)");

        foreach ($coordinates as $index => $coord) {
            $landmark = $coord['landmark'] ?? null;
            $lat = $coord['lat'];
            $lng = $coord['lng'];
            $sequence = $index + 1;
            
            $stmt->bind_param("iddis",
                $routeId,
                $lat,
                $lng,
                $sequence,
                $landmark
            );
            $stmt->execute();
        }
        $stmt->close();
    }
}

            $conn->commit();
            $_SESSION['success_message'] = 'Route updated successfully!';
            header("Location: app-route-view.php?id=$routeId");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Prepare coordinates for JavaScript
$coordinatesForJS = [];
foreach ($existingCoordinates as $coord) {
    $coordinatesForJS[] = [
        'lat' => (float)$coord['latitude'],
        'lng' => (float)$coord['longitude'],
        'landmark' => $coord['landmark']
    ];
}

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
        <h4 class="fw-bold">Edit Route: <?= htmlspecialchars($route['route_name']) ?></h4>
        <a href="app-route-view.php?id=<?= $routeId ?>" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to View
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Route Information</h5>
        </div>
        <div class="card-body">
            <form method="post" id="routeForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="route_name" class="form-label">Route Name</label>
                            <input type="text" class="form-control <?= isset($errors['route_name']) ? 'is-invalid' : '' ?>" 
                                   id="route_name" name="route_name" value="<?= htmlspecialchars($route['route_name']) ?>" required>
                            <?php if (isset($errors['route_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['route_name']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="start_point" class="form-label">Start Point</label>
                            <input type="text" class="form-control <?= isset($errors['start_point']) ? 'is-invalid' : '' ?>" 
                                   id="start_point" name="start_point" value="<?= htmlspecialchars($route['start_point']) ?>" required>
                            <?php if (isset($errors['start_point'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['start_point']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="end_point" class="form-label">End Point</label>
                            <input type="text" class="form-control <?= isset($errors['end_point']) ? 'is-invalid' : '' ?>" 
                                   id="end_point" name="end_point" value="<?= htmlspecialchars($route['end_point']) ?>" required>
                            <?php if (isset($errors['end_point'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['end_point']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="distance" class="form-label">Distance (km)</label>
                            <input type="number" step="0.01" class="form-control <?= isset($errors['distance']) ? 'is-invalid' : '' ?>" 
                                   id="distance" name="distance" value="<?= htmlspecialchars($route['distance']) ?>" required>
                            <?php if (isset($errors['distance'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['distance']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="estimated_duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control <?= isset($errors['estimated_duration']) ? 'is-invalid' : '' ?>" 
                                   id="estimated_duration" name="estimated_duration" value="<?= htmlspecialchars($route['estimated_duration']) ?>" required>
                            <?php if (isset($errors['estimated_duration'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['estimated_duration']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" <?= $route['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $route['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="Under Review" <?= $route['status'] == 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($route['description']) ?></textarea>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Route Map</h5>
        </div>
        <div class="card-body">
            <div id="map" style="height: 400px; background-color: #f8f9fa;"></div>
            <div class="mt-3">
                <button type="button" id="addMarkerBtn" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-map-pin me-1"></i> Add Point
                </button>
                <button type="button" id="clearRouteBtn" class="btn btn-outline-danger btn-sm ms-2">
                    <i class="bx bx-trash me-1"></i> Clear Route
                </button>
            </div>
            <div id="coordinatesList" class="mt-3">
                <h6 class="mb-2">Route Points:</h6>
                <ul id="coordinatesItems" class="list-group"></ul>
            </div>
            <input type="hidden" name="coordinates" id="coordinatesInput" value="<?= htmlspecialchars(json_encode($coordinatesForJS)) ?>">
        </div>
    </div>

    <div class="text-end">
        <button type="submit" form="routeForm" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Save Changes
        </button>
        <a href="app-route-view.php?id=<?= $routeId ?>" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialize the map
    var map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var routePoints = JSON.parse(document.getElementById('coordinatesInput').value || '[]');
    var routeLine = null;
    var markers = [];

    // Function to initialize the map with existing points
    function initializeMap() {
        if (routePoints.length > 0) {
            // Add existing markers
            routePoints.forEach(function(point) {
                var marker = L.marker([point.lat, point.lng]).addTo(map)
                    .bindPopup('<div class="form-group">' +
                              '<label>Landmark:</label>' +
                              '<input type="text" class="form-control landmark-input" placeholder="Enter landmark name" value="' + (point.landmark || '') + '">' +
                              '<button class="btn btn-danger btn-sm mt-2 remove-marker">Remove</button>' +
                              '</div>')
                    .on('popupopen', function() {
                        // Focus on the input when popup opens
                        var input = this.getPopup()._content.querySelector('.landmark-input');
                        input.focus();
                        
                        // Update landmark when input changes
                        input.addEventListener('change', function() {
                            var markerIndex = markers.indexOf(this.closest('.leaflet-popup-content').marker);
                            if (markerIndex !== -1) {
                                routePoints[markerIndex].landmark = this.value;
                                updateCoordinatesInput();
                            }
                        });
                        
                        // Remove marker button in popup
                        this.getPopup()._content.querySelector('.remove-marker').addEventListener('click', function() {
                            var marker = this.closest('.leaflet-popup-content').marker;
                            removeMarker(marker);
                        });
                    });
                
                // Store reference to marker in popup content
                marker.getPopup()._content.marker = marker;
                markers.push(marker);
            });
            
            // Draw route line
            updateRouteLine();
            
            // Fit bounds to the route
            var bounds = L.latLngBounds(routePoints.map(p => [p.lat, p.lng]));
            map.fitBounds(bounds);
        } else {
            // Default view if no coordinates
            map.setView([5.6500, -0.1860], 13); // Default to UG coordinates
        }
    }

    // Add marker button click handler
    document.getElementById('addMarkerBtn').addEventListener('click', function() {
        map.once('click', function(e) {
            var marker = L.marker(e.latlng).addTo(map)
                .bindPopup('<div class="form-group">' +
                          '<label>Landmark:</label>' +
                          '<input type="text" class="form-control landmark-input" placeholder="Enter landmark name">' +
                          '<button class="btn btn-danger btn-sm mt-2 remove-marker">Remove</button>' +
                          '</div>')
                .on('popupopen', function() {
                    // Focus on the input when popup opens
                    this.getPopup()._content.querySelector('.landmark-input').focus();
                    
                    // Store reference to marker in popup content
                    this.getPopup()._content.marker = this;
                    
                    // Update landmark when input changes
                    this.getPopup()._content.querySelector('.landmark-input').addEventListener('change', function() {
                        var markerIndex = markers.indexOf(this.closest('.leaflet-popup-content').marker);
                        if (markerIndex !== -1) {
                            routePoints[markerIndex].landmark = this.value;
                            updateCoordinatesInput();
                        }
                    });
                    
                    // Remove marker button in popup
                    this.getPopup()._content.querySelector('.remove-marker').addEventListener('click', function() {
                        var marker = this.closest('.leaflet-popup-content').marker;
                        removeMarker(marker);
                    });
                });
            
            markers.push(marker);
            routePoints.push({
                lat: e.latlng.lat,
                lng: e.latlng.lng,
                landmark: ''
            });
            
            updateRouteLine();
            updateCoordinatesList();
        });
    });

    // Clear route button click handler
    document.getElementById('clearRouteBtn').addEventListener('click', function() {
        clearRoute();
    });

    // Remove a marker
    function removeMarker(marker) {
        var index = markers.indexOf(marker);
        if (index !== -1) {
            map.removeLayer(marker);
            markers.splice(index, 1);
            routePoints.splice(index, 1);
            updateRouteLine();
            updateCoordinatesList();
        }
    }

    // Update the route line on the map
    function updateRouteLine() {
        if (routeLine) {
            map.removeLayer(routeLine);
        }
        
        if (routePoints.length > 1) {
            var latlngs = routePoints.map(function(point) {
                return [point.lat, point.lng];
            });
            routeLine = L.polyline(latlngs, {color: 'blue'}).addTo(map);
            map.fitBounds(routeLine.getBounds());
        } else if (routePoints.length === 1) {
            map.setView([routePoints[0].lat, routePoints[0].lng], 15);
        }
    }

    // Update the coordinates list in the form
    function updateCoordinatesList() {
        var list = document.getElementById('coordinatesItems');
        list.innerHTML = '';
        
        if (routePoints.length === 0) {
            document.getElementById('coordinatesList').style.display = 'none';
            document.getElementById('coordinatesInput').value = '';
            return;
        }
        
        document.getElementById('coordinatesList').style.display = 'block';
        
        routePoints.forEach(function(point, index) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            
            var landmarkInput = document.createElement('input');
            landmarkInput.type = 'text';
            landmarkInput.className = 'form-control form-control-sm';
            landmarkInput.placeholder = 'Landmark name';
            landmarkInput.value = point.landmark || '';
            landmarkInput.addEventListener('change', function() {
                routePoints[index].landmark = this.value;
                updateCoordinatesInput();
            });
            
            var removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-danger btn-sm';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.addEventListener('click', function() {
                removeMarker(markers[index]);
            });
            
            var pointInfo = document.createElement('div');
            pointInfo.className = 'mr-2';
            pointInfo.textContent = (index + 1) + '. ' + point.lat.toFixed(6) + ', ' + point.lng.toFixed(6);
            
            var inputGroup = document.createElement('div');
            inputGroup.className = 'input-group input-group-sm';
            inputGroup.style.width = '200px';
            
            var inputGroupAppend = document.createElement('div');
            inputGroupAppend.className = 'input-group-append';
            
            inputGroup.appendChild(landmarkInput);
            inputGroupAppend.appendChild(removeBtn);
            inputGroup.appendChild(inputGroupAppend);
            
            li.appendChild(pointInfo);
            li.appendChild(inputGroup);
            list.appendChild(li);
        });
        
        updateCoordinatesInput();
    }

    // Update the hidden input with coordinates data
    function updateCoordinatesInput() {
        document.getElementById('coordinatesInput').value = JSON.stringify(routePoints);
    }

    // Clear the entire route
    function clearRoute() {
        markers.forEach(function(marker) {
            map.removeLayer(marker);
        });
        
        markers = [];
        routePoints = [];
        
        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }
        
        updateCoordinatesList();
    }

    // Initialize the map when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        updateCoordinatesList();
    });
</script>

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
    
    <!-- Leaflet Routing Machine -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    
</body>

<script>
$(document).ready(function() {
    $('#editRouteForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return false;
        }
        
        // Collect all form data
        var formData = $(this).serializeArray();
        
        // Collect stops data as array
        var stops = [];
        $('[name^="stops["]').each(function() {
            var nameParts = $(this).attr('name').match(/stops\[(\d+)\]\[(\w+)\]/);
            if (nameParts) {
                var index = nameParts[1];
                var field = nameParts[2];
                if (!stops[index]) stops[index] = {};
                stops[index][field] = $(this).val();
            }
        });
        
        // Filter out any empty indexes
        stops = stops.filter(function(stop) { return stop !== undefined; });
        
        // Add stops to form data
        formData.push({name: 'stops', value: JSON.stringify(stops)});
        
        // Add coordinates data (you'll need to get this from your map)
        var coordinates = [];
        // This should be populated with your actual coordinate data from the map
        // For example:
        stops.forEach(function(stop, index) {
            coordinates.push({
                lat: 0, // Replace with actual latitude
                lng: 0, // Replace with actual longitude
                landmark: stop.name || ''
            });
        });
        
        // Add coordinates to form data
        formData.push({name: 'coordinates', value: JSON.stringify(coordinates)});
        
        // Convert to URL-encoded format for submission
        var formDataSerialized = $.param(formData);
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formDataSerialized,
            success: function(response) {
                window.location.href = 'app-route-list.php';
            },
            error: function(xhr, status, error) {
                alert('Error updating route: ' + error);
            }
        });
    });
});
// Store original form data on page load
var originalFormData = $('#editRouteForm').serializeArray();
var originalStopCount = <?php echo count($coordinates); ?>;

// Reset button handler
$('#editRouteForm button[type="reset"]').on('click', function(e) {
    e.preventDefault();
    
    // Reset basic form fields
    document.getElementById('editRouteForm').reset();
    
    // Reset stops to original state
    $('#routeStopsContainer').empty();
    <?php foreach ($coordinates as $index => $coord): ?>
        $('#routeStopsContainer').append(`
            <div class="stop-item">
                <div class="stop-marker"><?php echo $index + 1; ?></div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="stops[<?php echo $index; ?>][name]" 
                           value="<?php echo htmlspecialchars($coord['landmark']); ?>" required>
                    <button class="btn btn-outline-danger remove-stop" type="button" <?php echo $index === 0 ? 'disabled' : ''; ?>>
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="stops[<?php echo $index; ?>][address]" 
                           value="<?php echo htmlspecialchars($coord['landmark']); ?>">
                    <button class="btn btn-outline-secondary geocode-stop" type="button">
                        <i class="bx bx-map"></i>
                    </button>
                </div>
            </div>
        `);
    <?php endforeach; ?>
    
    // Reset map to original state
    resetMapToOriginalState();
});

function resetMapToOriginalState() {
    // Clear current route if exists
    if (window.routeControl) {
        map.removeControl(window.routeControl);
    }
    
    // Clear all markers
    if (window.markers) {
        window.markers.forEach(marker => map.removeLayer(marker));
    }
    
    // Add original markers back
    window.markers = [];
    <?php foreach ($coordinates as $coord): ?>
        var marker = L.marker([<?php echo $coord['latitude']; ?>, <?php echo $coord['longitude']; ?>])
            .addTo(map)
            .bindPopup("<?php echo addslashes($coord['landmark']); ?>");
        window.markers.push(marker);
    <?php endforeach; ?>
    
    // If you had a route drawn, redraw it here
    <?php if (count($coordinates) > 1): ?>
        var waypoints = [
            <?php foreach ($coordinates as $coord): ?>
                L.latLng(<?php echo $coord['latitude']; ?>, <?php echo $coord['longitude']; ?>),
            <?php endforeach; ?>
        ];
        window.routeControl = L.Routing.control({
            waypoints: waypoints,
            routeWhileDragging: true,
            show: false
        }).addTo(map);
    <?php endif; ?>
}
</script>
</html>