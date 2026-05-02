<?php 
require_once __DIR__ . '/config.php';
// Enforce student/staff access only
enforceRole(['student','staff']);

include 'includes/header.php';

// Get all active routes with their schedules
$routes = getAvailableRoutes();
?>

<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/user-menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/user-navbar.php'; ?>
                
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4">Routes & Schedules</h4>
                        
                        <!-- Search and Filter Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="" method="get">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="search" class="form-label">Search Routes</label>
                                            <input type="text" class="form-control" id="search" name="search" placeholder="Route name or location">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="day" class="form-label">Day of Week</label>
                                            <select class="form-select" id="day" name="day">
                                                <option value="">All Days</option>
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                                <option value="Saturday">Saturday</option>
                                                <option value="Sunday">Sunday</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="time" class="form-label">Time</label>
                                            <select class="form-select" id="time" name="time">
                                                <option value="">Any Time</option>
                                                <option value="morning">Morning (5am-12pm)</option>
                                                <option value="afternoon">Afternoon (12pm-5pm)</option>
                                                <option value="evening">Evening (5pm-9pm)</option>
                                                <option value="night">Night (9pm-5am)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">Available Routes</h5>
                                    <div class="card-body">
                                        <?php if ($routes->num_rows === 0): ?>
                                            <div class="alert alert-warning">No active routes found.</div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Route</th>
                                                            <th>Start Point</th>
                                                            <th>End Point</th>
                                                            <th>Distance (km)</th>
                                                            <th>Duration (min)</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while($route = $routes->fetch_assoc()): 
                                                            $schedules = getRouteSchedules($route['route_id']);
                                                        ?>
                                                        <tr data-route-id="<?= $route['route_id'] ?>">
                                                            <td><?= htmlspecialchars($route['route_name']) ?></td>
                                                            <td><?= htmlspecialchars($route['start_point']) ?></td>
                                                            <td><?= htmlspecialchars($route['end_point']) ?></td>
                                                            <td><?= $route['distance'] ?></td>
                                                            <td><?= $route['estimated_duration'] ?></td>
                                                            <td><?= htmlspecialchars($route['description']) ?></td>
                                                        </tr>
                                                        <?php if ($schedules->num_rows > 0): ?>
                                                        <tr>
                                                            <td colspan="6">
                                                                <div class="schedules-container">
                                                                    <strong>Schedules:</strong>
                                                                    <div class="row mt-2">
                                                                        <?php while($schedule = $schedules->fetch_assoc()): ?>
                                                                        <div class="col-md-4 mb-2">
                                                                            <div class="card schedule-card">
                                                                                <div class="card-body">
                                                                                    <h6 class="card-title"><?= htmlspecialchars($schedule['day_of_week']) ?></h6>
                                                                                    <p class="card-text">
                                                                                        <i class="bx bx-time"></i> <?= date('H:i', strtotime($schedule['departure_time'])) ?> - <?= date('H:i', strtotime($schedule['arrival_time'])) ?>
                                                                                    </p>
                                                                                    <p class="card-text">
                                                                                        <i class="bx bx-chair"></i> Seats: <?= $schedule['available_seats'] ?? $schedule['max_capacity'] ?>/<?= $schedule['max_capacity'] ?>
                                                                                    </p>
                                                                                    <p class="card-text">
                                                                                        <i class="bx bx-car"></i> Vehicle: <?= htmlspecialchars($schedule['registration_number'] ?? 'Not Assigned') ?>
                                                                                    </p>
                                                                                    <?php if (($schedule['available_seats'] ?? $schedule['max_capacity']) > 0): ?>
                                                                                    <a href="user-bookings-new.php?route_id=<?= $route['route_id'] ?>&schedule_id=<?= $schedule['schedule_id'] ?>" class="btn btn-sm btn-primary">Book Now</a>
                                                                                    <?php else: ?>
                                                                                    <button class="btn btn-sm btn-secondary" disabled>Fully Booked</button>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php endwhile; ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-muted">No schedules available for this route.</td>
                                                        </tr>
                                                        <?php endif; ?>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <h5 class="card-header">Live Routes Map</h5>
                                    <div class="card-body">
                                        <div id="map" style="height: 520px; position: relative;"></div>
                                        <div id="routeLegend" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Include Leaflet CSS and JS for maps -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
                    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

                    <script>
                    (function() {
                        const map = L.map('map').setView([5.6500, -0.1833], 14);
                        const base = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        const allRoutesLayer = L.layerGroup().addTo(map);
                        const colors = ['#7367F0', '#EA5455', '#FF9F43', '#28C76F', '#00CFE8', '#A8AAAE', '#FF85AC', '#5A8DEE', '#FDAC41', '#39DA8A'];

                        function clearRoutes() {
                            allRoutesLayer.clearLayers();
                            const legend = document.getElementById('routeLegend');
                            if (legend) legend.innerHTML = '';
                        }

                        function addLegendItem(name, color) {
                            const legend = document.getElementById('routeLegend');
                            if (!legend) return;
                            const item = document.createElement('div');
                            item.className = 'd-inline-flex align-items-center me-3 mb-2';
                            item.innerHTML = `<span style="display:inline-block;width:14px;height:14px;background:${color};border-radius:2px;margin-right:8px;"></span>${name}`;
                            legend.appendChild(item);
                        }

                        function fitAll(boundsArray) {
                            if (!boundsArray.length) return;
                            const bounds = L.latLngBounds(boundsArray);
                            map.fitBounds(bounds, { padding: [40, 40] });
                        }

                        async function loadRoutes() {
                            try {
                                const res = await fetch('get-all-routes.php', { cache: 'no-store' });
                                const routes = await res.json();
                                clearRoutes();
                                const allCoords = [];

                                routes.forEach((route, idx) => {
                                    if (!route.coordinates || route.coordinates.length < 2) return;
                                    const color = colors[idx % colors.length];
                                    const coords = route.coordinates.map(c => [c.lat, c.lng]);
                                    const poly = L.polyline(coords, { color, weight: 4, opacity: 0.9 }).addTo(allRoutesLayer);
                                    allCoords.push(...coords);

                                    // Start/End markers
                                    L.circleMarker(coords[0], { radius: 6, color: '#fff', fillColor: color, fillOpacity: 1, weight: 2 })
                                        .bindPopup(`<b>Start</b><br>${route.start_point || ''}`)
                                        .addTo(allRoutesLayer);
                                    L.circleMarker(coords[coords.length - 1], { radius: 6, color: '#fff', fillColor: color, fillOpacity: 1, weight: 2 })
                                        .bindPopup(`<b>End</b><br>${route.end_point || ''}`)
                                        .addTo(allRoutesLayer);

                                    addLegendItem(route.route_name, color);
                                });

                                fitAll(allCoords);
                            } catch (e) {
                                // noop
                            }
                        }

                        // Reset view control
                        L.control({ position: 'topright' }).onAdd = function() {
                            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                            div.innerHTML = '<a href="#" title="Reset View" style="line-height:26px;padding:0 8px;">🔄</a>';
                            div.onclick = function(e){ e.preventDefault(); loadRoutes(); };
                            return div;
                        }.addTo(map);

                        // Locate control
                        if (navigator.geolocation) {
                            L.control({ position: 'topright' }).onAdd = function() {
                                const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                                div.innerHTML = '<a href="#" title="Locate Me" style="line-height:26px;padding:0 8px;">📍</a>';
                                div.onclick = function(e){
                                    e.preventDefault();
                                    navigator.geolocation.getCurrentPosition(function(pos){
                                        const latlng = [pos.coords.latitude, pos.coords.longitude];
                                        L.marker(latlng).addTo(map).bindPopup('You are here').openPopup();
                                        map.setView(latlng, 16);
                                    });
                                };
                                return div;
                            }.addTo(map);
                        }

                        // Initial load and auto-refresh every 30s
                        loadRoutes();
                        setInterval(loadRoutes, 30000);
                    })();
                    </script>

                    <?php include('includes/footer.php'); ?>