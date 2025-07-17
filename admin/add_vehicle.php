<?php
ob_start(); // Start output buffering to prevent headers-already-sent issues
error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Display errors for debugging

require_once 'admin_auth.php';
require_once '../config/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['brand', 'model', 'usd_price', 'gear_box', 'fuel_type', 'max_speed', 'capacity', 'fuel_tank', 'mileage'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields.");
            }
        }

        $upload_dir = '../assets/images/vehicles/';
        // Ensure upload directory exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("Failed to create upload directory.");
            }
        }

        function handleFileUpload($file, $upload_dir) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $filename = uniqid() . '_' . basename($file['name']);
                $target_path = $upload_dir . $filename;
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    return 'assets/images/vehicles/' . $filename;
                } else {
                    throw new Exception("Failed to upload file: " . htmlspecialchars($file['name']));
                }
            }
            return '';
        }

        $main_image = '';
        $image1 = '';
        $image2 = '';
        $image3 = '';

        // Validate main image
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $main_image = handleFileUpload($_FILES['main_image'], $upload_dir);
            if (empty($main_image)) {
                throw new Exception("Main image upload failed.");
            }
        } else {
            throw new Exception("Main image is required.");
        }

        // Handle optional images
        if (isset($_FILES['image1']) && $_FILES['image1']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image1 = handleFileUpload($_FILES['image1'], $upload_dir);
        }
        if (isset($_FILES['image2']) && $_FILES['image2']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image2 = handleFileUpload($_FILES['image2'], $upload_dir);
        }
        if (isset($_FILES['image3']) && $_FILES['image3']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image3 = handleFileUpload($_FILES['image3'], $upload_dir);
        }

        // Prepare and execute the database query
        $stmt = $pdo->prepare("INSERT INTO vehicles (brand, model, usd_price, gear_box, fuel_type, max_speed, capacity, fuel_tank, mileage, main_image, image1, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $_POST['brand'],
            $_POST['model'],
            floatval($_POST['usd_price']),
            $_POST['gear_box'],
            $_POST['fuel_type'],
            $_POST['max_speed'],
            intval($_POST['capacity']),
            $_POST['fuel_tank'],
            $_POST['mileage'],
            $main_image,
            $image1,
            $image2,
            $image3
        ]);

        if ($result) {
            $success_message = "Vehicle added successfully!";
            ob_end_clean(); // Clean the output buffer
            header('Location: vehicles.php');
            exit();
        } else {
            throw new Exception("Failed to insert vehicle into database.");
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
ob_end_flush(); // Flush the output buffer
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
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .container {
            max-width: 1200px;
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
        .form-label {
            font-weight: 500;
            color: #6c757d;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-primary:hover, .btn-secondary:hover {
            filter: brightness(90%);
        }
        .image-preview {
            max-width: 150px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .alert {
            border-radius: 8px;
            padding: 15px;
        }
        @media (max-width: 768px) {
            .card-header h2 {
                font-size: 1.25rem;
            }
            .form-control, .form-select {
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
            <h2>Add New Vehicle</h2>
        </div>
        <div class="card-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
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
                            <label for="usd_price" class="form-label">Price per Day (USD)</label>
                            <input type="number" class="form-control" id="usd_price" name="usd_price" step="0.01" required>
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
                            <input type="file" class="form-control" id="main_image" name="main_image" required accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image1" class="form-label">Additional Image 1</label>
                            <input type="file" class="form-control" id="image1" name="image1" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="image2" class="form-label">Additional Image 2</label>
                            <input type="file" class="form-control" id="image2" name="image2" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="image3" class="form-label">Additional Image 3</label>
                            <input type="file" class="form-control" id="image3" name="image3" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Add Vehicle</button>
                    <a href="vehicles.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview uploaded images
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const img = this.closest('.mb-3').querySelector('img') || document.createElement('img');
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