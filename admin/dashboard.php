<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

$total_vehicles = $pdo->query('SELECT COUNT(*) FROM vehicles')->fetchColumn();
$total_reservations = $pdo->query('SELECT COUNT(*) FROM reservations')->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='confirmed'")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='cancelled'")->fetchColumn();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    <div class="row mb-4">
      <div class="col"><div class="card text-center"><div class="card-body"><h5>Total Vehicles</h5><p class="display-6"><?= $total_vehicles ?></p></div></div></div>
      <div class="col"><div class="card text-center"><div class="card-body"><h5>Total Reservations</h5><p class="display-6"><?= $total_reservations ?></p></div></div></div>
      <div class="col"><div class="card text-center"><div class="card-body"><h5>Pending</h5><p class="display-6 text-warning"><?= $pending ?></p></div></div></div>
      <div class="col"><div class="card text-center"><div class="card-body"><h5>Confirmed</h5><p class="display-6 text-success"><?= $confirmed ?></p></div></div></div>
      <div class="col"><div class="card text-center"><div class="card-body"><h5>Cancelled</h5><p class="display-6 text-danger"><?= $cancelled ?></p></div></div></div>
    </div>
    <div class="mb-4">
      <a href="reservations.php" class="btn btn-primary me-2">Manage Reservations</a>
      <a href="add_vehicle.php" class="btn btn-secondary">Add Vehicle</a>
    </div>
    <!-- You can add more dashboard content here -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 