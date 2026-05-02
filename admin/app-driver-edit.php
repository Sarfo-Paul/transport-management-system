<?php
require_once 'config.php';

$driverId = $_GET['id'] ?? null;

if (!$driverId) {
    header('Location: app-driver-list.php');
    exit;
}

// Fetch driver data using MySQLi
$stmt = $conn->prepare("SELECT * FROM drivers WHERE driver_id = ?");
$stmt->bind_param("i", $driverId);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();
    
if (!$driver) {
    header('Location: app-driver-list.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle file upload if new photo is provided
        $photoPath = $driver['photo_path'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Delete old photo if exists
            if ($photoPath && file_exists($photoPath)) {
                unlink($photoPath);
            }
            
            $fileExt = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = 'driver_' . time() . '.' . $fileExt;
            $targetPath = 'uploads/' . $fileName; // Make sure this directory exists
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoPath = $targetPath;
            }
        }
        
        // Prepare variables for binding
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $licenseNumber = $_POST['license_number'];
        $licenseExpiry = $_POST['license_expiry'];
        $contactNumber = $_POST['contact_number'];
        $email = $_POST['email'] ?? null;
        $address = $_POST['address'] ?? null;
        $hireDate = $_POST['hire_date'];
        $status = $_POST['status'] ?? 'Active';
        
        // Update driver data using MySQLi
        $stmt = $conn->prepare("
            UPDATE drivers SET
                first_name = ?,
                last_name = ?,
                license_number = ?,
                license_expiry = ?,
                contact_number = ?,
                email = ?,
                address = ?,
                hire_date = ?,
                status = ?,
                photo_path = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE driver_id = ?
        ");
        
        // Bind parameters
        $stmt->bind_param(
            "ssssssssssi",
            $firstName,
            $lastName,
            $licenseNumber,
            $licenseExpiry,
            $contactNumber,
            $email,
            $address,
            $hireDate,
            $status,
            $photoPath,
            $driverId
        );
        
        $stmt->execute();
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Driver updated successfully'
        ]);
        exit;
    } catch (Exception $e) {
        // Return error response
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
        exit;
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
<div class="container-xxl">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edit Driver: <?= htmlspecialchars($driver['first_name']).' '.htmlspecialchars($driver['last_name']) ?></h4>
        </div>
        <div class="card-body">
            <form id="editDriverForm" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <?php if ($driver['photo_path']): ?>
                                <img src="<?= htmlspecialchars($driver['photo_path']) ?>" class="rounded-circle mb-3" width="150" height="150" id="driverPhoto">
                            <?php else: ?>
                                <div class="avatar mb-3">
                                    <span class="avatar-initial rounded-circle bg-secondary">
                                        <?= substr($driver['first_name'], 0, 1).substr($driver['last_name'], 0, 1) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="photo" id="photoInput">
                            <small class="text-muted">Upload new photo (optional)</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($driver['first_name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($driver['last_name']) ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" class="form-control" name="license_number" value="<?= htmlspecialchars($driver['license_number']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Expiry</label>
                                <input type="date" class="form-control" name="license_expiry" value="<?= htmlspecialchars($driver['license_expiry']) ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact_number" value="<?= htmlspecialchars($driver['contact_number']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($driver['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address"><?= htmlspecialchars($driver['address'] ?? '') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hire Date</label>
                                <input type="date" class="form-control" name="hire_date" value="<?= htmlspecialchars($driver['hire_date']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Active" <?= $driver['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="On Leave" <?= $driver['status'] === 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                                    <option value="Suspended" <?= $driver['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                    <option value="Terminated" <?= $driver['status'] === 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Driver</button>
                            <a href="app-driver-list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

                    
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
// Photo preview
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.getElementById('driverPhoto') || document.createElement('img');
            img.src = event.target.result;
            img.className = 'rounded-circle mb-3';
            img.width = 150;
            img.height = 150;
            img.id = 'driverPhoto';
            e.target.parentNode.insertBefore(img, e.target);
            
            // Remove avatar initials if they exist
            const avatar = document.querySelector('.avatar-initial');
            if (avatar) {
                avatar.parentNode.remove();
            }
        };
        reader.readAsDataURL(file);
    }
});

// Form submission
document.getElementById('editDriverForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Driver updated successfully');
            window.location.href = 'app-driver-list.php';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the driver');
    });
});
</script>
</body>
</html>