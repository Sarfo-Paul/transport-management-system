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

// Base query
$sql = "SELECT * FROM drivers WHERE 1=1";
$countSql = "SELECT COUNT(*) as total FROM drivers WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $searchTerm = "%$search%";
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR license_number LIKE ? OR email LIKE ?)";
    $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR license_number LIKE ? OR email LIKE ?)";
}

// Add status condition
if (!empty($status) && $status != 'all') {
    $sql .= " AND status = ?";
    $countSql .= " AND status = ?";
}

// Complete queries
$sql .= " ORDER BY last_name, first_name LIMIT $limit OFFSET $offset";

try {
    // Get total count
    $countStmt = $conn->prepare($countSql);
    if ($countStmt) {
        $types = '';
        $params = [];
        
        if (!empty($search)) {
            $types .= 'ssss';
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        }
        
        if (!empty($status) && $status != 'all') {
            $types .= 's';
            array_push($params, $status);
        }
        
        if (!empty($types)) {
            $countStmt->bind_param($types, ...$params);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRow = $countResult->fetch_assoc();
        $totalRows = $totalRow['total'];
        $totalPages = ceil($totalRows / $limit);
        $countStmt->close();
    }

    // Get data
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $types = '';
        $params = [];
        
        if (!empty($search)) {
            $types .= 'ssss';
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        }
        
        if (!empty($status) && $status != 'all') {
            $types .= 's';
            array_push($params, $status);
        }
        
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $drivers = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    echo "<p style='color:red;'>$error</p>";
}

function getDriverStatusBadge($status) {
    switch ($status) {
        case 'Active': return 'success';
        case 'On Leave': return 'warning';
        case 'Suspended': return 'danger';
        case 'Terminated': return 'secondary';
        default: return 'primary';
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
                            <h5 class="card-title mb-0">Driver Management</h5>
                            <div class="d-flex">
                                <input type="text" id="searchInput" class="form-control me-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                                <select id="statusFilter" class="form-select me-2" style="width: 150px;">
                                    <option value="all" <?= empty($status) || $status == 'all' ? 'selected' : '' ?>>All Statuses</option>
                                    <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="On Leave" <?= $status == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                                    <option value="Suspended" <?= $status == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                    <option value="Terminated" <?= $status == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDriverModal">
                                    <i class="bx bx-plus me-1"></i> Add Driver
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>License</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Hire Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($drivers)): ?>
                                            <?php foreach ($drivers as $driver): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($driver['photo_path'])): ?>
                                                                <img src="<?= htmlspecialchars($driver['photo_path']) ?>" class="rounded-circle me-2" width="40" height="40" alt="Driver Photo">
                                                            <?php else: ?>
                                                                <div class="avatar avatar-sm me-2">
                                                                    <span class="avatar-initial rounded-circle bg-label-primary"><?= substr($driver['first_name'], 0, 1) . substr($driver['last_name'], 0, 1) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <strong><?= htmlspecialchars($driver['first_name']) . ' ' . htmlspecialchars($driver['last_name']) ?></strong>
                                                                <div class="text-muted small"><?= htmlspecialchars($driver['email']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($driver['license_number']) ?>
                                                        <div class="text-muted small">Exp: <?= date('M Y', strtotime($driver['license_expiry'])) ?></div>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($driver['contact_number']) ?>
                                                        <div class="text-muted small"><?= htmlspecialchars($driver['address']) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= getDriverStatusBadge($driver['status']) ?>"><?= $driver['status'] ?></span>
                                                    </td>
                                                    <td><?= date('M j, Y', strtotime($driver['hire_date'])) ?></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item view-driver" href="app-driver-view.php?id=<?= $driver['driver_id'] ?>">
                                                                    <i class="bx bx-show me-1"></i> View
                                                                </a>
                                                                <a class="dropdown-item edit-driver" href="app-driver-edit.php?id=<?= $driver['driver_id'] ?>">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                                <a class="dropdown-item delete-driver" href="app-driver-delete.php?id=<?= $driver['driver_id'] ?>" onclick="return confirm('Are you sure you want to delete this driver?')">
                                                                    <i class="bx bx-trash me-1"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No drivers found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center mt-3">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>" aria-label="Next">
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

    <!-- Add Driver Modal -->
    <div class="modal fade" id="addDriverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addDriverForm" action="app-driver-process.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contactNumber" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="licenseNumber" class="form-label">License Number</label>
                                    <input type="text" class="form-control" id="licenseNumber" name="licenseNumber" required>
                                </div>
                                <div class="mb-3">
                                    <label for="licenseExpiry" class="form-label">License Expiry Date</label>
                                    <input type="date" class="form-control" id="licenseExpiry" name="licenseExpiry" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Active">Active</option>
                                        <option value="On Leave">On Leave</option>
                                        <option value="Suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Driver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Driver Modal -->
    <div class="modal fade" id="viewDriverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Driver Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="driverDetails">
                    <!-- Details will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
                    <!-- / Content -->
                   
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
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            const search = this.value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = `?search=${encodeURIComponent(search)}&status=${status}`;
        }
    });

    // Status filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const search = document.getElementById('searchInput').value;
        window.location.href = `?search=${encodeURIComponent(search)}&status=${status}`;
    });

    // Form submission for adding driver
    document.getElementById('addDriverForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the driver.');
        });
    });
    </script>
</body>
</html>