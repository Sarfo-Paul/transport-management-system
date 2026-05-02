<?php
require_once 'config.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search and filter
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

// Base query
$sql = "SELECT b.*, 
               r.route_name, 
               rs.departure_time, rs.arrival_time,
               CONCAT(u.first_name, ' ', u.last_name) as passenger_name,
               u.university_id,
               v.make, v.model, v.registration_number,
               CONCAT(d.first_name, ' ', d.last_name) as driver_name
        FROM bookings b
        JOIN routes r ON b.route_id = r.route_id
        JOIN route_schedules rs ON b.schedule_id = rs.schedule_id
        JOIN users u ON b.user_id = u.user_id
        LEFT JOIN vehicles v ON b.vehicle_id = v.vehicle_id
        LEFT JOIN drivers d ON b.driver_id = d.driver_id
        WHERE 1=1";

$countSql = "SELECT COUNT(*) as total 
             FROM bookings b
             JOIN users u ON b.user_id = u.user_id
             WHERE 1=1";

// Prepare parameters for binding
$types = '';
$params = [];
$countTypes = '';
$countParams = [];

// Add search condition
if (!empty($search)) {
    $searchTerm = "%$search%";
    $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.university_id LIKE ? OR r.route_name LIKE ?)";
    $countSql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.university_id LIKE ?)";
    $types .= 'ssss';
    $countTypes .= 'sss';
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    array_push($countParams, $searchTerm, $searchTerm, $searchTerm);
}

// Add status condition
if (!empty($status) && $status != 'all') {
    $sql .= " AND b.status = ?";
    $countSql .= " AND b.status = ?";
    $types .= 's';
    $countTypes .= 's';
    array_push($params, $status);
    array_push($countParams, $status);
}

// Add date range conditions
if (!empty($dateFrom)) {
    $sql .= " AND b.travel_date >= ?";
    $countSql .= " AND b.travel_date >= ?";
    $types .= 's';
    $countTypes .= 's';
    array_push($params, $dateFrom);
    array_push($countParams, $dateFrom);
}
if (!empty($dateTo)) {
    $sql .= " AND b.travel_date <= ?";
    $countSql .= " AND b.travel_date <= ?";
    $types .= 's';
    $countTypes .= 's';
    array_push($params, $dateTo);
    array_push($countParams, $dateTo);
}

// Complete query with sorting and pagination
$sql .= " ORDER BY b.travel_date DESC, rs.departure_time LIMIT $limit OFFSET $offset";

