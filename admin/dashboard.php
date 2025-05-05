<?php
session_start();
require_once '../config/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle vehicle deletion
if (isset($_POST['delete_vehicle'])) {
    $id = $_POST['vehicle_id'];
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard.php');
    exit();
}

// Fetch all vehicles
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TukTuk Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">TukTuk Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Vehicle Management</h2>
            <a href="add_vehicle.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Vehicle
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Price/Day</th>
                        <th>Gear Box</th>
                        <th>Fuel Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?php echo $vehicle['id']; ?></td>
                        <td><?php echo htmlspecialchars($vehicle['brand']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                        <td>LKR <?php echo number_format($vehicle['price_per_day'], 2); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['gear_box']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['fuel_type']); ?></td>
                        <td>
                            <a href="edit_vehicle.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                <button type="submit" name="delete_vehicle" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
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