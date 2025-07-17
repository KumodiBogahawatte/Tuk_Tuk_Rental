<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

$total_vehicles = $pdo->query('SELECT COUNT(*) FROM vehicles')->fetchColumn();
$total_reservations = $pdo->query('SELECT COUNT(*) FROM reservations')->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='confirmed'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='cancelled'")->fetchColumn();

// Fetch recent reservations for the table
$stmt = $pdo->query("SELECT r.*, v.brand, v.model FROM reservations r JOIN vehicles v ON r.vehicle_id = v.id ORDER BY r.created_at DESC LIMIT 5");
$recent_reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TukTuk Rental</title>
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
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .dashboard-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a1f36;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-left: 4px solid;
            margin-bottom: 20px;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        .stat-card i {
            font-size: 1.5rem;
            color: #6c757d;
        }
        .stat-card h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .stat-card .display-6 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1f36;
        }
        .stat-card.vehicles { border-left-color: #0d6efd; }
        .stat-card.reservations { border-left-color: #198754; }
        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.confirmed { border-left-color: #28a745; }
        .stat-card.cancelled { border-left-color: #dc3545; }
        .action-buttons .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        .recent-reservations {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .recent-reservations h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1f36;
            margin-bottom: 20px;
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
        }
        @media (max-width: 768px) {
            .stat-card .display-6 {
                font-size: 2rem;
            }
            .dashboard-header h1 {
                font-size: 1.5rem;
            }
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <div class="action-buttons">
            <a href="reservations.php" class="btn btn-primary me-2"><i class="fas fa-calendar-check me-2"></i>Manage Reservations</a>
            <a href="add_vehicle.php" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>Add Vehicle</a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card vehicles">
                <div class="d-flex align-items-center">
                    <i class="fas fa-car me-3"></i>
                    <div>
                        <h5>Total Vehicles</h5>
                        <p class="display-6"><?= $total_vehicles ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card reservations">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-alt me-3"></i>
                    <div>
                        <h5>Total Reservations</h5>
                        <p class="display-6"><?= $total_reservations ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card pending">
                <div class="d-flex align-items-center">
                    <i class="fas fa-hourglass-half me-3"></i>
                    <div>
                        <h5>Pending</h5>
                        <p class="display-6"><?= $pending ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card confirmed">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3"></i>
                    <div>
                        <h5>Confirmed</h5>
                        <p class="display-6"><?= $confirmed ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2-4">
            <div class="stat-card cancelled">
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle me-3"></i>
                    <div>
                        <h5>Cancelled</h5>
                        <p class="display-6"><?= $cancelled ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="recent-reservations">
        <h3>Recent Reservations</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vehicle</th>
                        <th>Customer</th>
                        <th>Pickup</th>
                        <th>Return</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_reservations as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['id']) ?></td>
                        <td><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?></td>
                        <td><?= htmlspecialchars($r['customer_name']) ?></td>
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
                                <a href="reservations.php?action=confirm&id=<?= $r['id'] ?>" class="btn btn-success btn-sm">Confirm</a>
                                <a href="reservations.php?action=cancel&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm">Cancel</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>