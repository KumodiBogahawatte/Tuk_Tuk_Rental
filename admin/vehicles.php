<?php
require_once 'admin_auth.php';
require_once '../config/db_connect.php';

if (isset($_POST['delete_vehicle'])) {
    $id = $_POST['vehicle_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
        $result = $stmt->execute([$id]);
        if ($result) {
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

try {
    $stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
    $vehicles = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "Error fetching vehicles: " . $e->getMessage();
    $vehicles = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management - TukTuk Admin</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a1f36;
            margin: 0;
        }
        .btn-primary {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
        .alert {
            border-radius: 8px;
            padding: 15px;
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
        .table img {
            border-radius: 6px;
        }
        .btn-sm {
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
        }
    </style>
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Vehicle Management</h2>
            <a href="../admin/add_vehicle.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add New Vehicle</a>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
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
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this vehicle? This action cannot be undone.');
}
</script>
</body>
</html>