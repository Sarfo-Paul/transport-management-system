<?php
require_once __DIR__ . '/config.php';

// Get report type and ID
$report_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$report_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (empty($report_type) || empty($report_id)) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

try {
    if ($report_type === 'user') {
        // Get user report details
        $report = $conn->query("
            SELECT ur.*, u.first_name, u.last_name, u.user_type, u.email, u.phone,
                   a.first_name as admin_first, a.last_name as admin_last
            FROM user_reports ur
            JOIN users u ON ur.user_id = u.user_id
            LEFT JOIN users a ON ur.admin_id = a.user_id
            WHERE ur.report_id = $report_id
        ")->fetch_assoc();
        
        if (!$report) {
            throw new Exception("User report not found");
        }
        
        $report_table = 'user_reports';
    } elseif ($report_type === 'driver') {
        // Get driver report details
        $report = $conn->query("
            SELECT dr.*, d.first_name, d.last_name, d.contact_number as phone,
                   v.make, v.model, v.registration_number,
                   a.first_name as admin_first, a.last_name as admin_last
            FROM driver_reports dr
            JOIN drivers d ON dr.driver_id = d.driver_id
            LEFT JOIN vehicles v ON dr.vehicle_id = v.vehicle_id
            LEFT JOIN users a ON dr.admin_id = a.user_id
            WHERE dr.report_id = $report_id
        ")->fetch_assoc();
        
        if (!$report) {
            throw new Exception("Driver report not found");
        }
        
        $report_table = 'driver_reports';
    } else {
        throw new Exception("Invalid report type");
    }
    
    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $resolution_notes = filter_input(INPUT_POST, 'resolution_notes', FILTER_SANITIZE_STRING);
        
        if (in_array($new_status, ['In Progress', 'Resolved', 'Rejected'])) {
            $update_query = "UPDATE $report_table SET 
                            status = ?,
                            resolution_notes = ?,
                            resolution_date = NOW(),
                            admin_id = ?
                            WHERE report_id = ?";
            
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('ssii', $new_status, $resolution_notes, $_SESSION['user_id'], $report_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success = "Report status updated successfully";
                // Refresh report data
                header("Location: app-report-details.php?type=$report_type&id=$report_id");
                exit();
            } else {
                $error = "Failed to update report status";
            }
        } else {
            $error = "Invalid status selected";
        }
    }
    
} catch (Exception $e) {
    error_log("Error fetching report details: " . $e->getMessage());
    $error = "Failed to load report data";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details | Transport Management System</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/navbar.php'; ?>
                
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0">Report Details</h4>
                            <a href="app-reports-list.php" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back"></i> Back to Reports
                            </a>
                        </div>
                        
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($report)): ?>
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Report Details Card -->
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Report Information</h5>
                                        <span class="badge bg-<?= 
                                            $report['status'] === 'Pending' ? 'secondary' : 
                                            ($report['status'] === 'In Progress' ? 'primary' : 
                                            ($report['status'] === 'Resolved' ? 'success' : 'danger')) 
                                        ?>">
                                            <?= $report['status'] ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <h4><?= htmlspecialchars($report['title']) ?></h4>
                                            <p class="text-muted"><?= date('F j, Y \a\t g:i A', strtotime($report['report_date'])) ?></p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6>Description</h6>
                                            <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                                        </div>
                                        
                                        <?php if (!empty($report['location'])): ?>
                                        <div class="mb-3">
                                            <h6>Location</h6>
                                            <p><?= htmlspecialchars($report['location']) ?></p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($report_type === 'driver' && !empty($report['make'])): ?>
                                        <div class="mb-3">
                                            <h6>Vehicle Involved</h6>
                                            <p>
                                                <?= htmlspecialchars($report['make'] . ' ' . $report['model']) ?>
                                                (<?= htmlspecialchars($report['registration_number']) ?>)
                                            </p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($report['resolution_notes'])): ?>
                                        <div class="mb-3">
                                            <h6>Resolution Notes</h6>
                                            <p><?= nl2br(htmlspecialchars($report['resolution_notes'])) ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Reporter Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <?= $report_type === 'user' ? 'User Information' : 'Driver Information' ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <?= substr($report['first_name'], 0, 1) . substr($report['last_name'], 0, 1) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($report['first_name'] . ' ' . $report['last_name']) ?></h6>
                                                <?php if ($report_type === 'user'): ?>
                                                <small class="text-muted"><?= ucfirst($report['user_type']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="text-muted">Contact:</span>
                                            <p class="mb-0"><?= htmlspecialchars($report['phone']) ?></p>
                                        </div>
                                        
                                        <?php if ($report_type === 'user'): ?>
                                        <div class="mb-2">
                                            <span class="text-muted">Email:</span>
                                            <p class="mb-0"><?= htmlspecialchars($report['email']) ?></p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-2">
                                            <span class="text-muted">Issue Type:</span>
                                            <p class="mb-0"><?= ucfirst($report['issue_type']) ?></p>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="text-muted">Urgency:</span>
                                            <p class="mb-0">
                                                <span class="badge bg-<?= 
                                                    $report['urgency'] === 'Low' ? 'info' : 
                                                    ($report['urgency'] === 'Medium' ? 'warning' : 'danger')
                                                ?>">
                                                    <?= $report['urgency'] ?>
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <?php if (!empty($report['admin_first'])): ?>
                                        <div class="mb-2">
                                            <span class="text-muted">Handled By:</span>
                                            <p class="mb-0"><?= htmlspecialchars($report['admin_first'] . ' ' . $report['admin_last']) ?></p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($report['resolution_date'])): ?>
                                        <div class="mb-2">
                                            <span class="text-muted">Resolution Date:</span>
                                            <p class="mb-0"><?= date('F j, Y \a\t g:i A', strtotime($report['resolution_date'])) ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Status Update Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Update Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="mb-3">
                                                <label class="form-label">Current Status</label>
                                                <input type="text" class="form-control" value="<?= $report['status'] ?>" readonly>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">New Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="">Select status</option>
                                                    <option value="In Progress" <?= $report['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                    <option value="Resolved" <?= $report['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                    <option value="Rejected" <?= $report['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Resolution Notes</label>
                                                <textarea name="resolution_notes" class="form-control" rows="3"><?= 
                                                    !empty($report['resolution_notes']) ? htmlspecialchars($report['resolution_notes']) : '' 
                                                ?></textarea>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">Update Report</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-danger">Report not found</div>
                        <?php endif; ?>
                    </div>
                    
                    <?php include 'includes/footer.php'; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/scripts.php'; ?>
    
    <script>
    $(document).ready(function() {
        // Initialize any report details-specific scripts
    });
    </script>
</body>
</html>