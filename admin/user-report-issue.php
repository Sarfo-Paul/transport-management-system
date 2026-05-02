<?php
require_once __DIR__ . '/config.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$error = '';
$success = '';
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    // Get user information
    $stmt = $conn->prepare("SELECT first_name, last_name, university_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        $error = "User profile not found";
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $issue_type = $_POST['issue_type'] ?? '';
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
            $stmt = $conn->prepare("INSERT INTO user_reports 
                (user_id, user_type, issue_type, urgency, title, description, location, status, report_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            
            $stmt->bind_param("issssss", 
                $user_id, 
                $user_type,
                $issue_type, 
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
        <?php include 'includes/user-menu.php'; ?>                    

        <!-- Layout container -->
        <div class="layout-page">
            <?php include 'includes/user-navbar.php'; ?>

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <h4 class="fw-bold py-3 mb-4">
                        <span class="text-muted fw-light">User /</span> Report Issue
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
                                    <div class="mb-4 p-3 bg-light rounded">
                                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                        <p class="mb-1"><strong>University ID:</strong> <?= htmlspecialchars($user['university_id']) ?></p>
                                        <p class="mb-0"><strong>User Type:</strong> <?= ucfirst($user_type) ?></p>
                                    </div>
                                    
                                    <form method="post">
                                        <div class="mb-3">
                                            <label for="issue-type" class="form-label">Issue Type</label>
                                            <select class="form-select" id="issue-type" name="issue_type" required>
                                                <option value="">Select issue type</option>
                                                <option value="booking" <?= ($_POST['issue_type'] ?? '') === 'booking' ? 'selected' : '' ?>>Booking Problem</option>
                                                <option value="driver" <?= ($_POST['issue_type'] ?? '') === 'driver' ? 'selected' : '' ?>>Driver Behavior</option>
                                                <option value="vehicle" <?= ($_POST['issue_type'] ?? '') === 'vehicle' ? 'selected' : '' ?>>Vehicle Condition</option>
                                                <option value="route" <?= ($_POST['issue_type'] ?? '') === 'route' ? 'selected' : '' ?>>Route Issue</option>
                                                <option value="payment" <?= ($_POST['issue_type'] ?? '') === 'payment' ? 'selected' : '' ?>>Payment Problem</option>
                                                <option value="other" <?= ($_POST['issue_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                            </select>
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
                                                   placeholder="Where did the issue occur?"
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
</body>
</html>