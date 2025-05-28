<?php
require_once 'admin_auth.php';
include '../admin/admin_nav.php';
require_once '../config/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file uploads
    $upload_dir = '../assets/images/vehicles/';
    $main_image = '';
    $image1 = '';
    $image2 = '';
    $image3 = '';

    // Function to handle file upload
    function handleFileUpload($file, $upload_dir) {
        if ($file['error'] === 0) {
            $filename = uniqid() . '_' . basename($file['name']);
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return 'assets/images/vehicles/' . $filename;
            }
        }
        return '';
    }

    // Handle main image
    if (isset($_FILES['main_image'])) {
        $main_image = handleFileUpload($_FILES['main_image'], $upload_dir);
    }

    // Handle additional images
    if (isset($_FILES['image1'])) {
        $image1 = handleFileUpload($_FILES['image1'], $upload_dir);
    }
    if (isset($_FILES['image2'])) {
        $image2 = handleFileUpload($_FILES['image2'], $upload_dir);
    }
    if (isset($_FILES['image3'])) {
        $image3 = handleFileUpload($_FILES['image3'], $upload_dir);
    }

    // Insert vehicle data
    $stmt = $pdo->prepare("INSERT INTO vehicles (brand, model, price_per_day, gear_box, fuel_type, max_speed, capacity, fuel_tank, mileage, main_image, image1, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_POST['brand'],
        $_POST['model'],
        $_POST['price_per_day'],
        $_POST['gear_box'],
        $_POST['fuel_type'],
        $_POST['max_speed'],
        $_POST['capacity'],
        $_POST['fuel_tank'],
        $_POST['mileage'],
        $main_image,
        $image1,
        $image2,
        $image3
    ]);

    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - TukTuk Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
    <div class="container mt-4">
        <h2>Add New Vehicle</h2>
        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" id="brand" name="brand" required>
                    </div>
                    <div class="mb-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" required>
                    </div>
                    <div class="mb-3">
                        <label for="price_per_day" class="form-label">Price per Day (LKR)</label>
                        <input type="number" class="form-control" id="price_per_day" name="price_per_day" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="gear_box" class="form-label">Gear Box</label>
                        <select class="form-select" id="gear_box" name="gear_box" required>
                            <option value="Manual">Manual</option>
                            <option value="Automatic">Automatic</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fuel_type" class="form-label">Fuel Type</label>
                        <select class="form-select" id="fuel_type" name="fuel_type" required>
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="max_speed" class="form-label">Max Speed (km/h)</label>
                        <input type="text" class="form-control" id="max_speed" name="max_speed" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Passenger Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="fuel_tank" class="form-label">Fuel Tank Capacity</label>
                        <input type="text" class="form-control" id="fuel_tank" name="fuel_tank" required>
                    </div>
                    <div class="mb-3">
                        <label for="mileage" class="form-label">Mileage</label>
                        <input type="text" class="form-control" id="mileage" name="mileage" required>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="main_image" class="form-label">Main Image</label>
                        <input type="file" class="form-control" id="main_image" name="main_image" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image1" class="form-label">Additional Image 1</label>
                        <input type="file" class="form-control" id="image1" name="image1">
                    </div>
                    <div class="mb-3">
                        <label for="image2" class="form-label">Additional Image 2</label>
                        <input type="file" class="form-control" id="image2" name="image2">
                    </div>
                    <div class="mb-3">
                        <label for="image3" class="form-label">Additional Image 3</label>
                        <input type="file" class="form-control" id="image3" name="image3">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Add Vehicle</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 