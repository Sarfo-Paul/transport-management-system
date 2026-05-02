<?php
require_once 'config.php';

// Initialize variables
$errors = [];
$success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (function_exists('csrf_verify')) { csrf_verify(); }
    // Validate and sanitize input
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $licenseNumber = $conn->real_escape_string($_POST['licenseNumber']);
    $licenseExpiry = $conn->real_escape_string($_POST['licenseExpiry']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : null;
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : null;
    $hireDate = $conn->real_escape_string($_POST['hireDate']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Handle file upload (optional)
    $photoPath = null;
    if (isset($_FILES['driverPhoto']) && $_FILES['driverPhoto']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/drivers/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = basename($_FILES["driverPhoto"]["name"]);
        $targetFile = $targetDir . uniqid() . '_' . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["driverPhoto"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (5MB max)
            if ($_FILES["driverPhoto"]["size"] > 5000000) {
                $errors[] = "Sorry, your file is too large. Maximum 5MB allowed.";
            } else {
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                } else {
                    if (move_uploaded_file($_FILES["driverPhoto"]["tmp_name"], $targetFile)) {
                        $photoPath = $targetFile;
                    } else {
                        $errors[] = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        } else {
            $errors[] = "File is not an image.";
        }
    }

    // Validate required fields and formats
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($licenseNumber)) $errors[] = "License number is required";
    if (empty($licenseExpiry)) $errors[] = "License expiry date is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($hireDate)) $errors[] = "Hire date is required";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email";
    }
    if (!empty($phone) && !preg_match('/^\d{7,15}$/', $phone)) {
        $errors[] = "Phone must be 7-15 digits";
    }
    // Dates YYYY-MM-DD
    foreach ([['License expiry', $licenseExpiry], ['Hire date', $hireDate]] as $pair) {
        [$label, $dateVal] = $pair;
        $d = DateTime::createFromFormat('Y-m-d', $dateVal);
        if (!($d && $d->format('Y-m-d') === $dateVal)) {
            $errors[] = "$label must be a valid date (YYYY-MM-DD)";
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $sql = "INSERT INTO drivers (
            first_name, last_name, license_number, license_expiry, 
            contact_number, email, address, hire_date, status, photo_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssss", 
            $firstName, 
            $lastName, 
            $licenseNumber,
            $licenseExpiry,
            $phone,
            $email,
            $address,
            $hireDate,
            $status,
            $photoPath
        );
        
        if ($stmt->execute()) {
            $success = true;
            
            // Redirect after successful submission
            header("Location: app-driver-list.php?success=1");
            exit();
        } else {
            $errors[] = "Error saving driver: " . $conn->error;
        }
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
            <h4 class="card-title">Add New Driver</h4>
        </div>

        <div class="card-body">
            <!-- Alerts -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    Driver added successfully!
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="addDriverForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <h5>Personal Information</h5>

                        <div class="mb-3">
                            <label for="driverPhoto" class="form-label">Driver Photo (optional)</label>
                            <input class="form-control" type="file" id="driverPhoto" name="driverPhoto" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control only-letters" id="firstName" name="firstName" required>
                        </div>

                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control only-letters" id="lastName" name="lastName" required>
                        </div>

                        <h5>Contact Information</h5>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control only-numbers" id="phone" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <h5>License Information</h5>

                        <div class="mb-3">
                            <label for="licenseNumber" class="form-label">License Number</label>
                            <input type="text" class="form-control only-alphanum uppercase" id="licenseNumber" name="licenseNumber" required>
                        </div>

                        <div class="mb-3">
                            <label for="licenseExpiry" class="form-label">License Expiry Date</label>
                            <input type="date" class="form-control" id="licenseExpiry" name="licenseExpiry" required>
                        </div>

                        <h5>Employment Details</h5>

                        <div class="mb-3">
                            <label for="hireDate" class="form-label">Hire Date</label>
                            <input type="date" class="form-control" id="hireDate" name="hireDate" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="" disabled selected>Select status</option>
                                <option value="Active">Active</option>
                                <option value="On Leave">On Leave</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Terminated">Terminated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Driver</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Preview driver image when selected
        document.getElementById('driverPhoto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('driverPreview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation
        document.getElementById('addDriverForm').addEventListener('submit', function(e) {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *');
            }
        });
    });
    </script>
</body>
</html>