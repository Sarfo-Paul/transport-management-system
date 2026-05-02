<?php 
include 'includes/header.php';
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user bookings
$bookings = getUserBookings($_SESSION['user_id']);
?>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/user-menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/user-navbar.php'; ?>
                
                <div class="content-wrapper">
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">My Bookings</h4>
    
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Passengers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php while($booking = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td>#BK-<?php echo str_pad($booking['booking_id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($booking['route_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($booking['travel_date'])); ?></td>
                        <td>
                            <?php 
                            $statusClass = '';
                            switch($booking['status']) {
                                case 'Confirmed':
                                    $statusClass = 'success';
                                    break;
                                case 'Pending':
                                    $statusClass = 'warning';
                                    break;
                                case 'Cancelled':
                                    $statusClass = 'danger';
                                    break;
                                case 'Completed':
                                    $statusClass = 'info';
                                    break;
                                default:
                                    $statusClass = 'secondary';
                            }
                            ?>
                            <span class="badge bg-label-<?php echo $statusClass; ?>">
                                <?php echo $booking['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $booking['passenger_count']; ?></td>
                        <td>
                            <a href="booking-details.php?id=<?php echo $booking['booking_id']; ?>" 
                               class="btn btn-sm btn-outline-primary">View</a>
                            <?php if($booking['status'] === 'Pending'): ?>
                                <a href="cancel-booking.php?id=<?php echo $booking['booking_id']; ?>" 
                                   class="btn btn-sm btn-outline-danger">Cancel</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>