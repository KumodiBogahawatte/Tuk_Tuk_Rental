<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['confirm', 'cancel'])) {
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->execute([$_GET['action'] === 'confirm' ? 'confirmed' : 'cancelled', $_GET['id']]);
    header('Location: reservations.php');
    exit();
}

$status = $_GET['status'] ?? '';
$where = '';
$params = [];
if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
    $where = 'WHERE r.status = ?';
    $params[] = $status;
}

$sql = "SELECT r.*, v.brand, v.model FROM reservations r JOIN vehicles v ON r.vehicle_id = v.id $where ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reservations - TukTuk Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .container {
            max-width: 1400px;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #0d6efd;
        }
        .card-header {
            background: none;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1f36;
            margin: 0;
        }
        .btn-filter {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            margin-right: 5px;
        }
        .btn-filter.active {
            background-color: #0d6efd;
            color: #ffffff;
            border-color: #0d6efd;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 500;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
            color: #343a40;
        }
        .table .badge {
            font-size: 0.85rem;
            padding: 0.5em 1em;
        }
        .table .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }
        @media (max-width: 768px) {
            .card-header h2 {
                font-size: 1.25rem;
            }
            .table {
                font-size: 0.9rem;
            }
            .btn-filter {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Reservations</h2>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="?status=" class="btn btn-outline-secondary btn-filter btn-sm <?= $status==''?'active':'' ?>">All</a>
                <a href="?status=pending" class="btn btn-outline-warning btn-filter btn-sm <?= $status=='pending'?'active':'' ?>">Pending</a>
                <a href="?status=confirmed" class="btn btn-outline-success btn-filter btn-sm <?= $status=='confirmed'?'active':'' ?>">Confirmed</a>
                <a href="?status=cancelled" class="btn btn-outline-danger btn-filter btn-sm <?= $status=='cancelled'?'active':'' ?>">Cancelled</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
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
                            <td><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?></td>
                            <td><?= htmlspecialchars($r['customer_name']) ?></td>
                            <td><?= htmlspecialchars($r['customer_email']) ?></td>
                            <td><?= htmlspecialchars($r['customer_phone']) ?></td>
                            <td>
                                <?= htmlspecialchars($r['pickup_location']) ?><br>
                                <small><?= htmlspecialchars($r['pickup_date']) ?> <?= htmlspecialchars($r['pickup_time']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($r['return_location']) ?><br>
                                <small><?= htmlspecialchars($r['return_date']) ?> <?= htmlspecialchars($r['return_time']) ?></small>
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
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>