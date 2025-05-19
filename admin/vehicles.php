<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

// Handle vehicle deletion
if (isset($_POST['delete_vehicle'])) {
    $id = $_POST['vehicle_id'];
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: vehicles.php');
    exit();
}

// Fetch all vehicles
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Vehicle Management - TukTuk Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Vehicle Management</h2>
        <a href="../admin/add_vehicle.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Vehicle
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Main Image</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Price/Day</th>
                    <th>Gear Box</th>
                    <th>Fuel Type</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td><?= $vehicle['id']; ?></td>
                    <td>
                        <?php if ($vehicle['main_image']): ?>
                            <img src="../<?= htmlspecialchars($vehicle['main_image']); ?>" alt="Main Image" style="max-width: 80px; max-height: 60px;">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($vehicle['brand']); ?></td>
                    <td><?= htmlspecialchars($vehicle['model']); ?></td>
                    <td>LKR <?= number_format($vehicle['price_per_day'], 2); ?></td>
                    <td><?= htmlspecialchars($vehicle['gear_box']); ?></td>
                    <td><?= htmlspecialchars($vehicle['fuel_type']); ?></td>
                    <td><?= htmlspecialchars($vehicle['capacity']); ?></td>
                    <td>
                        <a href="edit_vehicle.php?id=<?= $vehicle['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                            <input type="hidden" name="vehicle_id" value="<?= $vehicle['id']; ?>">
                            <button type="submit" name="delete_vehicle" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 