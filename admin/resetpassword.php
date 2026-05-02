<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth_functions.php';

// Check if token is provided (for password reset form)
$token = isset($_GET['token']) ? $_GET['token'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        // Request password reset
        $email = trim($_POST['email']);
        $result = generateResetToken($email);
        
        if ($result['success']) {
            // Send password reset email
            if (sendPasswordResetEmail($result['email'], $result['token'])) {
                $_SESSION['reset_message'] = "Password reset link has been sent to your email.";
                header("Location: resetpassword.php");
                exit();
            } else {
                $_SESSION['reset_error'] = "Failed to send reset email. Please try again later.";
                header("Location: resetpassword.php");
                exit();
            }
        } else {
            $_SESSION['reset_error'] = $result['message'];
            header("Location: resetpassword.php");
            exit();
        }
    } elseif (isset($_POST['password']) && isset($_POST['confirm_password']) && $token) {
        // Process password reset
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);
        
        if ($password !== $confirmPassword) {
            $_SESSION['reset_error'] = "Passwords don't match";
            header("Location: resetpassword.php?token=$token");
            exit();
        } else {
            $result = resetPassword($token, $password);
            
            if ($result['success']) {
                $_SESSION['reset_success'] = "Password reset successfully! You can now login with your new password.";
                header('Location: login.php');
                exit();
            } else {
                $_SESSION['reset_error'] = $result['message'];
                header("Location: resetpassword.php?token=$token");
                exit();
            }
        }
    }
}

// Check for messages from session
$error = isset($_SESSION['reset_error']) ? $_SESSION['reset_error'] : null;
unset($_SESSION['reset_error']);

$success = isset($_SESSION['reset_message']) ? $_SESSION['reset_message'] : null;
unset($_SESSION['reset_message']);

$reset_success = isset($_SESSION['reset_success']) ? $_SESSION['reset_success'] : null;
unset($_SESSION['reset_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Transpass</title>
    <?php include 'includes/header.php'; ?>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card mt-5">
        <div class="card-body p-4">
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

          <?php if ($token): ?>
            <!-- Password Reset Form -->
            <div class="text-center mb-4">
              <h4 class="fw-bold">Reset Password 🔒</h4>
              <p class="text-muted">Your new password must be different from previously used passwords</p>
            </div>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
              <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <div class="input-group">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="Enter new password"
                    required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bx bx-hide"></i>
                  </button>
                </div>
              </div>
              
              <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                  <input
                    type="password"
                    id="confirm_password"
                    class="form-control"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    required>
                  <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                    <i class="bx bx-hide"></i>
                  </button>
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary w-100 mb-3">Set New Password</button>
            </form>
            
          <?php else: ?>
            <!-- Forgot Password Form -->
            <div class="text-center mb-4">
              <h4 class="fw-bold">Forgot Password? 🔒</h4>
              <p class="text-muted">Enter your email and we'll send you a password reset link</p>
            </div>

            <?php if (isset($error)): ?>
              <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
              <div class="alert alert-success mb-4"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if (isset($reset_success)): ?>
              <div class="alert alert-success mb-4"><?php echo htmlspecialchars($reset_success); ?></div>
            <?php endif; ?>

            <form method="POST">
              <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Enter your email"
                  required
                  autofocus>
              </div>
              
              <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>
            </form>
          <?php endif; ?>

          <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">
              <i class="bx bx-chevron-left me-1"></i>
              Back to login
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Toggle password visibility
  document.getElementById('togglePassword')?.addEventListener('click', function() {
    const password = document.getElementById('password');
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bx-show');
    this.querySelector('i').classList.toggle('bx-hide');
  });

  document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
    const confirmPassword = document.getElementById('confirm_password');
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bx-show');
    this.querySelector('i').classList.toggle('bx-hide');
  });
</script>

<?php include 'includes/scripts.php'; ?>
</body>
</html>