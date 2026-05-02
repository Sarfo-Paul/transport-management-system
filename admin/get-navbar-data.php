<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$data = [
    'notifications' => [],
    'user' => [
        'name' => $_SESSION['first_name'] ?? 'Admin',
        'role' => ucfirst($_SESSION['role'] ?? 'Administrator'),
        'avatar' => 'assets/img/avatars/1.png'
    ]
];

try {
    // Admin notifications: mix of latest user reports and driver reports
    $sql = "(
            SELECT 'User Report' AS type, title, status, report_date AS t
            FROM user_reports
            ORDER BY report_date DESC
            LIMIT 3
        ) UNION ALL (
            SELECT 'Driver Report' AS type, title, status, report_date AS t
            FROM driver_reports
            ORDER BY report_date DESC
            LIMIT 3
        ) ORDER BY t DESC LIMIT 5";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data['notifications'][] = [
                'title' => $row['type'] . ': ' . $row['title'],
                'message' => 'Status: ' . $row['status'],
                'time' => date('M d, H:i', strtotime($row['t']))
            ];
        }
    }
} catch (Exception $e) {
    // ignore
}

echo json_encode($data);



