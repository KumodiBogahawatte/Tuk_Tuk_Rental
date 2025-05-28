<?php
require_once '../config/db_connect.php';

// Get vehicle and booking info from query params
$vehicle_id = $_GET['id'] ?? null;
$pickup_location = $_GET['pickup_location'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? '';
$pickup_time = $_GET['pickup_time'] ?? '';
$return_location = $_GET['return_location'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$return_time = $_GET['return_time'] ?? '';

// Convert dates to Y-m-d for SQL
function parseDate($date) {
    $parts = explode('/', $date);
    if (count($parts) === 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return $date;
}
$pickup_date_sql = parseDate($pickup_date);
$return_date_sql = parseDate($return_date);

// Fetch vehicle details
$vehicle = null;
if ($vehicle_id) {
    $stmt = $pdo->prepare('SELECT * FROM vehicles WHERE id = ?');
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch();
}

// Handle reservation form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    if ($vehicle_id && $pickup_location && $pickup_date_sql && $pickup_time && $return_location && $return_date_sql && $return_time && $customer_name && $customer_email && $customer_phone) {
        // Check for overlapping reservation (double check)
        $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status IN ('pending','confirmed') AND (pickup_date <= ? AND return_date >= ?)");
        $check->execute([$vehicle_id, $return_date_sql, $pickup_date_sql]);
        if ($check->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO reservations (vehicle_id, pickup_location, pickup_date, pickup_time, return_location, return_date, return_time, customer_name, customer_email,customer_phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $insert->execute([$vehicle_id, $pickup_location, $pickup_date_sql, $pickup_time, $return_location, $return_date_sql, $return_time, $customer_name, $customer_email,$customer_phone]);
            $success = true;
        } else {
            $error = 'Sorry, this vehicle has just been booked for those dates.';
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="../assets/css/reservation.css">
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>
    <section class="container my-5">
      <div class="card shadow">
        <div class="card-header text-white" style="background-color:#375FE0;">
          <h4 class="mb-0">Your Reservation Details</h4>
        </div>
        <div class="card-body">
          <?php if ($success): ?>
            <div class="alert alert-success">Your reservation has been submitted! We will contact you soon.</div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <?php if ($vehicle && !$success): ?>
          <table class="table table-bordered">
            <tbody>
              <tr><th>Vehicle</th><td><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></td></tr>
              <tr><th>Pick-Up Location</th><td><?php echo htmlspecialchars($pickup_location); ?></td></tr>
              <tr><th>Rental Date</th><td><?php echo htmlspecialchars($pickup_date); ?></td></tr>
              <tr><th>Rental Time</th><td><?php echo htmlspecialchars($pickup_time); ?></td></tr>
              <tr><th>Return Location</th><td><?php echo htmlspecialchars($return_location); ?></td></tr>
              <tr><th>Return Date</th><td><?php echo htmlspecialchars($return_date); ?></td></tr>
              <tr><th>Return Time</th><td><?php echo htmlspecialchars($return_time); ?></td></tr>
            </tbody>
          </table>
          <form method="post">
            <div class="mb-3">
              <label for="customer_name" class="form-label">Your Name</label>
              <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="mb-3">
              <label for="customer_email" class="form-label">Your Email</label>
              <input type="email" class="form-control" id="customer_email" name="customer_email" required>
            </div>
            <div class="mb-3">
              <label for="customer_phone" class="form-label">Your Phone Number</label>
              <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <button class="btn btn-primary btn-confirm-booking" type="submit">Confirm Booking</button>
            </div>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </section>


    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- GSAP (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>

    <!-- Your JS File -->
    <script src="../assets/js/index.js"></script>
    <!-- AOS Library JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    flatpickr(".date-input", {
        dateFormat: "d/m/Y", // or "Y-m-d" etc.
    });
    </script>

  </body>
</html>