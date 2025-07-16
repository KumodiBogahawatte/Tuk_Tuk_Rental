<?php
// Start output buffering to prevent header errors
ob_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include files with proper error handling
try {
    require_once 'admin_auth.php';
    require_once '../config/db_connect.php';
} catch (Exception $e) {
    die("Error loading required files: " . $e->getMessage());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Get vehicle ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid vehicle ID");
}

// Fetch vehicle data
try {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();
    
    if (!$vehicle) {
        header('Location: vehicles.php');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file uploads
    $upload_dir = '../assets/images/vehicles/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            die("Failed to create upload directory");
        }
    }

    // Function to handle file upload
    function handleFileUpload($file, $upload_dir) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $filename = uniqid() . '_' . basename($file['name']);
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return 'assets/images/vehicles/' . $filename;
            }
        }
        return null;
    }

    // Initialize variables for images
    $main_image = $vehicle['main_image'];
    $image1 = $vehicle['image1'];
    $image2 = $vehicle['image2'];
    $image3 = $vehicle['image3'];

    // Handle file uploads
    try {
        if (!empty($_FILES['main_image']['name'])) {
            $new_main_image = handleFileUpload($_FILES['main_image'], $upload_dir);
            if ($new_main_image) $main_image = $new_main_image;
        }
        
        if (!empty($_FILES['image1']['name'])) {
            $new_image1 = handleFileUpload($_FILES['image1'], $upload_dir);
            if ($new_image1) $image1 = $new_image1;
        }
        
        if (!empty($_FILES['image2']['name'])) {
            $new_image2 = handleFileUpload($_FILES['image2'], $upload_dir);
            if ($new_image2) $image2 = $new_image2;
        }
        
        if (!empty($_FILES['image3']['name'])) {
            $new_image3 = handleFileUpload($_FILES['image3'], $upload_dir);
            if ($new_image3) $image3 = $new_image3;
        }

        // Update vehicle data
        $stmt = $pdo->prepare("UPDATE vehicles SET 
                brand = ?, 
                model = ?, 
                usd_price = ?, 
                gear_box = ?, 
                fuel_type = ?, 
                max_speed = ?, 
                capacity = ?, 
                fuel_tank = ?, 
                mileage = ?,
                main_image = ?,
                image1 = ?,
                image2 = ?,
                image3 = ?
                WHERE id = ?");
        
        $success = $stmt->execute([
            $_POST['brand'],
            $_POST['model'],
            $_POST['usd_price'],
            $_POST['gear_box'],
            $_POST['fuel_type'],
            $_POST['max_speed'],
            $_POST['capacity'],
            $_POST['fuel_tank'],
            $_POST['mileage'],
            $main_image,
            $image1,
            $image2,
            $image3,
            $id
        ]);

        if ($success) {
            // Clean output buffer and redirect
            ob_end_clean();
            header('Location: vehicles.php');
            exit();
        } else {
            throw new Exception("Failed to update vehicle");
        }
    } catch (Exception $e) {
        die("Error updating vehicle: " . $e->getMessage());
    }
}

// End output buffering and send content
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle - TukTuk Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <style>
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    <div class="container mt-4">
        <h2>Edit Vehicle</h2>
        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($vehicle['brand']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($vehicle['model']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="usd_price" class="form-label">Price per Day (USD)</label>
                        <input type="number" class="form-control" id="usd_price" name="usd_price" step="0.01" value="<?= $vehicle['usd_price'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gear_box" class="form-label">Gear Box</label>
                        <select class="form-select" id="gear_box" name="gear_box" required>
                            <option value="Manual" <?= $vehicle['gear_box'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="Automatic" <?= $vehicle['gear_box'] === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fuel_type" class="form-label">Fuel Type</label>
                        <select class="form-select" id="fuel_type" name="fuel_type" required>
                            <option value="Petrol" <?= $vehicle['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                            <option value="Diesel" <?= $vehicle['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                            <option value="Electric" <?= $vehicle['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Electric</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="max_speed" class="form-label">Max Speed (km/h)</label>
                        <input type="text" class="form-control" id="max_speed" name="max_speed" value="<?= htmlspecialchars($vehicle['max_speed']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Passenger Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" value="<?= $vehicle['capacity'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fuel_tank" class="form-label">Fuel Tank Capacity</label>
                        <input type="text" class="form-control" id="fuel_tank" name="fuel_tank" value="<?= htmlspecialchars($vehicle['fuel_tank']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mileage" class="form-label">Mileage</label>
                        <input type="text" class="form-control" id="mileage" name="mileage" value="<?= htmlspecialchars($vehicle['mileage']) ?>" required>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="main_image" class="form-label">Main Image</label>
                        <?php if ($vehicle['main_image']): ?>
                            <div class="mb-2">
                                <img src="../<?= htmlspecialchars($vehicle['main_image']) ?>" alt="Current main image" class="image-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image1" class="form-label">Additional Image 1</label>
                        <?php if ($vehicle['image1']): ?>
                            <div class="mb-2">
                                <img src="../<?= htmlspecialchars($vehicle['image1']) ?>" alt="Current image 1" class="image-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image1" name="image1" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="image2" class="form-label">Additional Image 2</label>
                        <?php if ($vehicle['image2']): ?>
                            <div class="mb-2">
                                <img src="../<?= htmlspecialchars($vehicle['image2']) ?>" alt="Current image 2" class="image-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image2" name="image2" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="image3" class="form-label">Additional Image 3</label>
                        <?php if ($vehicle['image3']): ?>
                            <div class="mb-2">
                                <img src="../<?= htmlspecialchars($vehicle['image3']) ?>" alt="Current image 3" class="image-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image3" name="image3" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
                <a href="vehicles.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple client-side image preview
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const img = this.closest('.mb-3').querySelector('img') || 
                                document.createElement('img');
                    img.className = 'image-preview';
                    img.src = URL.createObjectURL(this.files[0]);
                    
                    if (!this.closest('.mb-3').querySelector('img')) {
                        const div = document.createElement('div');
                        div.className = 'mb-2';
                        div.appendChild(img);
                        this.closest('.mb-3').insertBefore(div, this);
                    }
                }
            });
        });
    </script>
</body>
</html>