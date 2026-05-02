<?php
/**
 * Endpoint: GET /admin/get-all-routes.php
 * Returns all active routes with ordered coordinates for map rendering.
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    // Fetch active routes
    $routesStmt = $conn->prepare("SELECT route_id, route_name, start_point, end_point, distance, estimated_duration, description FROM routes WHERE status='Active'");
    $routesStmt->execute();
    $routesResult = $routesStmt->get_result();
    $routes = [];
    while ($row = $routesResult->fetch_assoc()) {
        $routes[(int)$row['route_id']] = $row;
        $routes[(int)$row['route_id']]['coordinates'] = [];
    }
    $routesStmt->close();

    if (empty($routes)) {
        echo json_encode([]);
        exit;
    }

    $routeIds = array_keys($routes);
    $placeholders = implode(',', array_fill(0, count($routeIds), '?'));
    $types = str_repeat('i', count($routeIds));

    // Fetch coordinates for all routes in one query
    $coordsSql = "SELECT route_id, latitude, longitude, sequence_order, landmark
                  FROM route_coordinates
                  WHERE route_id IN ($placeholders)
                  ORDER BY route_id, sequence_order";
    $coordsStmt = $conn->prepare($coordsSql);
    $coordsStmt->bind_param($types, ...$routeIds);
    $coordsStmt->execute();
    $coordsRes = $coordsStmt->get_result();
    while ($c = $coordsRes->fetch_assoc()) {
        $rid = (int)$c['route_id'];
        if (!isset($routes[$rid])) continue;
        $lat = (float)$c['latitude'];
        $lng = (float)$c['longitude'];
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) continue;
        $routes[$rid]['coordinates'][] = [
            'lat' => $lat,
            'lng' => $lng,
            'order' => (int)$c['sequence_order'],
            'landmark' => $c['landmark'] ?? null
        ];
    }
    $coordsStmt->close();

    // Re-index as numeric array
    echo json_encode(array_values($routes));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load routes', 'details' => $e->getMessage()]);
}
?>

