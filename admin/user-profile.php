<?php 
include 'includes/header.php';
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get user data
$user = getUserData($_SESSION['user_id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which form was submitted
    if (isset($_POST['update_profile'])) {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token validation failed.");
        }

        // Get form data
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        // Validate
        $errors = [];
        if ($firstName === '') $errors[] = 'First name is required';
        if ($lastName === '') $errors[] = 'Last name is required';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if ($phone === '' || !preg_match('/^\d{7,15}$/', $phone)) $errors[] = 'Phone must be 7-15 digits';
        
        if (empty($errors)) {
            // Update user in database
            $stmt = $conn->prepare("UPDATE users SET 
                                  first_name = ?, 
                                  last_name = ?, 
                                  email = ?, 
                                  phone = ?, 
                                  address = ? 
                                  WHERE user_id = ?");
            $stmt->bind_param("sssssi", $firstName, $lastName, $email, $phone, $address, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Profile updated successfully!";
                // Refresh user data
                $user = getUserData($_SESSION['user_id']);
                header("Location: user-profile.php");
                exit();
            } else {
                $error = "Error updating profile: " . $conn->error;
            }
        } else {
            $error = implode('<br>', array_map('htmlspecialchars', $errors));
        }
    }
}

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    // Check for upload errors
    if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/img/avatars/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['avatar']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            // Validate file size (800KB max)
            if ($_FILES['avatar']['size'] <= 819200) {
                $filename = 'avatar-' . $_SESSION['user_id'] . '-' . time() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                    // Update database with new photo path
                    $stmt = $conn->prepare("UPDATE users SET photo_path = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $filename, $_SESSION['user_id']);
                    
                    if ($stmt->execute()) {
                        $_SESSION['success'] = "Profile picture updated successfully!";
                        // Delete old photo if it exists and isn't the default
                        if (!empty($user['photo_path']) && $user['photo_path'] !== 'default-avatar.jpg') {
                            @unlink($uploadDir . $user['photo_path']);
                        }
                        header("Location: user-profile.php");
                        exit();
                    } else {
                        $error = "Error updating profile picture in database.";
                        @unlink($uploadPath); // Remove uploaded file if database update failed
                    }
                } else {
                    $error = "Error moving uploaded file.";
                }
            } else {
                $error = "File size too large. Maximum size is 800KB.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $error = "File upload error: " . $_FILES['avatar']['error'];
    }
}

// Display success message if set
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!-- Layout wrapper -->
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
                        <span class="text-muted fw-light">Account /</span> Profile
                    </h4>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <h5 class="card-header">Profile Details</h5>
                                <!-- User Account -->
                                <div class="card-body">
                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                        <img src="assets/img/avatars/<?php echo !empty($user['photo_path']) ? htmlspecialchars($user['photo_path']) : 'default-avatar.jpg'; ?>" 
                                             alt="user-avatar" class="d-block rounded" height="120" width="120" id="userAvatar">
                                        <div class="button-wrapper">
                                            <form id="avatarForm" method="post" enctype="multipart/form-data">
                                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                    <span class="d-none d-sm-block">Upload new photo</span>
                                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                                    <input type="file" id="upload" name="avatar" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif">
                                                </label>
                                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4" id="resetAvatar">
                                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                                    <span class="d-none d-sm-block">Reset</span>
                                                </button>
                                                <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-0">
                                <div class="card-body">
                                    <form id="formUserSettings" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="userId" class="form-label">User ID</label>
                                                <input class="form-control" type="text" id="userId" name="userId" 
                                                       value="<?php echo htmlspecialchars($user['user_id']); ?>" readonly>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="registrationDate" class="form-label">Registration Date</label>
                                                <input class="form-control" type="text" id="registrationDate" 
                                                       value="<?php echo date('Y-m-d', strtotime($user['created_at'])); ?>" readonly>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="firstName" class="form-label">First Name</label>
                                                <input class="form-control" type="text" id="firstName" name="firstName" 
                                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="lastName" class="form-label">Last Name</label>
                                                <input class="form-control" type="text" name="lastName" id="lastName" 
                                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="email" class="form-label">Email</label>
                                                <input class="form-control" type="email" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" id="phone" name="phone" 
                                                       value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <label for="address" class="form-label">Address</label>
                                                <textarea class="form-control" id="address" name="address" rows="2"><?php 
                                                    echo htmlspecialchars($user['address'] ?? ''); 
                                                ?></textarea>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="submit" name="update_profile" class="btn btn-primary me-2">Save changes</button>
                                            <button type="reset" class="btn btn-outline-secondary" id="cancelChanges">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /User Account -->
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
// Handle photo preview when file is selected
document.getElementById('upload').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(event) {
            document.getElementById('userAvatar').src = event.target.result;
            // Auto-submit the avatar form
            document.getElementById('avatarForm').submit();
        };
        
        reader.readAsDataURL(file);
    }
});

// Reset avatar to default
document.getElementById('resetAvatar').addEventListener('click', function() {
    if (confirm('Are you sure you want to reset your avatar to default?')) {
        fetch('reset-avatar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: JSON.stringify({user_id: <?php echo $_SESSION['user_id']; ?>})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('userAvatar').src = 'assets/img/avatars/default-avatar.jpg';
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = 'Avatar reset successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                document.querySelector('.container-xxl').prepend(alertDiv);
                // Auto-remove after 5 seconds
                setTimeout(() => alertDiv.remove(), 5000);
            } else {
                alert('Error resetting avatar: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error resetting avatar');
        });
    }
});

// Cancel button functionality
document.getElementById('cancelChanges').addEventListener('click', function() {
    // Reload the page to discard changes
    location.reload();
});
</script>
</body>
</html>