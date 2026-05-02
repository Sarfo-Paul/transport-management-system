<?php
require_once __DIR__ . '/config.php';

// Check authentication and driver role
if (!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

// Initialize variables
$error = '';
$success = '';
$driver_id = null;
$vehicle_options = [];
$current_vehicle = '';

try {
    // Get driver information
    $stmt = $conn->prepare("SELECT driver_id, assigned_vehicle_id FROM drivers WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $driver = $stmt->get_result()->fetch_assoc();
    
    if ($driver) {
        $driver_id = $driver['driver_id'];
        $current_vehicle = $driver['assigned_vehicle_id'];
    } else {
        $error = "Driver profile not found";
    }

    // Get list of vehicles for dropdown
    $vehicle_result = $conn->query("SELECT vehicle_id, make, model, registration_number FROM vehicles WHERE status = 'Active'");
    while ($row = $vehicle_result->fetch_assoc()) {
        $vehicle_options[$row['vehicle_id']] = $row['make'] . ' ' . $row['model'] . ' (' . $row['registration_number'] . ')';
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $issue_type = $_POST['issue_type'] ?? '';
        $vehicle_id = $_POST['vehicle'] ?? $current_vehicle;
        $urgency = $_POST['urgency'] ?? 'Low';
        $title = $_POST['issue_title'] ?? '';
        $description = $_POST['issue_description'] ?? '';
        $location = $_POST['location'] ?? '';
        
        // Validate inputs
        if (empty($issue_type)) {
            $error = "Issue type is required";
        } elseif (empty($title)) {
            $error = "Issue title is required";
        } elseif (empty($description)) {
            $error = "Description is required";
        } else {
            // Insert report into database
            $stmt = $conn->prepare("INSERT INTO driver_reports 
                (driver_id, issue_type, vehicle_id, urgency, title, description, location, status, report_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            
            $stmt->bind_param("issssss", 
                $driver_id, 
                $issue_type, 
                $vehicle_id, 
                $urgency, 
                $title, 
                $description, 
                $location
            );
            
            if ($stmt->execute()) {
                $success = "Your report has been submitted successfully!";
                // Clear form fields
                $_POST = [];
            } else {
                $error = "Failed to submit report. Please try again.";
            }
        }
    }
} catch (Exception $e) {
    error_log("Error in report issue: " . $e->getMessage());
    $error = "An error occurred while processing your request.";
}

include 'includes/header.php';
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <?php include 'includes/driver-menu.php'; ?>                    

        <!-- Layout container -->
        <div class="layout-page">
            <?php include 'includes/driver-navbar.php'; ?>

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">
                        <span class="text-muted fw-light">Driver /</span> Report Issue
                    </h4>

                    <!-- Display success/error messages -->
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <h5 class="card-header">Report an Issue</h5>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="issue-type" class="form-label">Issue Type</label>
                                                <select class="form-select" id="issue-type" name="issue_type" required>
                                                    <option value="">Select issue type</option>
                                                    <option value="vehicle" <?= ($_POST['issue_type'] ?? '') === 'vehicle' ? 'selected' : '' ?>>Vehicle Problem</option>
                                                    <option value="route" <?= ($_POST['issue_type'] ?? '') === 'route' ? 'selected' : '' ?>>Route Issue</option>
                                                    <option value="passenger" <?= ($_POST['issue_type'] ?? '') === 'passenger' ? 'selected' : '' ?>>Passenger Issue</option>
                                                    <option value="schedule" <?= ($_POST['issue_type'] ?? '') === 'schedule' ? 'selected' : '' ?>>Schedule Conflict</option>
                                                    <option value="other" <?= ($_POST['issue_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="vehicle" class="form-label">Vehicle (if applicable)</label>
                                                <select class="form-select" id="vehicle" name="vehicle">
                                                    <option value="">Select vehicle</option>
                                                    <?php foreach ($vehicle_options as $id => $label): ?>
                                                        <option value="<?= $id ?>" 
                                                            <?= ($_POST['vehicle'] ?? $current_vehicle) == $id ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($label) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Urgency Level</label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="urgency" id="urgency-low" value="Low" autocomplete="off" <?= ($_POST['urgency'] ?? 'Low') === 'Low' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-primary" for="urgency-low">Low</label>

                                                <input type="radio" class="btn-check" name="urgency" id="urgency-medium" value="Medium" autocomplete="off" <?= ($_POST['urgency'] ?? '') === 'Medium' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-primary" for="urgency-medium">Medium</label>

                                                <input type="radio" class="btn-check" name="urgency" id="urgency-high" value="High" autocomplete="off" <?= ($_POST['urgency'] ?? '') === 'High' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-primary" for="urgency-high">High</label>

                                                <input type="radio" class="btn-check" name="urgency" id="urgency-emergency" value="Emergency" autocomplete="off" <?= ($_POST['urgency'] ?? '') === 'Emergency' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-primary" for="urgency-emergency">Emergency</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="issue-title" class="form-label">Issue Title</label>
                                            <input type="text" class="form-control" id="issue-title" name="issue_title" 
                                                   placeholder="Brief description of the issue" 
                                                   value="<?= htmlspecialchars($_POST['issue_title'] ?? '') ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="issue-description" class="form-label">Detailed Description</label>
                                            <textarea class="form-control" id="issue-description" name="issue_description" 
                                                      rows="5" placeholder="Please provide as much detail as possible..." required><?= 
                                                      htmlspecialchars($_POST['issue_description'] ?? '') ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location (if applicable)</label>
                                            <input type="text" class="form-control" id="location" name="location" 
                                                   placeholder="Where is the issue occurring?"
                                                   value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary me-2">Submit Report</button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <?php include 'includes/footer.php'; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
    // Auto-select vehicle when issue type is vehicle-related
    document.getElementById('issue-type').addEventListener('change', function() {
        if (this.value === 'vehicle' && '<?= $current_vehicle ?>') {
            document.getElementById('vehicle').value = '<?= $current_vehicle ?>';
        }
    });
</script>

</body>
</html>