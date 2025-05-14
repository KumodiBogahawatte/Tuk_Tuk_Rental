<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

// Handle status update
if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['confirm', 'cancel'])) {
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->execute([$_GET['action'] === 'confirm' ? 'confirmed' : 'cancelled', $_GET['id']]);
    header('Location: reservations.php');
    exit();
}

// Filter by status
$status = $_GET['status'] ?? '';
$where = '';
$params = [];
if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
    $where = 'WHERE r.status = ?';
    $params[] = $status;
}

$sql = "SELECT r.*, v.brand, v.model FROM reservations r
        JOIN vehicles v ON r.vehicle_id = v.id
        $where
        ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container">
    <h2 class="mb-4">Reservations</h2>
    <div class="mb-3">
        <a href="?status=" class="btn btn-outline-secondary btn-sm<?= $status==''?' active':'' ?>">All</a>
        <a href="?status=pending" class="btn btn-outline-warning btn-sm<?= $status=='pending'?' active':'' ?>">Pending</a>
        <a href="?status=confirmed" class="btn btn-outline-success btn-sm<?= $status=='confirmed'?' active':'' ?>">Confirmed</a>
        <a href="?status=cancelled" class="btn btn-outline-danger btn-sm<?= $status=='cancelled'?' active':'' ?>">Cancelled</a>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Pickup</th>
                <th>Return</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['id']) ?></td>
                <td><?= htmlspecialchars($r['brand'].' '.$r['model']) ?></td>
                <td><?= htmlspecialchars($r['customer_name']) ?></td>
                <td><?= htmlspecialchars($r['customer_email']) ?></td>
                <td><?= htmlspecialchars($r['customer_phone']) ?></td>
                <td>
                    <?= htmlspecialchars($r['pickup_location']) ?><br>
                    <?= htmlspecialchars($r['pickup_date']) ?> <?= htmlspecialchars($r['pickup_time']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($r['return_location']) ?><br>
                    <?= htmlspecialchars($r['return_date']) ?> <?= htmlspecialchars($r['return_time']) ?>
                </td>
                <td>
                    <?php if ($r['status'] == 'pending'): ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php elseif ($r['status'] == 'confirmed'): ?>
                        <span class="badge bg-success">Confirmed</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Cancelled</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($r['status'] == 'pending'): ?>
                        <a href="?action=confirm&id=<?= $r['id'] ?>" class="btn btn-success btn-sm">Confirm</a>
                        <a href="?action=cancel&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html> 