// Get total count
try {
    $stmt = $conn->prepare($countSql);
    if ($stmt) {
        if (!empty($countTypes)) {
            $stmt->bind_param($countTypes, ...$countParams);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $totalRow = $result->fetch_assoc();
        $totalRows = $totalRow['total'];
        $totalPages = ceil($totalRows / $limit);
        $stmt->close();
    }
} catch (Exception $e) {
    $totalRows = 0;
    $totalPages = 0;
    echo "<p style='color:red;'>Error retrieving total bookings: " . $e->getMessage() . "</p>";
}

// Get bookings data
try {
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $bookings = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    $bookingError = $e->getMessage();
    echo "<p style='color:red;'>$bookingError</p>";
}

function getBookingStatusBadge($status) {
    switch ($status) {
        case 'Confirmed': return 'success';
        case 'Pending': return 'warning';
        case 'Cancelled': return 'danger';
        case 'Completed': return 'info';
        default: return 'secondary';
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
                    <!-- Content -->
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Booking Management</h5>
                                            <div class="d-flex">
                                                <div class="input-group input-group-merge me-2">
                                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                                    <input type="text" class="form-control" id="searchInput" placeholder="Search bookings..." value="<?= htmlspecialchars($search) ?>">
                                                </div>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                                                    <i class="bx bx-plus me-1"></i> Add Booking
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="statusFilter" class="form-label">Status Filter</label>
                                                    <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                                        <option value="all" <?= empty($status) || $status == 'all' ? 'selected' : '' ?>>All Statuses</option>
                                                        <option value="Confirmed" <?= $status == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                        <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="Cancelled" <?= $status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                        <option value="Completed" <?= $status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="dateFrom" class="form-label">From Date</label>
                                                    <input type="date" class="form-control" id="dateFrom" value="<?= htmlspecialchars($dateFrom) ?>" onchange="applyFilters()">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="dateTo" class="form-label">To Date</label>
                                                    <input type="date" class="form-control" id="dateTo" value="<?= htmlspecialchars($dateTo) ?>" onchange="applyFilters()">
                                                </div>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Booking ID</th>
                                                            <th>Passenger</th>
                                                            <th>Route</th>
                                                            <th>Travel Date</th>
                                                            <th>Time</th>
                                                            <th>Vehicle</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($bookings)): ?>
                                                            <?php foreach ($bookings as $booking): ?>
                                                                <tr>
                                                                    <td>#<?= $booking['booking_id'] ?></td>
                                                                    <td>
                                                                        <?= htmlspecialchars($booking['passenger_name']) ?>
                                                                        <small class="text-muted d-block"><?= $booking['university_id'] ?></small>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($booking['route_name']) ?></td>
                                                                    <td><?= date('M j, Y', strtotime($booking['travel_date'])) ?></td>
                                                                    <td>
                                                                        <?= date('g:i A', strtotime($booking['departure_time'])) ?> - 
                                                                        <?= date('g:i A', strtotime($booking['arrival_time'])) ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($booking['make']): ?>
                                                                            <?= htmlspecialchars($booking['make'] . ' ' . $booking['model']) ?>
                                                                            <small class="text-muted d-block"><?= $booking['registration_number'] ?></small>
                                                                        <?php else: ?>
                                                                            Not assigned
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-<?= getBookingStatusBadge($booking['status']) ?>">
                                                                            <?= $booking['status'] ?>
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="dropdown">
                                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <button class="dropdown-item view-booking" data-id="<?= $booking['booking_id'] ?>">
                                                                                    <i class="bx bx-show me-1"></i> View
                                                                                </button>
                                                                                <button class="dropdown-item edit-booking" data-id="<?= $booking['booking_id'] ?>">
                                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                                </button>
                                                                                <?php if ($booking['status'] == 'Pending' || $booking['status'] == 'Confirmed'): ?>
                                                                                <button class="dropdown-item cancel-booking" data-id="<?= $booking['booking_id'] ?>">
                                                                                    <i class="bx bx-x me-1"></i> Cancel
                                                                                </button>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="8" class="text-center">No bookings found</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <!-- Pagination -->
                                            <?php if ($totalPages > 1): ?>
                                                <nav aria-label="Page navigation" class="mt-4">
                                                    <ul class="pagination justify-content-center">
                                                        <?php if ($page > 1): ?>
                                                            <li class="page-item">
                                                                <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>" aria-label="Previous">
                                                                    <span aria-hidden="true">&laquo;</span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>"><?= $i ?></a>
                                                            </li>
                                                        <?php endfor; ?>
                                                        
                                                        <?php if ($page < $totalPages): ?>
                                                            <li class="page-item">
                                                                <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>" aria-label="Next">
                                                                    <span aria-hidden="true">&raquo;</span>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </nav>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Booking Modal -->
                    <div class="modal fade" id="addBookingModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Booking</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="addBookingForm" action="app-booking-process.php" method="POST">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="passengerId" class="form-label">Passenger</label>
                                                    <select class="form-select" id="passengerId" name="passengerId" required>
                                                        <option value="">Select Passenger</option>
                                                        <?php 
                                                        $passengers = $conn->query("SELECT user_id, CONCAT(first_name, ' ', last_name) as name, university_id FROM users ORDER BY last_name");
                                                        while ($passenger = $passengers->fetch_assoc()): ?>
                                                            <option value="<?= $passenger['user_id'] ?>">
                                                                <?= htmlspecialchars($passenger['name']) ?> (<?= $passenger['university_id'] ?>)
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="routeId" class="form-label">Route</label>
                                                    <select class="form-select" id="routeId" name="routeId" required>
                                                        <option value="">Select Route</option>
                                                        <?php 
                                                        $routes = $conn->query("SELECT route_id, route_name FROM routes WHERE status = 'Active' ORDER BY route_name");
                                                        while ($route = $routes->fetch_assoc()): ?>
                                                            <option value="<?= $route['route_id'] ?>"><?= htmlspecialchars($route['route_name']) ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="scheduleId" class="form-label">Schedule</label>
                                                    <select class="form-select" id="scheduleId" name="scheduleId" required>
                                                        <option value="">Select Route First</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="travelDate" class="form-label">Travel Date</label>
                                                    <input type="date" class="form-control" id="travelDate" name="travelDate" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="passengerCount" class="form-label">Passenger Count</label>
                                                    <input type="number" class="form-control" id="passengerCount" name="passengerCount" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" id="status" name="status" required>
                                                        <option value="Confirmed">Confirmed</option>
                                                        <option value="Pending">Pending</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Special Requests</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Booking</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- View Booking Modal -->
                    <div class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Booking Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="bookingDetails">
                                    <!-- Details will be loaded here via AJAX -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            window.location.href = `?search=${encodeURIComponent(search)}&status=${status}&date_from=${dateFrom}&date_to=${dateTo}`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });

            // View booking
            document.querySelectorAll('.view-booking').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    fetch(`app-booking-view.php?id=${id}`)
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('bookingDetails').innerHTML = data;
                            const modal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
                            modal.show();
                        });
                });
            });

            // Edit booking
            document.querySelectorAll('.edit-booking').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    window.location.href = `app-booking-edit.php?id=${id}`;
                });
            });

            // Cancel booking
            document.querySelectorAll('.cancel-booking').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel this booking?')) {
                        const id = this.getAttribute('data-id');
                        window.location.href = `app-booking-cancel.php?id=${id}`;
                    }
                });
            });

            // Load schedules when route changes
            document.getElementById('routeId').addEventListener('change', function() {
                const routeId = this.value;
                const scheduleSelect = document.getElementById('scheduleId');
                
                if (routeId) {
                    fetch(`get-schedules.php?route_id=${routeId}`)
                        .then(response => response.json())
                        .then(data => {
                            scheduleSelect.innerHTML = '<option value="">Select Schedule</option>';
                            data.forEach(schedule => {
                                const option = document.createElement('option');
                                option.value = schedule.schedule_id;
                                option.textContent = `${schedule.departure_time} - ${schedule.arrival_time}`;
                                scheduleSelect.appendChild(option);
                            });
                        });
                } else {
                    scheduleSelect.innerHTML = '<option value="">Select Route First</option>';
                }
            });
        });
    </script>
</body>
</html>