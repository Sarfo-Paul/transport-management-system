<?php
require_once 'auth_functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(); // CSRF protection
    $identifier = trim($_POST['email-username']);
    $password = trim($_POST['password']);
    
    $result = loginUser($identifier, $password);
    
    if ($result['success']) {
        // Redirect based on user type
        switch ($_SESSION['user_type']) {
            case 'administrator':
                header('Location: index.php');
                break;
            case 'driver':
                header('Location: driver-dashboard.php');
                break;
            default:
                header('Location: user-dashboard.php');
        }
        exit();
    } else {
        $error = $result['message'];
    }
}

// Check for registration or verification messages
$registered = isset($_GET['registered']) && $_GET['registered'] == '1';
$verified = isset($_GET['verified']) && $_GET['verified'] == '1';
$resent = isset($_GET['resent']) && $_GET['resent'] == '1';
?>

<?php include 'includes/header.php'; ?>

<div class="container">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner" style="max-width: 400px; margin: 0 auto;">
      <div class="card px-sm-4 px-3 py-sm-4 py-3">
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
          <h4 class="mb-1 text-center">Welcome👋</h4>
          <p class="mb-4 text-center text-muted">Sign in to continue to your dashboard</p>

          <!-- Alerts -->
          <?php if (isset($error)): ?>
              <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <?php if ($registered): ?>
              <div class="alert alert-success mb-4">Registration successful! Please log in.</div>
          <?php endif; ?>
          <?php if ($verified): ?>
              <div class="alert alert-success mb-4">Email verified successfully! You can now log in.</div>
          <?php endif; ?>
          <?php if ($resent): ?>
              <div class="alert alert-success mb-4">Verification code resent. Please check your email.</div>
          <?php endif; ?>

          <!-- Login Form -->
          <form method="POST">
            <?php csrf_input(); ?>
            <div class="mb-3">
              <label for="email" class="form-label">Email or Username</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-user"></i></span>
                <input type="text" class="form-control" id="email" name="email-username" placeholder="Enter your email or username" required autofocus>
              </div>
            </div>

            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                <input type="password" id="password" class="form-control" name="password" placeholder="Enter your password" required>
                <span class="input-group-text cursor-pointer" id="togglePassword"><i class="bx bx-hide"></i></span>
              </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember-me">
                <label class="form-check-label" for="remember-me">Remember me</label>
              </div>
              <a href="resetpassword.php" class="text-decoration-none">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary d-grid w-100">Sign in</button>
          </form>

          <p class="text-center mt-4">
            <span>New on our platform?</span>
            <a href="register.php" class="ms-1">Create an account</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Toggle password visibility
  document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bx-show');
    this.querySelector('i').classList.toggle('bx-hide');
  });
</script>

<?php include 'includes/scripts.php'; ?>
