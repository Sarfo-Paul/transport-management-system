<?php
require_once 'auth_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['verify_email'] ?? null;
    $code = trim($_POST['verification_code']);
    
    if ($email) {
        $result = verifyEmail($email, $code);
        
        if ($result['success']) {
            // Clear the session variable
            unset($_SESSION['verify_email']);
            header('Location: login.php?verified=1');
            exit();
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Verification session expired. Please register again.';
    }
}

// If coming from registration, store email in session
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email'])) {
    $_SESSION['verify_email'] = $_GET['email'];
    $email = $_GET['email'];
} elseif (isset($_SESSION['verify_email'])) {
    $email = $_SESSION['verify_email'];
} else {
    header('Location: register.php');
    exit();
}

// Check for resend success/error messages
if (isset($_GET['resent']) && $_GET['resent'] == '1') {
    $success = 'Verification code resent successfully!';
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<?php if (isset($success)): ?>
    <div class="alert alert-success mb-4">
        <i class="bx bx-check-circle me-2"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<!doctype html>
<html
  lang="en"
  class="layout-wide customizer-hide"
  dir="ltr"
  data-skin="default"
  data-assets-path="assets/"
  data-template="vertical-menu-template"
  data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Email Verification | Transpass</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Verification Card -->
          <div class="card px-sm-6 px-0">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <span class="text-primary">
                      <svg
                        width="32"
                        height="32"
                        viewBox="0 0 32 32"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                          <linearGradient id="TSgradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#7367F0;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#A66FFE;stop-opacity:1" />
                          </linearGradient>
                        </defs>
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect fill="url(#TSgradient)" x="0" y="0" width="32" height="32" rx="6"></rect>
                          <text x="16" y="22" font-family="Arial, sans-serif" font-size="16" font-weight="bold" text-anchor="middle" fill="#FFFFFF">TS</text>
                        </g>
                      </svg>
                    </span>
                  </span>
                  <span class="app-brand-text demo text-heading fw-bold" style="letter-spacing: 1px;">TRANSPASS</span>
                </a>
              </div>
              <!-- /Logo -->
              
              <h4 class="mb-2">Verify Your Email</h4>
              <p class="mb-4">We've sent a verification code to <span class="fw-bold"><?php echo htmlspecialchars($email); ?></span></p>
              
              <?php if (isset($error)): ?>
                <div class="alert alert-danger mb-4">
                  <i class="bx bx-error-circle me-2"></i>
                  <?php echo htmlspecialchars($error); ?>
                </div>
              <?php endif; ?>
              
              <form id="formVerification" method="POST" class="mb-3">
                <div class="mb-3">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                    <input 
                      type="text" 
                      id="verification_code" 
                      class="form-control" 
                      name="verification_code" 
                      placeholder="Enter 6-digit code" 
                      maxlength="6"
                      required
                      autofocus />
                  </div>
                </div>
                
                <button class="btn btn-primary d-grid w-100 mb-3">
                  Verify Email
                </button>
              </form>
              
              <div class="text-center">
                <span>Didn't receive the code?</span>
                <a href="resend_verification.php?email=<?php echo urlencode($email); ?>">
                  <span>Resend</span>
                </a>
              </div>
            </div>
          </div>
          <!-- /Verification Card -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <?php include 'includes/scripts.php'; ?>
  </body>
</html>