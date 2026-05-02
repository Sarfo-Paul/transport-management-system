<?php
require_once 'includes/header.php';
require_once 'auth_functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('csrf_verify')) { csrf_verify(); }
    $university_id = trim($_POST['university_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $user_type = trim($_POST['user_type']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $username = !empty($_POST['username']) ? trim($_POST['username']) : null;

    // Validation
    if (!isset($error) && empty($user_type)) {
        $error = 'User type is required';
    }
    if (!isset($error) && empty($first_name)) {
        $error = 'First name is required';
    }
    if (!isset($error) && empty($last_name)) {
        $error = 'Last name is required';
    }
    if (!isset($error) && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
        $error = 'Please enter a valid email';
    }
    if (!isset($error) && (empty($phone) || !preg_match('/^\d{7,15}$/', $phone))) {
        $error = 'Phone must be 7-15 digits';
    }
    if (in_array($user_type, ['student', 'staff'])) {
        if (empty($university_id)) $error = 'University ID is required';
        elseif (!preg_match('/^\d{8,}$/', $university_id)) $error = 'University ID must have at least 8 digits';
    }

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (!preg_match('/\d/', $password)) {
        $error = 'Password must contain a number';
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $error = 'Password must contain a letter';
    } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\'":\\|,.<>\/?]/', $password)) {
        $error = 'Password must contain a special character';
    }

    // Additional fields based on type
    $additional_data = [];
    if ($user_type === 'driver') {
        $additional_data = [
            'license_number' => trim($_POST['license_number']),
            'license_expiry' => trim($_POST['license_expiry']),
            'hire_date' => trim($_POST['hire_date'])
        ];
    } elseif ($user_type === 'administrator') {
        $additional_data = [
            'department' => trim($_POST['department']),
            'position' => trim($_POST['position'])
        ];
    } elseif (in_array($user_type, ['student', 'staff'])) {
        $additional_data = ['address' => trim($_POST['address'])];
    }

    if (!isset($error)) {
        ob_start();
        $result = registerUser($university_id, $first_name, $last_name, $email, $user_type, $phone, $password, $username, $additional_data);
        if ($result['success']) {
            $_SESSION['verify_email'] = $email;
            ob_end_clean();
            header('Location: verify.php?email=' . urlencode($email));
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container">
  <div class="authentication-wrapper container-p-y">
    <div class="authentication-inner" style="max-width:500px; margin:0 auto;">
      <div class="card p-4">
        <div class="card-body">
    <!-- Logo -->
          <div class="app-brand justify-content-center mb-4">
            <a href="lander.php" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                      <linearGradient id="TSgradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#7367F0;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#A66FFE;stop-opacity:1" />
                      </linearGradient>
                    </defs>
                    <rect fill="url(#TSgradient)" x="0" y="0" width="32" height="32" rx="6"></rect>
                    <text x="16" y="22" font-family="Arial, sans-serif" font-size="16" font-weight="bold" text-anchor="middle" fill="#FFFFFF">TS</text>
                  </svg>
                </span>
              </span>
              <span class="app-brand-text demo text-heading fw-bold">UG TransPass</span>
            </a>
          </div>

          <!-- Heading -->
          <h4 class="mb-1 text-center">Create your account 🚀</h4>
          <p class="text-center text-muted mb-4">Join UG TransPass to book, drive, or manage routes</p>

          <?php if (isset($error)): ?>
            <div class="alert alert-danger mb-3"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="POST" id="formAuthentication">
            <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
            <!-- User Type & University ID -->
            <div class="mb-3">
              <label for="user_type" class="form-label">User Type</label>
              <select id="user_type" name="user_type" class="form-select" required>
                <option value="">Select role</option>
                <option value="student">Student</option>
                <option value="staff">Staff</option>
                <option value="driver">Driver</option>
                <option value="administrator">Administrator</option>
              </select>
            </div>

            <div class="mb-3" id="university_id_field" style="display:none;">
              <label for="university_id" class="form-label">University ID</label>
              <input type="text" id="university_id" name="university_id" class="form-control only-numbers" placeholder="At least 8 digits">
            </div>

            <!-- Name -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control only-letters" required>
              </div>
              <div class="col-md-6">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control only-letters" required>
              </div>
            </div>

            <!-- Email & Phone -->
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" name="phone" id="phone" class="form-control only-numbers" required>
            </div>

            <!-- Username -->
            <div class="mb-3">
              <label for="username" class="form-label">Username (optional)</label>
              <input type="text" name="username" id="username" class="form-control">
            </div>

            <!-- Dynamic Fields -->
            <div id="driver-fields" class="dynamic-fields mb-3" style="display:none;">
              <label class="form-label">Driver Details</label>
              <div class="row">
                <div class="col-md-4"><input type="text" id="license_number" name="license_number" class="form-control only-alphanum uppercase" placeholder="License Number"></div>
                <div class="col-md-4"><input type="date" id="license_expiry" name="license_expiry" class="form-control"></div>
                <div class="col-md-4"><input type="date" id="hire_date" name="hire_date" class="form-control"></div>
              </div>
            </div>

            <div id="admin-fields" class="dynamic-fields mb-3" style="display:none;">
              <label class="form-label">Administrator Details</label>
              <div class="row">
                <div class="col-md-6"><input type="text" id="department" name="department" class="form-control" placeholder="Department"></div>
                <div class="col-md-6"><input type="text" id="position" name="position" class="form-control" placeholder="Position"></div>
              </div>
            </div>

            <div id="student-staff-fields" class="dynamic-fields mb-3" style="display:none;">
              <label for="address" class="form-label">Address</label>
              <textarea name="address" id="address" class="form-control" rows="2"></textarea>
            </div>

         
    <!-- Password -->
    <div class="mb-3 form-password-toggle">
      <label for="password" class="form-label">Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password" class="form-control" placeholder="Strong password" required>
        <span class="input-group-text cursor-pointer" id="toggleRegPassword"><i class="bx bx-hide"></i></span>
      </div>
      <small class="form-text text-muted">At least 6 characters, include letters, numbers & special chars</small>
    </div>

    <!-- Confirm Password -->
    <div class="mb-3 form-password-toggle">
      <label for="confirm_password" class="form-label">Confirm Password</label>
      <div class="input-group">
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter password" required>
        <span class="input-group-text cursor-pointer" id="toggleConfirmPassword"><i class="bx bx-hide"></i></span>
      </div>
    </div>

            <button type="submit" class="btn btn-primary d-grid w-100">Sign Up</button>
          </form>

          <p class="text-center mt-3">
            Already have an account? <a href="login.php">Sign in</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const userType = document.getElementById('user_type');
  const driverFields = document.getElementById('driver-fields');
  const adminFields = document.getElementById('admin-fields');
  const studentFields = document.getElementById('student-staff-fields');
  const uniFieldContainer = document.getElementById('university_id_field');
  const uniField = document.getElementById('university_id');

  function toggleDynamicFields() {
    const type = userType.value;
    driverFields.style.display = type==='driver'?'block':'none';
    adminFields.style.display = type==='administrator'?'block':'none';
    studentFields.style.display = (type==='student'||type==='staff')?'block':'none';
    uniFieldContainer.style.display = (type==='student'||type==='staff')?'block':'none';
    uniField.required = (type==='student'||type==='staff');
  }
  userType.addEventListener('change', toggleDynamicFields);
  toggleDynamicFields();

  // Password toggle
  const togglePwd = document.getElementById('toggleRegPassword');
  togglePwd.addEventListener('click', () => {
    const pwd = document.getElementById('password');
    const type = pwd.type === 'password' ? 'text' : 'password';
    pwd.type = type;
    togglePwd.querySelector('i').classList.toggle('bx-show');
    togglePwd.querySelector('i').classList.toggle('bx-hide');
  });
});
</script>
