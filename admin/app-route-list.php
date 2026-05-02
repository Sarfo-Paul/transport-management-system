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
$sql = "SELECT * FROM routes WHERE 1=1";
$countSql = "SELECT COUNT(*) as total FROM routes WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $searchTerm = "%$search%";
    $sql .= " AND (route_name LIKE ? OR start_point LIKE ? OR end_point LIKE ?)";
    $countSql .= " AND (route_name LIKE ? OR start_point LIKE ? OR end_point LIKE ?)";
}

// Add status condition
if (!empty($status) && $status != 'all') {
    $sql .= " AND status = ?";
    $countSql .= " AND status = ?";
}

// Complete queries
$sql .= " ORDER BY route_name LIMIT $limit OFFSET $offset";

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
        
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $routes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    echo "<p style='color:red;'>$error</p>";
}

function getRouteStatusBadge($status) {
    switch ($status) {
        case 'Active': return 'success';
        case 'Inactive': return 'secondary';
        case 'Under Review': return 'warning';
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
                            <h5 class="card-title mb-0">Route Management</h5>
                            <div class="d-flex">
                                <input type="text" id="searchInput" class="form-control me-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                                <select id="statusFilter" class="form-select me-2" style="width: 150px;">
                                    <option value="all" <?= empty($status) || $status == 'all' ? 'selected' : '' ?>>All Statuses</option>
                                    <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= $status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="Under Review" <?= $status == 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                </select>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRouteModal">
                                    <i class="bx bx-plus me-1"></i> Add Route
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Route Name</th>
                                            <th>Start Point</th>
                                            <th>End Point</th>
                                            <th>Distance</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($routes)): ?>
                                            <?php foreach ($routes as $route): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($route['route_name']) ?></td>
                                                    <td><?= htmlspecialchars($route['start_point']) ?></td>
                                                    <td><?= htmlspecialchars($route['end_point']) ?></td>
                                                    <td><?= htmlspecialchars($route['distance']) ?> km</td>
                                                    <td><?= htmlspecialchars($route['estimated_duration']) ?> min</td>
                                                    <td>
                                                        <span class="badge bg-<?= getRouteStatusBadge($route['status']) ?>"><?= $route['status'] ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item view-route" href="app-route-view.php?id=<?= $route['route_id'] ?>">
                                                                    <i class="bx bx-show me-1"></i> View
                                                                </a>
                                                                <a class="dropdown-item edit-route" href="app-route-edit.php?id=<?= $route['route_id'] ?>">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                                <a class="dropdown-item map-route" href="app-route-map.php?id=<?= $route['route_id'] ?>">
                                                                    <i class="bx bx-map me-1"></i> Map
                                                                </a>
                                                                <a class="dropdown-item delete-route" href="app-route-delete.php?id=<?= $route['route_id'] ?>" onclick="return confirm('Are you sure you want to delete this route?')">
                                                                    <i class="bx bx-trash me-1"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No routes found</td>
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

    <!-- Add Route Modal -->
    <div class="modal fade" id="addRouteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRouteForm" action="app-routes-process.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="routeName" class="form-label">Route Name</label>
                                    <input type="text" class="form-control" id="routeName" name="routeName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="startPoint" class="form-label">Start Point</label>
                                    <input type="text" class="form-control" id="startPoint" name="startPoint" required>
                                </div>
                                <div class="mb-3">
                                    <label for="endPoint" class="form-label">End Point</label>
                                    <input type="text" class="form-control" id="endPoint" name="endPoint" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="distance" class="form-label">Distance (km)</label>
                                    <input type="number" class="form-control" id="distance" name="distance" step="0.1" min="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="estimatedDuration" class="form-label">Estimated Duration (minutes)</label>
                                    <input type="number" class="form-control" id="estimatedDuration" name="estimatedDuration" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Under Review">Under Review</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Route</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Route Modal -->
    <div class="modal fade" id="viewRouteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Route Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="routeDetails">
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

    // Form submission for adding route
    document.getElementById('addRouteForm').addEventListener('submit', function(e) {
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
            alert('An error occurred while saving the route.');
        });
    });
    </script>
</body>
</html>