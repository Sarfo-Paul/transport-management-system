<?php
require_once __DIR__ . '/config.php';

// Pagination setup
$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$records_per_page = 10;
$offset = ($current_page - 1) * $records_per_page;

// Get filter parameters with modern sanitization
$report_type = isset($_GET['type']) && in_array($_GET['type'], ['all', 'user', 'driver']) 
    ? $_GET['type'] 
    : 'all';

$status = isset($_GET['status']) && in_array($_GET['status'], ['all', 'Pending', 'In Progress', 'Resolved', 'Rejected']) 
    ? $_GET['status'] 
    : 'all';

try {
    // Base query for user reports
    $user_reports_query = "SELECT ur.*, u.first_name, u.last_name, u.user_type 
                          FROM user_reports ur 
                          JOIN users u ON ur.user_id = u.user_id";
    
    // Base query for driver reports
    $driver_reports_query = "SELECT dr.*, d.first_name, d.last_name 
                            FROM driver_reports dr 
                            JOIN drivers d ON dr.driver_id = d.driver_id";
    
    // Apply filters
    $user_where = [];
    $driver_where = [];
    
    if ($status !== 'all') {
        $user_where[] = "ur.status = '" . $conn->real_escape_string($status) . "'";
        $driver_where[] = "dr.status = '" . $conn->real_escape_string($status) . "'";
    }
    
    if (!empty($user_where)) {
        $user_reports_query .= " WHERE " . implode(" AND ", $user_where);
    }
    
    if (!empty($driver_where)) {
        $driver_reports_query .= " WHERE " . implode(" AND ", $driver_where);
    }
    
    // Get counts for pagination
    $user_reports_count = $conn->query(str_replace('ur.*', 'COUNT(*) as total', $user_reports_query))->fetch_assoc()['total'];
    $driver_reports_count = $conn->query(str_replace('dr.*', 'COUNT(*) as total', $driver_reports_query))->fetch_assoc()['total'];
    $total_reports = $user_reports_count + $driver_reports_count;
    $total_pages = ceil($total_reports / $records_per_page);
    
    // Fetch reports based on type
    if ($report_type === 'user' || $report_type === 'all') {
        $user_reports_query .= " ORDER BY ur.report_date DESC LIMIT $offset, $records_per_page";
        $user_reports = $conn->query($user_reports_query)->fetch_all(MYSQLI_ASSOC);
    }
    
    if ($report_type === 'driver' || $report_type === 'all') {
        $driver_reports_query .= " ORDER BY dr.report_date DESC LIMIT $offset, $records_per_page";
        $driver_reports = $conn->query($driver_reports_query)->fetch_all(MYSQLI_ASSOC);
    }
    
} catch (Exception $e) {
    error_log("Error fetching reports: " . $e->getMessage());
    $error = "Failed to load reports data";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management | Transport Management System</title>
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
                        <h4 class="fw-bold mb-4">Reports Management</h4>
                        
                        <!-- Filter Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Filters</h5>
                                        <form method="get" class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Report Type</label>
                                                <select name="type" class="form-select">
                                                    <option value="all" <?= $report_type === 'all' ? 'selected' : '' ?>>All Reports</option>
                                                    <option value="user" <?= $report_type === 'user' ? 'selected' : '' ?>>User Reports</option>
                                                    <option value="driver" <?= $report_type === 'driver' ? 'selected' : '' ?>>Driver Reports</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Statuses</option>
                                                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                    <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                    <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                                <a href="app-reports-list.php" class="btn btn-outline-secondary">Reset</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="mb-2">User Reports</h6>
                                                <h2 class="mb-0"><?= $user_reports_count ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <h6 class="mb-2">Driver Reports</h6>
                                                <h2 class="mb-0"><?= $driver_reports_count ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reports Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Reports</h5>
                                <div>
                                    <span class="badge bg-label-primary me-2">User: <?= $user_reports_count ?></span>
                                    <span class="badge bg-label-success">Driver: <?= $driver_reports_count ?></span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Type</th>
                                                <th>Reporter</th>
                                                <th>Title</th>
                                                <th>Issue Type</th>
                                                <th>Urgency</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (($report_type === 'user' || $report_type === 'all') && !empty($user_reports)): ?>
                                                <?php foreach($user_reports as $report): ?>
                                                    <tr>
                                                        <td>UR-<?= htmlspecialchars($report['report_id']) ?></td>
                                                        <td><span class="badge bg-label-primary">User</span></td>
                                                        <td>
                                                            <?= htmlspecialchars($report['first_name']) . ' ' . htmlspecialchars($report['last_name']) ?>
                                                            <br><small class="text-muted"><?= ucfirst($report['user_type']) ?></small>
                                                        </td>
                                                        <td><?= htmlspecialchars($report['title']) ?></td>
                                                        <td><?= ucfirst($report['issue_type']) ?></td>
                                                        <td>
                                                            <?php 
                                                            $urgency_class = [
                                                                'Low' => 'bg-label-info',
                                                                'Medium' => 'bg-label-warning',
                                                                'High' => 'bg-label-danger'
                                                            ];
                                                            ?>
                                                            <span class="badge <?= $urgency_class[$report['urgency']] ?? 'bg-label-secondary' ?>">
                                                                <?= $report['urgency'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('M d, Y H:i', strtotime($report['report_date'])) ?></td>
                                                        <td>
                                                            <?php 
                                                            $status_class = [
                                                                'Pending' => 'bg-label-secondary',
                                                                'In Progress' => 'bg-label-primary',
                                                                'Resolved' => 'bg-label-success',
                                                                'Rejected' => 'bg-label-danger'
                                                            ];
                                                            ?>
                                                            <span class="badge <?= $status_class[$report['status']] ?? 'bg-label-secondary' ?>">
                                                                <?= $report['status'] ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="app-report-details.php?type=user&id=<?= $report['report_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bx bx-show"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <?php if (($report_type === 'driver' || $report_type === 'all') && !empty($driver_reports)): ?>
                                                <?php foreach($driver_reports as $report): ?>
                                                    <tr>
                                                        <td>DR-<?= htmlspecialchars($report['report_id']) ?></td>
                                                        <td><span class="badge bg-label-success">Driver</span></td>
                                                        <td><?= htmlspecialchars($report['first_name']) . ' ' . htmlspecialchars($report['last_name']) ?></td>
                                                        <td><?= htmlspecialchars($report['title']) ?></td>
                                                        <td><?= ucfirst($report['issue_type']) ?></td>
                                                        <td>
                                                            <?php 
                                                            $urgency_class = [
                                                                'Low' => 'bg-label-info',
                                                                'Medium' => 'bg-label-warning',
                                                                'High' => 'bg-label-danger',
                                                                'Emergency' => 'bg-label-danger'
                                                            ];
                                                            ?>
                                                            <span class="badge <?= $urgency_class[$report['urgency']] ?? 'bg-label-secondary' ?>">
                                                                <?= $report['urgency'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('M d, Y H:i', strtotime($report['report_date'])) ?></td>
                                                        <td>
                                                            <?php 
                                                            $status_class = [
                                                                'Pending' => 'bg-label-secondary',
                                                                'In Progress' => 'bg-label-primary',
                                                                'Resolved' => 'bg-label-success',
                                                                'Rejected' => 'bg-label-danger'
                                                            ];
                                                            ?>
                                                            <span class="badge <?= $status_class[$report['status']] ?? 'bg-label-secondary' ?>">
                                                                <?= $report['status'] ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="app-report-details.php?type=driver&id=<?= $report['report_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bx bx-show"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <?php if ((empty($user_reports)) && (empty($driver_reports))): ?>
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">No reports found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <?php if ($total_pages > 1): ?>
                                <div class="card-footer">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                                                <a class="page-link" href="?page=<?= $current_page - 1 ?>&type=<?= htmlspecialchars($report_type) ?>&status=<?= htmlspecialchars($status) ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&type=<?= htmlspecialchars($report_type) ?>&status=<?= htmlspecialchars($status) ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                                                <a class="page-link" href="?page=<?= $current_page + 1 ?>&type=<?= htmlspecialchars($report_type) ?>&status=<?= htmlspecialchars($status) ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php include 'includes/footer.php'; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/scripts.php'; ?>
    
    <script>
    $(document).ready(function() {
        // Initialize any reports-specific scripts
    });
    </script>
</body>
</html>