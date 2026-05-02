<?php
require_once 'config.php';
require 'vendor/autoload.php'; // Path to autoload.php from Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Generates a 6-digit random verification code
 * @return string 6-digit verification code
 */
function generateEmailVerificationCode() {
    try {
        // Generate a cryptographically secure random number
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    } catch (Exception $e) {
        // Fallback to less secure method if random_int fails
        return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

/**
 * Authenticates a user by email or username and sets session state.
 * Regenerates session ID on success to prevent fixation.
 */
function loginUser($identifier, $password) {
    global $conn;
    
    // Check if user exists by email or username
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    // Check if email is verified
    if (!$user['is_verified']) {
        return ['success' => false, 'message' => 'Please verify your email before logging in. <a href="resend_verification.php?email=' . urlencode($user['email']) . '">Resend verification email</a>'];
    }
    
    // Regenerate session ID on login to prevent fixation
    if (PHP_SESSION_ACTIVE === session_status()) {
        session_regenerate_id(true);
    }

    // Start session and store user data
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    
    return ['success' => true];
}

function sendVerificationEmail($email, $verification_code) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'junioratta64@gmail.com';
        $mail->Password   = 'kwye mxbw otfh kicj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        
        // Recipients
        $mail->setFrom('no-reply@transpass.com', 'Transpass System');
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification for Transpass';
        $mail->Body    = "
            <h2>Welcome to Transpass!</h2>
            <p>Thank you for registering. Please verify your email address by entering the following code:</p>
            <h3 style='color: #7367F0;'>$verification_code</h3>
            <p>This code will expire in 10 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        ";
        $mail->AltBody = "Your verification code is: $verification_code\n\nThis code will expire in 10 minutes.";
        
        $mail->send();
        error_log("Verification email sent to $email with code $verification_code");
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function registerUser($university_id, $first_name, $last_name, $email, $user_type, $phone, $password, $username = null, $additional_data = []) {
    global $conn;
    
    // Validate university ID for students and staff
    if (in_array($user_type, ['student', 'staff'])) {
        if (empty($university_id)) {
            return ['success' => false, 'message' => 'University ID is required for students and staff'];
        }
        if (!preg_match('/^\d{8,}$/', $university_id)) {
            return ['success' => false, 'message' => 'University ID must contain at least 8 digits'];
        }
    }
    
    // Validate password strength
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters long'];
    }
    if (!preg_match('/\d/', $password)) {
        return ['success' => false, 'message' => 'Password must contain at least one number'];
    }
    if (!preg_match('/[a-zA-Z]/', $password)) {
        return ['success' => false, 'message' => 'Password must contain at least one letter'];
    }
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        return ['success' => false, 'message' => 'Password must contain at least one special character'];
    }
    
    // First check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Check if university ID already exists (only if provided)
    if (!empty($university_id)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE university_id = ?");
        $stmt->bind_param("s", $university_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'University ID already exists'];
        }
    }
    
    // Generate verification code and expiry
    $verification_code = generateEmailVerificationCode();
    $verification_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Insert new user with verification details
        $stmt = $conn->prepare("INSERT INTO users 
            (university_id, first_name, last_name, email, user_type, phone, password, username, verification_code, verification_expiry, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        
        $stmt->bind_param("ssssssssss", 
            $university_id, 
            $first_name, 
            $last_name, 
            $email, 
            $user_type, 
            $phone, 
            $hashedPassword, 
            $username,
            $verification_code,
            $verification_expiry);
        
        if (!$stmt->execute()) {
            throw new Exception("User creation failed: " . $conn->error);
        }
        
        $user_id = $conn->insert_id;
        
        // Handle additional data based on user type
        if ($user_type === 'driver' && !empty($additional_data)) {
            // Insert into drivers table
            $stmt = $conn->prepare("INSERT INTO drivers 
                (user_id, first_name, last_name, license_number, license_expiry, contact_number, email, hire_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
            
            $stmt->bind_param("isssssss", 
                $user_id,
                $first_name, 
                $last_name, 
                $additional_data['license_number'], 
                $additional_data['license_expiry'], 
                $phone, 
                $email, 
                $additional_data['hire_date']);
            
            if (!$stmt->execute()) {
                throw new Exception("Driver creation failed: " . $conn->error);
            }
        } elseif ($user_type === 'administrator' && !empty($additional_data)) {
            // Update user with admin-specific data
            $stmt = $conn->prepare("UPDATE users SET department = ?, position = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", 
                $additional_data['department'], 
                $additional_data['position'], 
                $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Administrator update failed: " . $conn->error);
            }
        } elseif (in_array($user_type, ['student', 'staff']) && !empty($additional_data)) {
            // Update user with address for students/staff
            $stmt = $conn->prepare("UPDATE users SET address = ? WHERE user_id = ?");
            $stmt->bind_param("si", $additional_data['address'], $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Address update failed: " . $conn->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Send verification email
        if (sendVerificationEmail($email, $verification_code)) {
            // Store email in session for verification page
            $_SESSION['verify_email'] = $email;
            return ['success' => true, 'message' => 'Registration successful. Please check your email for verification.'];
        } else {
            // If email fails to send, we still consider it a success but log the issue
            error_log("Verification email failed to send for $email");
            return ['success' => true, 'message' => 'Registration successful but verification email failed to send. Please contact support.'];
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return ['success' => false, 'message' => 'Registration error: ' . $e->getMessage()];
    }
}
function verifyEmail($email, $code) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT user_id, verification_expiry FROM users WHERE email = ? AND verification_code = ? AND is_verified = 0");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid verification code or email'];
    }
    
    $user = $result->fetch_assoc();
    $now = date('Y-m-d H:i:s');
    
    if ($now > $user['verification_expiry']) {
        return ['success' => false, 'message' => 'Verification code has expired'];
    }
    
    // Mark as verified
    $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL, verification_expiry = NULL WHERE user_id = ?");
    $update->bind_param("i", $user['user_id']);
    
    if ($update->execute()) {
        return ['success' => true, 'message' => 'Email verified successfully'];
    } else {
        return ['success' => false, 'message' => 'Error updating verification status'];
    }
}

function resendVerificationEmail($email) {
    global $conn;
    
    // Check if user exists and is unverified
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND is_verified = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Email not found or already verified'];
    }
    
    // Generate a NEW verification code and expiry
    $verification_code = generateEmailVerificationCode();
    $new_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Update the database with new code
    $update = $conn->prepare("UPDATE users SET verification_code = ?, verification_expiry = ? WHERE email = ?");
    $update->bind_param("sss", $verification_code, $new_expiry, $email);
    
    if (!$update->execute()) {
        return ['success' => false, 'message' => 'Failed to update verification details'];
    }
    
    // Resend the email with new code
    if (sendVerificationEmail($email, $verification_code)) {
        return ['success' => true, 'message' => 'New verification code sent'];
    } else {
        return ['success' => false, 'message' => 'Failed to resend verification email'];
    }
}
    
/**
 * Generates a password reset token and stores it in the database
 */
function generateResetToken($email) {
    global $conn;
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Email not found'];
    }
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $user = $result->fetch_assoc();
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['user_id'], $token, $expires);
    
    if ($stmt->execute()) {
        return ['success' => true, 'token' => $token, 'email' => $email];
    } else {
        return ['success' => false, 'message' => 'Failed to generate reset token'];
    }
}

