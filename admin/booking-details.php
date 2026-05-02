<?php
include 'includes/header.php';
include 'config.php';

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    header("Location: user-bookings.php");
    exit();
}

$bookingId = intval($_GET['id']);

// Get booking details
$stmt = $conn->prepare("SELECT b.*, r.route_name, r.start_point, r.end_point, 
                       s.departure_time, s.arrival_time, s.day_of_week
                       FROM bookings b
                       JOIN routes r ON b.route_id = r.route_id
                       LEFT JOIN route_schedules s ON b.schedule_id = s.schedule_id
                       WHERE b.booking_id = ? AND b.user_id = ?");
$stmt->bind_param("ii", $bookingId, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header("Location: user-bookings.php");
    exit();
}
?>
<?php include 'includes/user-header.php'; ?>                    
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
       
        <?php include 'includes/user-menu.php'; ?>                    

            
            <!-- Layout container -->
            <div class="layout-page">
            <?php include 'includes/user-navbar.php'; ?>
                
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="content-wrapper">
                

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Booking Details #BK-<?php echo str_pad($booking['booking_id'], 4, '0', STR_PAD_LEFT); ?></h4>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Route Information</h5>
                    <p><strong>Route:</strong> <?php echo htmlspecialchars($booking['route_name']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($booking['start_point']); ?></p>
                    <p><strong>To:</strong> <?php echo htmlspecialchars($booking['end_point']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Schedule Details</h5>
                    <p><strong>Travel Date:</strong> <?php echo date('Y-m-d', strtotime($booking['travel_date'])); ?></p>
                    <?php if ($booking['day_of_week']): ?>
                        <p><strong>Day:</strong> <?php echo htmlspecialchars($booking['day_of_week']); ?></p>
                        <p><strong>Departure Time:</strong> <?php echo date('H:i', strtotime($booking['departure_time'])); ?></p>
                        <p><strong>Arrival Time:</strong> <?php echo date('H:i', strtotime($booking['arrival_time'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Booking Information</h5>
                    <p><strong>Booking Date:</strong> <?php echo date('Y-m-d H:i', strtotime($booking['created_at'])); ?></p>
                    <p><strong>Passengers:</strong> <?php echo $booking['passenger_count']; ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-label-<?php 
                            switch($booking['status']) {
                                case 'Confirmed': echo 'success'; break;
                                case 'Pending': echo 'warning'; break;
                                case 'Cancelled': echo 'danger'; break;
                                case 'Completed': echo 'info'; break;
                                default: echo 'secondary';
                            }
                        ?>">
                            <?php echo $booking['status']; ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Additional Information</h5>
                    <p><strong>Purpose:</strong> <?php echo htmlspecialchars($booking['purpose'] ?? 'N/A'); ?></p>
                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes'] ?? 'N/A'); ?></p>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="user-bookings.php" class="btn btn-secondary">Back to Bookings</a>
                <?php if ($booking['status'] === 'Pending'): ?>
                    <a href="cancel-booking.php?id=<?php echo $booking['booking_id']; ?>" 
                       class="btn btn-danger">Cancel Booking</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
   
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
    <?php include 'includes/scripts.php'; ?>  
<?php include('includes/footer.php'); ?>
</body>
</html>