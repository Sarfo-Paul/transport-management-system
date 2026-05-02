<?php
require_once __DIR__ . '/config.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$driverDetails = [];
$vehicleDetails = [];
$error = '';
$success = '';

try {
    // Get driver details
    $stmt = $conn->prepare("
        SELECT d.*, u.email, u.phone, u.first_name, u.last_name 
        FROM drivers d 
        JOIN users u ON d.user_id = u.user_id 
        WHERE d.user_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $driverDetails = $stmt->get_result()->fetch_assoc();
    
    // Get assigned vehicle if exists
    if ($driverDetails && isset($driverDetails['assigned_vehicle_id'])) {
        $stmt = $conn->prepare("
            SELECT * FROM vehicles 
            WHERE vehicle_id = ?
        ");
        $stmt->bind_param("i", $driverDetails['assigned_vehicle_id']);
        $stmt->execute();
        $vehicleDetails = $stmt->get_result()->fetch_assoc();
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle profile updates
        if (isset($_POST['save_changes'])) {
            $firstName = trim($_POST['firstName']);
            $lastName = trim($_POST['lastName']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $licenseNumber = trim($_POST['licenseNumber']);
            $licenseExpiry = $_POST['licenseExpiry'];
            $hireDate = $_POST['hireDate'];
            $status = $_POST['status'];
            
            // Validate inputs
            if (empty($firstName) || empty($lastName) || empty($email)) {
                $error = "First name, last name and email are required";
            } else {
                // Update users table
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET first_name = ?, last_name = ?, email = ?, phone = ? 
                    WHERE user_id = ?
                ");
                $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $_SESSION['user_id']);
                $stmt->execute();
                
                // Update drivers table
                $stmt = $conn->prepare("
                    UPDATE drivers 
                    SET license_number = ?, license_expiry = ?, hire_date = ?, status = ? 
                    WHERE user_id = ?
                ");
                $stmt->bind_param("ssssi", $licenseNumber, $licenseExpiry, $hireDate, $status, $_SESSION['user_id']);
                $stmt->execute();
                
                $success = "Profile updated successfully!";
                
                // Refresh driver details
                $stmt = $conn->prepare("
                    SELECT d.*, u.email, u.phone, u.first_name, u.last_name 
                    FROM drivers d 
                    JOIN users u ON d.user_id = u.user_id 
                    WHERE d.user_id = ?
                ");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $driverDetails = $stmt->get_result()->fetch_assoc();
            }
        }
        
        // Handle photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/img/drivers/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $fileExt = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $filename = 'driver-' . $_SESSION['user_id'] . '-' . uniqid() . '.' . $fileExt;
            $uploadPath = $uploadDir . $filename;
            
            // Check if file is an image
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['profile_photo']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                // Check file size (max 800KB)
                if ($_FILES['profile_photo']['size'] <= 800000) {
                    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
                        // Delete old photo if exists
                        if (!empty($driverDetails['photo_path']) && file_exists($driverDetails['photo_path'])) {
                            unlink($driverDetails['photo_path']);
                        }
                        
                        // Update photo path in database
                        $stmt = $conn->prepare("UPDATE drivers SET photo_path = ? WHERE user_id = ?");
                        $stmt->bind_param("si", $uploadPath, $_SESSION['user_id']);
                        $stmt->execute();
                        
                        $success = $success ? $success . " Photo uploaded successfully!" : "Photo uploaded successfully!";
                        $driverDetails['photo_path'] = $uploadPath;
                    } else {
                        $error = "Failed to upload photo. Please check directory permissions.";
                    }
                } else {
                    $error = "File size too large. Maximum allowed is 800KB.";
                }
            } else {
                $error = "Only JPG, PNG, and GIF files are allowed.";
            }
        }
    }
} catch (Exception $e) {
    error_log("Driver profile error: " . $e->getMessage());
    $error = "An error occurred while processing your request";
}

include 'includes/header.php';
?>

<!-- Layout wrapper -->
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
                        <span class="text-muted fw-light">Driver /</span> Profile
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
                                <h5 class="card-header">Driver Details</h5>
                                <!-- Driver Account -->
                                <div class="card-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                                            <img src="<?php 
                                                echo !empty($driverDetails['photo_path']) ? 
                                                htmlspecialchars($driverDetails['photo_path']) : 
                                                'assets/img/drivers/default-avatar.jpg'; 
                                            ?>" 
                                            alt="driver-avatar" class="d-block rounded" height="120" width="120" id="driverAvatar">
                                            <div class="button-wrapper">
                                                <label for="profilePhoto" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                    <span class="d-none d-sm-block">Upload new photo</span>
                                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                                    <input type="file" id="profilePhoto" name="profile_photo" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif">
                                                </label>
                                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4" onclick="resetPhoto()">
                                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Reset</span>
                                                </button>
                                                <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                                            </div>
                                        </div>
                                    
                                        <hr class="my-0">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="driverId" class="form-label">Driver ID</label>
                                                <input class="form-control" type="text" id="driverId" name="driverId" 
                                                       value="<?php echo isset($driverDetails['driver_id']) ? 'DRV-' . str_pad($driverDetails['driver_id'], 3, '0', STR_PAD_LEFT) : 'DRV-000'; ?>" readonly>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="hireDate" class="form-label">Hire Date</label>
                                                <input class="form-control" type="date" id="hireDate" name="hireDate" 
                                                       value="<?php echo $driverDetails['hire_date'] ?? ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="firstName" class="form-label">First Name</label>
                                                <input class="form-control" type="text" id="firstName" name="firstName" 
                                                       value="<?php echo htmlspecialchars($driverDetails['first_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="lastName" class="form-label">Last Name</label>
                                                <input class="form-control" type="text" name="lastName" id="lastName" 
                                                       value="<?php echo htmlspecialchars($driverDetails['last_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="email" class="form-label">Email</label>
                                                <input class="form-control" type="email" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($driverDetails['email'] ?? ''); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phone" name="phone" 
                                                       value="<?php echo htmlspecialchars($driverDetails['phone'] ?? ''); ?>">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="licenseNumber" class="form-label">License Number</label>
                                                <input type="text" class="form-control" id="licenseNumber" name="licenseNumber" 
                                                       value="<?php echo htmlspecialchars($driverDetails['license_number'] ?? ''); ?>">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="licenseExpiry" class="form-label">License Expiry</label>
                                                <input type="date" class="form-control" id="licenseExpiry" name="licenseExpiry" 
                                                       value="<?php echo $driverDetails['license_expiry'] ?? ''; ?>">
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="vehicleAssigned" class="form-label">Assigned Vehicle</label>
                                                <input type="text" class="form-control" id="vehicleAssigned" name="vehicleAssigned" 
                                                       value="<?php 
                                                           echo $vehicleDetails ? 
                                                           htmlspecialchars($vehicleDetails['make'] . ' ' . $vehicleDetails['model'] . ' (' . $vehicleDetails['registration_number'] . ')') : 
                                                           'Not assigned'; 
                                                       ?>" readonly>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Active" <?php echo ($driverDetails['status'] ?? '') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="On Leave" <?php echo ($driverDetails['status'] ?? '') === 'On Leave' ? 'selected' : ''; ?>>On Leave</option>
                                                    <option value="Suspended" <?php echo ($driverDetails['status'] ?? '') === 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                                                    <option value="Terminated" <?php echo ($driverDetails['status'] ?? '') === 'Terminated' ? 'selected' : ''; ?>>Terminated</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="submit" name="save_changes" class="btn btn-primary me-2">Save changes</button>
                                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /Driver Account -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <?php include 'includes/footer.php'; ?>                    
                <?php include 'includes/scripts.php'; ?>
                
                <script>
                // Handle photo preview when file is selected
                document.getElementById('profilePhoto').addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        const file = e.target.files[0];
                        const reader = new FileReader();
                        
                        reader.onload = function(event) {
                            document.getElementById('driverAvatar').src = event.target.result;
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
                
                // Reset photo to default
                function resetPhoto() {
                    if (confirm('Are you sure you want to reset your photo to default?')) {
                        fetch('reset_driver_photo.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({driver_id: <?php echo $driverDetails['driver_id'] ?? 0; ?>})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('driverAvatar').src = 'assets/img/drivers/default-avatar.jpg';
                                alert('Photo reset successfully');
                                location.reload();
                            } else {
                                alert('Error resetting photo: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error resetting photo');
                        });
                    }
                }
                </script>
            </div>
        </div>
    </div>
</div>
</body>
</html>