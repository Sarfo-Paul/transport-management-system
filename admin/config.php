<?php
/**
 * Transport Management System - Core Configuration & Utilities
 *
 * Responsibilities
 * - Bootstraps database connection and session handling
 * - Provides common helpers (users, routes, schedules)
 * - Enforces role-based access via enforceRole()
 * - Exposes CSRF protection helpers
 */

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'kantanka');
define('DB_NAME', 'transport_management');
define('DB_PORT', 3307);  

// Create connection with port
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn->connect_error) {
    die("Database connection error. Please try again later. Error: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Secure session configuration
// Note: On HTTPS, cookie_secure should be true. We auto-detect for local.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
           (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

if (PHP_SESSION_ACTIVE !== session_status()) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// CSRF utilities
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Returns the current CSRF token string
 */
function csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * Echoes a hidden input containing the CSRF token for use in forms
 */
function csrf_input() {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verifies CSRF token on POST requests
 * Exits with 400 response on failure
 */
function csrf_verify() {
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') return;
    $sent = $_POST['csrf_token'] ?? '';
    $valid = hash_equals($_SESSION['csrf_token'] ?? '', $sent);
    if (!$valid) {
        http_response_code(400);
        echo 'Invalid CSRF token';
        exit();
    }
}


// Helper function to get user data
function getUserData($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Helper function to get user bookings
function getUserBookings($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT b.*, r.route_name 
                           FROM bookings b
                           JOIN routes r ON b.route_id = r.route_id
                           WHERE b.user_id = ?
                           ORDER BY b.travel_date DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result();
}

// In config.php
function getAvailableRoutes($search = null, $day = null, $time = null) {
    global $conn;
    
    $query = "SELECT * FROM routes WHERE status = 'Active'";
    $params = [];
    $types = "";
    
    if ($search) {
        $query .= " AND (route_name LIKE ? OR start_point LIKE ? OR end_point LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $types .= "ssss";
    }
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

function getRouteSchedules($routeId, $day = null, $time = null) {
    global $conn;
    
    $query = "SELECT rs.*, v.registration_number, v.capacity as max_capacity,
              CONCAT(d.first_name, ' ', d.last_name) AS driver_name
              FROM route_schedules rs
              LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
              LEFT JOIN drivers d ON rs.driver_id = d.driver_id
              WHERE rs.route_id = ?";
    $params = [$routeId];
    $types = "i";
    
    if ($day) {
        $query .= " AND rs.day_of_week = ?";
        $params[] = $day;
        $types .= "s";
    }
    
    if ($time) {
        switch ($time) {
            case 'morning':
                $query .= " AND HOUR(rs.departure_time) BETWEEN 5 AND 11";
                break;
            case 'afternoon':
                $query .= " AND HOUR(rs.departure_time) BETWEEN 12 AND 16";
                break;
            case 'evening':
                $query .= " AND HOUR(rs.departure_time) BETWEEN 17 AND 20";
                break;
            case 'night':
                $query .= " AND (HOUR(rs.departure_time) BETWEEN 21 AND 23 OR HOUR(rs.departure_time) BETWEEN 0 AND 4)";
                break;
        }
    }
    
    $query .= " ORDER BY rs.day_of_week, rs.departure_time";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Enforce that the current session user has one of the allowed roles.
 * If not authenticated, redirect to login. If role mismatch, redirect
 * the user to their correct dashboard based on their role.
 *
 * @param array $allowedRoles List of allowed role strings
 * @return void Exits via header redirect on failure
 */
function enforceRole(array $allowedRoles) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        header('Location: login.php');
        exit();
    }

    $role = $_SESSION['user_type'];
    if (!in_array($role, $allowedRoles, true)) {
        // Redirect to the appropriate dashboard for this role
        switch ($role) {
            case 'administrator':
                header('Location: index.php');
                break;
            case 'driver':
                header('Location: driver-dashboard.php');
                break;
            case 'student':
            case 'staff':
                header('Location: user-dashboard.php');
                break;
            default:
                // Fallback to login on unknown role
                header('Location: login.php');
                break;
        }
        exit();
    }
}
?>