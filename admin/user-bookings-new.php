<?php 
include 'includes/header.php';
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get available routes
$routes = getAvailableRoutes();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Get and validate form data
    $errors = [];
    $routeId = intval($_POST['route']);
    $scheduleId = intval($_POST['schedule']);
    $travelDate = trim($_POST['date']);
    $passengers = intval($_POST['passengers']);
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');

    if ($routeId <= 0) $errors[] = 'Route is required';
    if ($scheduleId <= 0) $errors[] = 'Schedule is required';
    $d = DateTime::createFromFormat('Y-m-d', $travelDate);
    if (!($d && $d->format('Y-m-d') === $travelDate)) {
        $errors[] = 'Date must be valid (YYYY-MM-DD)';
    } else {
        if ($travelDate < date('Y-m-d')) $errors[] = 'Date cannot be in the past';
    }
    if ($passengers < 1 || $passengers > 10) $errors[] = 'Passenger count must be 1-10';
    
    if (empty($errors)) {
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, route_id, schedule_id, travel_date, passenger_count, notes, status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("iiisis", $_SESSION['user_id'], $routeId, $scheduleId, $travelDate, $passengers, $notes);
    
        if ($stmt->execute()) {
            $success = "Booking submitted successfully!";
        } else {
            $error = "Error submitting booking: " . $conn->error;
        }
    } else {
        $error = implode('<br>', array_map('htmlspecialchars', $errors));
    }
}
?>

<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/user-menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/navbar.php'; ?>
                
                <div class="content-wrapper">

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">New Booking</h4>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <form id="newBookingForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="route" class="form-label">Select Route</label>
                        <select class="form-select" id="route" name="route" required>
                            <option value="" selected disabled>Choose route</option>
                            <?php while($route = $routes->fetch_assoc()): ?>
                                <option value="<?php echo $route['route_id']; ?>">
                                    <?php echo htmlspecialchars($route['route_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="schedule" class="form-label">Select Schedule</label>
                        <select class="form-select" id="schedule" name="schedule" required>
                            <option value="" selected disabled>Choose schedule</option>
                            <!-- Will be populated via JavaScript based on selected route -->
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="passengers" class="form-label">Number of Passengers</label>
                        <input type="number" class="form-control only-numbers" id="passengers" name="passengers" 
                               min="1" max="10" value="1" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Special Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <button type="submit" name="submit_booking" class="btn btn-primary">Submit Booking</button>
            </form>
        </div>
    </div>
</div>

<script>
// Load schedules when route or date changes
const routeEl = document.getElementById('route');
const dateEl = document.getElementById('date');
const scheduleEl = document.getElementById('schedule');

function resetScheduleSelect(message = 'Choose schedule') {
  scheduleEl.innerHTML = `<option value="" selected disabled>${message}</option>`;
}

async function loadSchedules() {
  const routeId = routeEl.value;
  const travelDate = dateEl.value; // YYYY-MM-DD (optional)

  if (!routeId) {
    resetScheduleSelect('Choose schedule');
    return;
  }

  resetScheduleSelect('Loading...');

  try {
    const url = new URL('get-schedules.php', window.location.href);
    url.searchParams.set('route_id', routeId);
    if (travelDate) url.searchParams.set('travel_date', travelDate);

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) {
      resetScheduleSelect('No schedules available for selection');
      return;
    }

    let options = '<option value="" selected disabled>Choose schedule</option>';
    data.forEach(s => {
      const time = (s.departure_time || '').slice(0,5);
      const seats = (s.available_seats != null) ? ` — ${s.available_seats} seats` : '';
      const vehicle = s.registration_number ? ` — ${s.registration_number}` : '';
      options += `<option value="${s.schedule_id}">${s.day_of_week} ${time}${seats}${vehicle}</option>`;
    });
    scheduleEl.innerHTML = options;
  } catch (e) {
    console.error('Failed to load schedules', e);
    resetScheduleSelect('Failed to load schedules');
  }
}

routeEl.addEventListener('change', loadSchedules);
dateEl.addEventListener('change', loadSchedules);
</script>

<?php include('includes/footer.php'); ?>