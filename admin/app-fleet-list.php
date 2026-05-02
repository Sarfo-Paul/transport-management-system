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
$type = $_GET['type'] ?? '';

// Base query
$sql = "SELECT * FROM vehicles WHERE 1=1";
$countSql = "SELECT COUNT(*) as total FROM vehicles WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $searchTerm = "%$search%";
    $sql .= " AND (make LIKE ? OR model LIKE ? OR registration_number LIKE ?)";
    $countSql .= " AND (make LIKE ? OR model LIKE ? OR registration_number LIKE ?)";
}

// Add status condition
if (!empty($status) && $status != 'all') {
    $sql .= " AND status = ?";
    $countSql .= " AND status = ?";
}

// Add type condition
if (!empty($type) && $type != 'all') {
    $sql .= " AND vehicle_type = ?";
    $countSql .= " AND vehicle_type = ?";
}

// Complete queries
$sql .= " ORDER BY make, model LIMIT $limit OFFSET $offset";

try {
    // Get total count
    $countStmt = $conn->prepare($countSql);
    if ($countStmt) {
        $types = '';
        $params = [];
        
        if (!empty($search)) {
            $types .= 'sss';
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
        }
        
        if (!empty($status) && $status != 'all') {
            $types .= 's';
            array_push($params, $status);
        }
        
        if (!empty($type) && $type != 'all') {
            $types .= 's';
            array_push($params, $type);
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
            $types .= 'sss';
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
        }
        
        if (!empty($status) && $status != 'all') {
            $types .= 's';
            array_push($params, $status);
        }
        
        if (!empty($type) && $type != 'all') {
            $types .= 's';
            array_push($params, $type);
        }
        
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicles = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    echo "<p style='color:red;'>$error</p>";
}

function getVehicleStatusBadge($status) {
    switch ($status) {
        case 'Active': return 'success';
        case 'Maintenance': return 'warning';
        case 'Out of Service': return 'danger';
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
                            <h5 class="card-title mb-0">Fleet Management</h5>
                            <div class="d-flex">
                                <input type="text" id="searchInput" class="form-control me-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                                <select id="statusFilter" class="form-select me-2" style="width: 150px;">
                                    <option value="all" <?= empty($status) || $status == 'all' ? 'selected' : '' ?>>All Statuses</option>
                                    <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Maintenance" <?= $status == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                    <option value="Out of Service" <?= $status == 'Out of Service' ? 'selected' : '' ?>>Out of Service</option>
                                </select>
                                <select id="typeFilter" class="form-select me-2" style="width: 150px;">
                                    <option value="all" <?= empty($type) || $type == 'all' ? 'selected' : '' ?>>All Types</option>
                                    <option value="Bus" <?= $type == 'Bus' ? 'selected' : '' ?>>Bus</option>
                                    <option value="Minibus" <?= $type == 'Minibus' ? 'selected' : '' ?>>Minibus</option>
                                    <option value="Van" <?= $type == 'Van' ? 'selected' : '' ?>>Van</option>
                                    <option value="Car" <?= $type == 'Car' ? 'selected' : '' ?>>Car</option>
                                </select>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                                    <i class="bx bx-plus me-1"></i> Add Vehicle
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Vehicle</th>
                                            <th>Type</th>
                                            <th>Registration</th>
                                            <th>Capacity</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vehicles)): ?>
                                            <?php foreach ($vehicles as $vehicle): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($vehicle['image_path'])): ?>
                                                                <img src="<?= htmlspecialchars($vehicle['image_path']) ?>" class="rounded me-2" width="60" height="40" alt="Vehicle Image">
                                                            <?php else: ?>
                                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                                                    <i class="bx bx-car text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <strong><?= htmlspecialchars($vehicle['make']) ?></strong>
                                                                <div class="text-muted small"><?= htmlspecialchars($vehicle['model']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($vehicle['vehicle_type']) ?></td>
                                                    <td><?= htmlspecialchars($vehicle['registration_number']) ?></td>
                                                    <td><?= htmlspecialchars($vehicle['capacity']) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= getVehicleStatusBadge($vehicle['status']) ?>"><?= $vehicle['status'] ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item view-vehicle" href="app-fleet-view.php?id=<?= $vehicle['vehicle_id'] ?>">
                                                                    <i class="bx bx-show me-1"></i> View
                                                                </a>
                                                                <a class="dropdown-item edit-vehicle" href="app-fleet-edit.php?id=<?= $vehicle['vehicle_id'] ?>">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                                <a class="dropdown-item delete-vehicle" href="app-fleet-delete.php?id=<?= $vehicle['vehicle_id'] ?>" onclick="return confirm('Are you sure you want to delete this vehicle?')">
                                                                    <i class="bx bx-trash me-1"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No vehicles found</td>
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
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&type=<?= $type ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&type=<?= $type ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>&type=<?= $type ?>" aria-label="Next">
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

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addVehicleForm" action="app-fleet-process.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="make" class="form-label">Make</label>
                                    <input type="text" class="form-control" id="make" name="make" required>
                                </div>
                                <div class="mb-3">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control" id="model" name="model" required>
                                </div>
                                <div class="mb-3">
                                    <label for="year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="year" name="year" min="2000" max="<?= date('Y') + 1 ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="vehicleType" class="form-label">Vehicle Type</label>
                                    <select class="form-select" id="vehicleType" name="vehicleType" required>
                                        <option value="Bus">Bus</option>
                                        <option value="Minibus">Minibus</option>
                                        <option value="Van">Van</option>
                                        <option value="Car">Car</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registrationNumber" class="form-label">Registration Number</label>
                                    <input type="text" class="form-control" id="registrationNumber" name="registrationNumber" required>
                                </div>
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Active">Active</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Out of Service">Out of Service</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Vehicle Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Vehicle Modal -->
    <div class="modal fade" id="viewVehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vehicle Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="vehicleDetails">
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
            const type = document.getElementById('typeFilter').value;
            window.location.href = `?search=${encodeURIComponent(search)}&status=${status}&type=${type}`;
        }
    });

    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const search = document.getElementById('searchInput').value;
        const type = document.getElementById('typeFilter').value;
        window.location.href = `?search=${encodeURIComponent(search)}&status=${status}&type=${type}`;
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        const type = this.value;
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        window.location.href = `?search=${encodeURIComponent(search)}&status=${status}&type=${type}`;
    });

    // Form submission for adding vehicle
    document.getElementById('addVehicleForm').addEventListener('submit', function(e) {
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
            alert('An error occurred while saving the vehicle.');
        });
    });
    </script>
</body>
</html>