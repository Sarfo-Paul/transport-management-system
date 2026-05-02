<?php
/**
 * Endpoint: GET /admin/get-schedules.php?route_id=ID&travel_date=YYYY-MM-DD
 * Returns route schedules with vehicle/driver info and live available seats.
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if (!isset($_GET['route_id'])) {
    echo json_encode([]);
    exit();
}

$routeId = (int)$_GET['route_id'];
$travelDate = $_GET['travel_date'] ?? null; // optional: YYYY-MM-DD

try {
    // Build query computing capacity and live available seats for the given date
    $sql = "SELECT 
                rs.schedule_id,
                rs.day_of_week,
                rs.departure_time,
                rs.arrival_time,
                rs.status,
                v.registration_number,
                v.capacity AS max_capacity,
                CONCAT(d.first_name, ' ', d.last_name) AS driver_name,
                GREATEST(v.capacity - COALESCE(b.booked_seats, 0), 0) AS available_seats
            FROM route_schedules rs
            LEFT JOIN vehicles v ON rs.vehicle_id = v.vehicle_id
            LEFT JOIN drivers d ON rs.driver_id = d.driver_id
            LEFT JOIN (
                SELECT schedule_id, COALESCE(SUM(passenger_count), 0) AS booked_seats
                FROM bookings
                WHERE status IN ('Pending','Confirmed')";

    $params = [];
    $types = '';

    if ($travelDate) {
        $sql .= " AND travel_date = ?";
        $params[] = $travelDate;
        $types .= 's';
    }

    $sql .= " GROUP BY schedule_id
            ) b ON b.schedule_id = rs.schedule_id
            WHERE rs.route_id = ? AND rs.status = 'Scheduled'
            ORDER BY rs.day_of_week, rs.departure_time";

    $params[] = $routeId;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load schedules', 'details' => $e->getMessage()]);
}
?>