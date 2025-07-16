<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

// Handle vehicle deletion
if (isset($_POST['delete_vehicle'])) {
    $id = $_POST['vehicle_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            // Use JavaScript redirect as backup
            echo "<script>window.location.href = 'vehicles.php';</script>";
            header('Location: vehicles.php');
            exit();
        } else {
            $error_message = "Failed to delete vehicle.";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch all vehicles
try {
    $stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "Error fetching vehicles: " . $e->getMessage();
    $vehicles = [];
}
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
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Vehicle Management</h2>
        <a href="../admin/add_vehicle.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Vehicle
        </a>
    </div>
    
    <?php if (empty($vehicles)): ?>
        <div class="alert alert-info" role="alert">
            No vehicles found. <a href="../admin/add_vehicle.php">Add your first vehicle</a>.
        </div>
    <?php else: ?>
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
                        <td><?= htmlspecialchars($vehicle['id']); ?></td>
                        <td>
                            <?php if (!empty($vehicle['main_image'])): ?>
                                <img src="../<?= htmlspecialchars($vehicle['main_image']); ?>" 
                                     alt="Main Image" 
                                     style="max-width: 80px; max-height: 60px; object-fit: cover;"
                                     onerror="this.style.display='none';">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($vehicle['brand']); ?></td>
                        <td><?= htmlspecialchars($vehicle['model']); ?></td>
                        <td>USD <?= number_format($vehicle['usd_price'], 2); ?></td>
                        <td><?= htmlspecialchars($vehicle['gear_box']); ?></td>
                        <td><?= htmlspecialchars($vehicle['fuel_type']); ?></td>
                        <td><?= htmlspecialchars($vehicle['capacity']); ?></td>
                        <td>
                            <a href="edit_vehicle.php?id=<?= $vehicle['id']; ?>" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirmDelete();">
                                <input type="hidden" name="vehicle_id" value="<?= $vehicle['id']; ?>">
                                <button type="submit" name="delete_vehicle" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this vehicle? This action cannot be undone.');
}
</script>
</body>
</html>