/**
 * Sends password reset email with the token link
 */
function sendPasswordResetEmail($email, $token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Turn off verbose debug output for production
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'junioratta64@gmail.com'; // your Gmail
        $mail->Password   = 'kwye mxbw otfh kicj';   // your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('no-reply@transpass.com', 'Transpass System');
        $mail->addAddress($email);

        // Reset link
        if (!defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost/transport_management/admin/'); // Change this if your folder changes
        }
        $resetLink = BASE_URL . "resetpassword.php?token=$token";

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "
            <h2>Password Reset Request</h2>
            <p>We received a request to reset your password. Click the button below to proceed:</p>
            <p style='text-align:center;'>
                <a href='$resetLink' style='background-color: #7367F0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
            </p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this, please ignore this email.</p>
        ";
        $mail->AltBody = "Password Reset Link: $resetLink\n\nThis link will expire in 1 hour.";

        $mail->send();
        error_log("Password reset email sent to $email with token $token");
        return true;

    } catch (Exception $e) {
        error_log("Password reset email error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Resets the user's password using a valid token
 */
function resetPassword($token, $newPassword) {
    global $conn;
    
    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Invalid or expired token'];
    }
    
    $reset = $result->fetch_assoc();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update user password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashedPassword, $reset['user_id']);
    
    if ($stmt->execute()) {
        // Delete the used token
        $conn->query("DELETE FROM password_resets WHERE token = '".$conn->real_escape_string($token)."'");
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Failed to reset password'];
    }
}