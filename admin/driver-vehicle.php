<?php
require_once __DIR__ . '/config.php';

// Check authentication and driver role
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$vehicle = null;
$maintenance = [];
$error = null;

try {
    // First get the driver_id and assigned vehicle
    $stmt = $conn->prepare("
        SELECT d.driver_id, d.assigned_vehicle_id 
        FROM drivers d 
        WHERE d.user_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $driver = $stmt->get_result()->fetch_assoc();
    
    if ($driver && $driver['assigned_vehicle_id']) {
        // Get vehicle details
        $stmt = $conn->prepare("
            SELECT v.*, 
                   CONCAT(v.make, ' ', v.model) AS vehicle_name
            FROM vehicles v
            WHERE v.vehicle_id = ?
        ");
        $stmt->bind_param("i", $driver['assigned_vehicle_id']);
        $stmt->execute();
        $vehicle = $stmt->get_result()->fetch_assoc();
        
        // Get maintenance records if vehicle exists
        if ($vehicle) {
            $stmt = $conn->prepare("
                SELECT * FROM vehicle_maintenance 
                WHERE vehicle_id = ?
                ORDER BY maintenance_date DESC
                LIMIT 5
            ");
            $stmt->bind_param("i", $vehicle['vehicle_id']);
            $stmt->execute();
            $maintenance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }
} catch (Exception $e) {
    error_log("Vehicle error: " . $e->getMessage());
    $error = "Failed to load vehicle data";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Vehicle | Driver Portal</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .vehicle-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .vehicle-header {
            height: 120px;
            background: linear-gradient(135deg, #3a86ff 0%, #2667cc 100%);
            position: relative;
            display: flex;
            align-items: flex-end;
        }
        .vehicle-image {
            width: 180px;
            position: absolute;
            bottom: 20px;
            right: 20px;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));
        }
        .vehicle-title {
            padding: 20px;
            color: white;
            z-index: 2;
        }
        .vehicle-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            padding: 20px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-value {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 13px;
            color: #7f8c8d;
        }
        .progress-thin {
            height: 6px;
            border-radius: 3px;
        }
        .maintenance-item {
            border-left: 3px solid;
            padding-left: 15px;
            margin-bottom: 15px;
            transition: all 0.2s ease;
        }
        .maintenance-item:hover {
            background-color: #f8f9fa;
        }
        .maintenance-routine {
            border-left-color: #4cc9a7;
        }
        .maintenance-repair {
            border-left-color: #f25c54;
        }
    </style>
</head>
<body class="layout-navbar-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'includes/driver-menu.php'; ?>
            
            <div class="layout-page">
                <?php include 'includes/driver-navbar.php'; ?>
                
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0">My Assigned Vehicle</h4>
                            <a href="driver-dashboard.php" class="btn btn-primary">
                                <i class="bx bx-chevron-left me-1"></i> Back to Dashboard
                            </a>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <?php if($vehicle): ?>
                            <!-- Vehicle Overview Card -->
                            <div class="card vehicle-card mb-4">
                                <div class="vehicle-header">
                                    <div class="vehicle-title">
                                        <h3 class="text-white mb-1"><?= htmlspecialchars($vehicle['make']) ?> <?= htmlspecialchars($vehicle['model']) ?></h3>
                                        <p class="text-white mb-0"><?= htmlspecialchars($vehicle['registration_number']) ?></p>
                                    </div>
                                    <img src="<?= $vehicle['image_path'] ? htmlspecialchars($vehicle['image_path']) : 'assets/img/vehicles/default-vehicle.png' ?>" 
                                         alt="Vehicle" class="vehicle-image">
                                </div>
                                <div class="vehicle-stats">
                                    <div class="stat-card">
                                        <div class="stat-value"><?= number_format($vehicle['current_mileage']) ?> km</div>
                                        <div class="stat-label">Current Mileage</div>
                                        <div class="progress progress-thin mt-2">
                                            <div class="progress-bar bg-success" 
                                                 role="progressbar" 
                                                 style="width: <?= min(($vehicle['current_mileage'] / 200000) * 100, 100) ?>%" 
                                                 aria-valuenow="<?= $vehicle['current_mileage'] ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="200000"></div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-value"><?= htmlspecialchars($vehicle['vehicle_type']) ?></div>
                                        <div class="stat-label">Vehicle Type</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-value"><?= $vehicle['capacity'] ?></div>
                                        <div class="stat-label">Passenger Capacity</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Vehicle Details -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Vehicle Details</h5>
                                            <span class="badge bg-label-<?= 
                                                $vehicle['status'] === 'Active' ? 'success' : 
                                                ($vehicle['status'] === 'Maintenance' ? 'warning' : 'danger')
                                            ?>">
                                                <?= $vehicle['status'] ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <small class="text-muted d-block">Year</small>
                                                    <p class="mb-0"><?= $vehicle['year'] ?></p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <small class="text-muted d-block">VIN</small>
                                                    <p class="mb-0"><?= htmlspecialchars($vehicle['vin']) ?></p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <small class="text-muted d-block">Purchase Date</small>
                                                    <p class="mb-0"><?= date('M j, Y', strtotime($vehicle['purchase_date'])) ?></p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <small class="text-muted d-block">Fuel Type</small>
                                                    <p class="mb-0"><?= htmlspecialchars($vehicle['fuel_type']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance Information -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="mb-0">Maintenance History</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if(!empty($maintenance)): ?>
                                                <?php foreach($maintenance as $record): ?>
                                                    <div class="maintenance-item <?= $record['maintenance_type'] === 'Routine' ? 'maintenance-routine' : 'maintenance-repair' ?>">
                                                        <div class="d-flex justify-content-between">
                                                            <strong><?= htmlspecialchars($record['maintenance_type']) ?></strong>
                                                            <small><?= date('M j, Y', strtotime($record['maintenance_date'])) ?></small>
                                                        </div>
                                                        <p class="mb-1 small"><?= htmlspecialchars($record['description']) ?></p>
                                                        <small class="text-muted">
                                                            Cost: GHS <?= number_format($record['cost'], 2) ?> | 
                                                            Status: <?= $record['status'] ?>
                                                        </small>
                                                    </div>
                                                <?php endforeach; ?>
                                                <a href="vehicle-maintenance.php?id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-sm btn-outline-primary mt-2">
                                                    View Full Maintenance History
                                                </a>
                                            <?php else: ?>
                                                <div class="alert alert-info">No maintenance records found</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No vehicle currently assigned to you. Please check back later or contact administration.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php include 'includes/footer.php'; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/scripts.php'; ?>
</body>
</html>