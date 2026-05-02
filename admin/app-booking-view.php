<?php
require_once __DIR__ . '/config.php';

if (!isset($_GET['id'])) {
    header("Location: app-booking-list.php");
    exit();
}

$bookingId = (int)$_GET['id'];

// Get booking details
$query = "SELECT b.*, r.route_name, rs.departure_time, rs.arrival_time, 
          CONCAT(u.first_name, ' ', u.last_name) AS user_name, u.university_id, u.email, u.phone, u.user_type,
          v.registration_number, v.vehicle_type, v.make, v.model,
          CONCAT(d.first_name, ' ', d.last_name) AS driver_name, d.contact_number AS driver_phone
          FROM bookings b
          JOIN routes r ON b.route_id = r.route_id
          JOIN route_schedules rs ON b.schedule_id = rs.schedule_id
          JOIN users u ON b.user_id = u.user_id
          LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
          LEFT JOIN drivers d ON rs.driver_id = d.driver_id
          WHERE b.booking_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    header("Location: app-booking-list.php");
    exit();
}

// Get payment details if exists (with error handling)
$payment = null;
try {
    $query = "SELECT * FROM booking_payments WHERE booking_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        $payment = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
} catch (mysqli_sql_exception $e) {
    // Table doesn't exist - we'll just continue without payment info
    error_log("Payment table error: " . $e->getMessage());
}

// Get passengers if group booking
$passengers = [];
try {
    $query = "SELECT * FROM booking_passengers WHERE booking_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        $passengers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Passengers table error: " . $e->getMessage());
}

function getBookingStatusBadge($status) {
    switch ($status) {
        case 'Confirmed':
            return 'success';
        case 'Pending':
            return 'warning';
        case 'Cancelled':
            return 'danger';
        case 'Completed':
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
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Booking Details</h4>
        <div class="d-flex">
            <a href="app-booking-list.php" class="btn btn-outline-secondary me-2">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown">
                    <i class="bx bx-cog me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="app-booking-edit.php?id=<?= $booking['booking_id'] ?>">
                            <i class="bx bx-edit me-2"></i> Edit Booking
                        </a>
                    </li>
                    <?php if ($booking['status'] == 'Pending' || $booking['status'] == 'Confirmed'): ?>
                    <li>
                        <a class="dropdown-item text-danger" href="app-booking-cancel.php?id=<?= $booking['booking_id'] ?>" 
                           onclick="return confirm('Are you sure you want to cancel this booking?')">
                            <i class="bx bx-x me-2"></i> Cancel Booking
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a class="dropdown-item" href="app-booking-print.php?id=<?= $booking['booking_id'] ?>" target="_blank">
                            <i class="bx bx-printer me-2"></i> Print Details
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Booking Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Booking Information</h5>
                    <span class="badge bg-<?= getBookingStatusBadge($booking['status']) ?>"><?= $booking['status'] ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Booking ID</label>
                            <p class="form-control-static">#<?= $booking['booking_id'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Booking Date</label>
                            <p class="form-control-static"><?= date('M j, Y g:i A', strtotime($booking['booking_date'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Travel Date</label>
                            <p class="form-control-static"><?= date('M j, Y', strtotime($booking['travel_date'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Travel Time</label>
                            <p class="form-control-static">
                                <?= date('g:i A', strtotime($booking['departure_time'])) ?> - 
                                <?= date('g:i A', strtotime($booking['arrival_time'])) ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Route</label>
                            <p class="form-control-static"><?= $booking['route_name'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passengers</label>
                            <p class="form-control-static"><?= $booking['passenger_count'] ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pickup Point</label>
                            <p class="form-control-static"><?= $booking['pickup_point'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dropoff Point</label>
                            <p class="form-control-static"><?= $booking['dropoff_point'] ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <p class="form-control-static"><?= $booking['purpose'] ?? 'N/A' ?></p>
                    </div>

                    <?php if (!empty($booking['notes'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes</label>
                        <p class="form-control-static text-muted"><?= nl2br(htmlspecialchars($booking['notes'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - User and Transport Details -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <p class="form-control-static"><?= $booking['user_name'] ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">University ID</label>
                        <p class="form-control-static"><?= $booking['university_id'] ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <p class="form-control-static"><?= $booking['user_type'] ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <p class="form-control-static">
                            <?= $booking['email'] ?><br>
                            <?= $booking['phone'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transport Details</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($booking['registration_number'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Vehicle</label>
                            <p class="form-control-static">
                                <?= $booking['vehicle_type'] ?> (<?= $booking['make'] ?> <?= $booking['model'] ?>)<br>
                                <small class="text-muted">Reg: <?= $booking['registration_number'] ?></small>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2">
                            <i class="bx bx-info-circle me-2"></i> Vehicle not assigned yet
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($booking['driver_name'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Driver</label>
                            <p class="form-control-static">
                                <?= $booking['driver_name'] ?><br>
                                <small class="text-muted"><?= $booking['driver_phone'] ?></small>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2">
                            <i class="bx bx-info-circle me-2"></i> Driver not assigned yet
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/scripts.php'; ?>
        
  </body>
</html>