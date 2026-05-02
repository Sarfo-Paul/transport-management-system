<?php
require_once 'config.php'; 

$errors = [];
$route = [
    'route_name' => '',
    'start_point' => '',
    'end_point' => '',
    'distance' => '',
    'estimated_duration' => '',
    'description' => '',
    'status' => 'Active'
];

// Function to get status badge class (since we're not using common-functions.php)
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
            // Insert route
            $stmt = $conn->prepare("INSERT INTO routes (route_name, start_point, end_point, distance, estimated_duration, description, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdiss", 
                $route['route_name'],
                $route['start_point'],
                $route['end_point'],
                $route['distance'],
                $route['estimated_duration'],
                $route['description'],
                $route['status']
            );
            $stmt->execute();
            
            $routeId = $conn->insert_id;
            $stmt->close();

            // Process coordinates if provided
            if (!empty($_POST['coordinates'])) {
                $coordinates = json_decode($_POST['coordinates'], true);
                if (is_array($coordinates)) {
                    $stmt = $conn->prepare("INSERT INTO route_coordinates (route_id, latitude, longitude, sequence_order, landmark) 
                                          VALUES (?, ?, ?, ?, ?)");
                    
                    foreach ($coordinates as $index => $coord) {
                        $landmark = $coord['landmark'] ?? null;
                        $stmt->bind_param("iddis", 
                            $routeId,
                            $coord['lat'],
                            $coord['lng'],
                            $index + 1,
                            $landmark
                        );
                        $stmt->execute();
                    }
                    $stmt->close();
                }
            }

            $conn->commit();
            $_SESSION['success_message'] = 'Route added successfully!';
            header("Location: app-route-view.php?id=$routeId");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
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
        <h4 class="fw-bold">Add New Route</h4>
        <a href="app-route-list.php" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to List
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
            <input type="hidden" name="coordinates" id="coordinatesInput">
        </div>
    </div>

    <div class="text-end">
        <button type="submit" form="routeForm" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Save Route
        </button>
        <a href="app-route-list.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialize the map
    var map = L.map('map').setView([5.6500, -0.1860], 13); // Default to UG coordinates
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var routePoints = [];
    var routeLine = null;
    var markers = [];

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
                    
                    // Remove marker button in popup
                    this.getPopup()._content.querySelector('.remove-marker').addEventListener('click', function() {
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

    // Initialize with coordinates list hidden
    document.getElementById('coordinatesList').style.display = 'none';
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
    
    <script>
    document.getElementById('addRouteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = {
        routeName: document.getElementById('routeName').value,
        routeDescription: document.getElementById('routeDescription').value,
        routeType: document.getElementById('routeType').value,
        routeStatus: document.getElementById('routeStatus').value,
        estimatedDistance: document.getElementById('estimatedDistance').value,
        estimatedDuration: document.getElementById('estimatedDuration').value,
        firstTrip: document.getElementById('firstTrip').value,
        lastTrip: document.getElementById('lastTrip').value,
        frequency: document.getElementById('frequency').value,
        operatingDays: Array.from(document.querySelectorAll('input[name="operatingDays[]"]:checked')).map(el => el.value),
        stops: [],
        coordinates: []
    };
    
    // Collect stops data
    document.querySelectorAll('.stop-item').forEach((stop, index) => {
        const name = stop.querySelector('input[placeholder="Stop name"]').value;
        const address = stop.querySelector('input[placeholder="Address"]').value;
        formData.stops.push({name, address, sequence: index + 1});
    });
    
    // Collect coordinates from map (this would be populated by your map drawing functionality)
    // For demo purposes, we'll use dummy coordinates
    if (typeof routeCoordinates !== 'undefined') {
        formData.coordinates = routeCoordinates;
    } else {
        // Fallback dummy data
        formData.coordinates = [
            {lat: 5.650000, lng: -0.186000, landmark: 'Great Hall'},
            {lat: 5.650500, lng: -0.186500, landmark: 'Department of Mathematics'},
            {lat: 5.651000, lng: -0.187000, landmark: 'JQB Building'},
            {lat: 5.651500, lng: -0.187500, landmark: 'Balme Library'}
        ];
    }
    
    // Send data to server via AJAX
    fetch('app-route-add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Route Added',
                text: 'The new route has been successfully added to the system.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'app-route-list.php';
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'There was an error adding the route.'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'There was an error adding the route: ' + error
        });
    });
});
    </script>
</body>
</html>