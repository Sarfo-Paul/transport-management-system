# Data Access Patterns in the Transport Management System

## Overview
This document outlines the data access patterns utilized in the Transport Management System (TMS) project, specifically focusing on how data is retrieved, manipulated, and displayed within the application. The patterns described herein are designed to ensure efficient data handling, maintainability, and security.

## Prepared Statements
The TMS employs prepared statements for all database interactions involving dynamic filters and identifiers. This approach not only enhances performance by allowing the database to optimize query execution but also significantly mitigates the risk of SQL injection attacks.

### Example
```php
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$results = $stmt->fetchAll();
```

## Common Query Patterns
The system follows a consistent pattern for list pages, which includes the following steps:

1. **Count Query**: A preliminary query is executed to count the total number of records that match the specified filters.
2. **Data Query**: A subsequent query retrieves the actual data, applying the same filters and implementing pagination to manage the volume of data displayed.

### Example
```php
// Count query
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status = :status");
$countStmt->execute(['status' => $status]);
$totalCount = $countStmt->fetchColumn();

// Data query with pagination
$dataStmt = $pdo->prepare("SELECT * FROM bookings WHERE status = :status LIMIT :offset, :limit");
$dataStmt->bindValue(':status', $status);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$dataStmt->execute();
$dataResults = $dataStmt->fetchAll();
```

## Data Caching
To improve performance, frequently accessed data is cached using an in-memory store. This reduces the number of database queries and speeds up data retrieval for users.

## Error Handling
Robust error handling mechanisms are implemented to manage database exceptions gracefully. This includes logging errors and providing user-friendly messages without exposing sensitive information.

### Example
```php
try {
    // Database operations
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "An error occurred while processing your request.";
}
```

## Conclusion
The data access patterns implemented in the Transport Management System are designed to ensure efficient, secure, and maintainable interactions with the database. By utilizing prepared statements, consistent query patterns, caching, and robust error handling, the system is well-equipped to handle the demands of a modern transport management application.