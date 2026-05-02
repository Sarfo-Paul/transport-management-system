<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid maintenance ID.");
}

$maintenance_id = (int)$_GET['id'];

// Fetch maintenance details
$query = "SELECT 
    vm.*, 
    v.make, 
    v.model, 
    v.registration_number
FROM vehicle_maintenance vm
JOIN vehicles v ON vm.vehicle_id = v.vehicle_id
WHERE vm.maintenance_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $maintenance_id);
$stmt->execute();
$result = $stmt->get_result();
$maintenance = $result->fetch_assoc();

if (!$maintenance) {
    die("Maintenance record not found.");
}
?>

<div class="row">
    <div class="col-md-6">
        <h5>Vehicle Information</h5>
        <p><strong>Make/Model:</strong> <?= htmlspecialchars($maintenance['make']) ?> <?= htmlspecialchars($maintenance['model']) ?></p>
        <p><strong>Registration:</strong> <?= htmlspecialchars($maintenance['registration_number']) ?></p>
        
        <h5 class="mt-4">Maintenance Details</h5>
        <p><strong>Type:</strong> <?= htmlspecialchars($maintenance['maintenance_type']) ?></p>
        <p><strong>Status:</strong> 
            <span class="badge badge-<?= 
                $maintenance['status'] == 'Completed' ? 'success' : 
                ($maintenance['status'] == 'In Progress' ? 'warning' : 
                ($maintenance['status'] == 'Cancelled' ? 'danger' : 'primary')) 
            ?>">
                <?= htmlspecialchars($maintenance['status']) ?>
            </span>
        </p>
        <p><strong>Scheduled Date:</strong> <?= date('M j, Y', strtotime($maintenance['maintenance_date'])) ?></p>
        <?php if ($maintenance['completion_date']): ?>
        <p><strong>Completion Date:</strong> <?= date('M j, Y', strtotime($maintenance['completion_date'])) ?></p>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <h5>Service Information</h5>
        <?php if ($maintenance['technician']): ?>
        <p><strong>Technician:</strong> <?= htmlspecialchars($maintenance['technician']) ?></p>
        <?php endif; ?>
        
        <?php if ($maintenance['cost']): ?>
        <p><strong>Cost:</strong> GHS <?= number_format($maintenance['cost'], 2) ?></p>
        <?php endif; ?>
        
        <?php if ($maintenance['description']): ?>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($maintenance['description'])) ?></p>
        <?php endif; ?>
        
        <?php if ($maintenance['notes']): ?>
        <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($maintenance['notes'])) ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
?>