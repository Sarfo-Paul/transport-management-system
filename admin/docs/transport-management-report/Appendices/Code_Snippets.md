# Code Snippets for Transport Management System

## PHP Includes

### Header Include
```php
<?php include 'includes/header.php'; ?>
```

### Menu Include
```php
<?php include 'includes/menu.php'; ?>
```

### Navbar Include
```php
<?php include 'includes/navbar.php'; ?>
```

## Endpoints

### Get Notifications
```php
// get-notifications.php
$params = $_GET['scope']; // user or driver
// Fetch notifications based on scope
```

### Get Navbar Data
```php
// get-navbar-data.php
$notifications = getNotifications(); // Fetch notifications
$userData = getUserData(); // Fetch user data
```

## Data Access Patterns

### Prepared Statement Example
```php
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
```

## Status-to-Badge Mapping

### Vehicle Status Example
```php
$status = 'Active'; // Example status
$badgeClass = ($status == 'Active') ? 'success' : 'danger';
```

## Security Measures

### CSRF Protection
```php
// Form generation
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
```

### Session Regeneration
```php
session_regenerate_id(true); // Regenerate session ID on login
```

## Maps and Live Data

### Leaflet Map Initialization
```javascript
var map = L.map('map').setView([latitude, longitude], zoomLevel);
```

### Fetching Live Data
```javascript
setInterval(function() {
    fetch('/admin/get-schedules.php')
        .then(response => response.json())
        .then(data => updateMap(data));
}, 30000); // Refresh every 30 seconds
```

## Future Enhancements Code Snippet
```php
// Example for 2FA implementation
if (isset($_POST['two_factor_code'])) {
    // Verify TOTP code
}
```