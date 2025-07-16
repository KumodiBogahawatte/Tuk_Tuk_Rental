<?php
session_start();
require_once '../config/db_connect.php';
include_once '../includes/social_icons.php';
require_once '../service/currencyService.php';
$currencyService = new CurrencyService($pdo);
$usdRate = $currencyService->getExchangeRate('LKR', 'USD');
$depositLKR = 5000; // Fixed deposit in LKR

// Get vehicle and booking info from query params
$vehicle_id = $_GET['id'] ?? null;
$pickup_location = $_GET['pickup_location'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? '';
$pickup_time = $_GET['pickup_time'] ?? '';
$return_location = $_GET['return_location'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$return_time = $_GET['return_time'] ?? '';
$image_url = $_GET['image_url'] ?? '';

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

// Fetch location prices
$pickup_location_price = 0;
$return_location_price = 0;

function cleanLocation($location) {
    // Remove anything after the first " ("
    return preg_replace('/\s+\(.*$/', '', $location);
}

$pickup_location = cleanLocation($pickup_location);
$return_location = cleanLocation($return_location);

if ($pickup_location) {
    $stmt = $pdo->prepare("SELECT price FROM locations WHERE name = ?");
    $stmt->execute([$pickup_location]);
    $row = $stmt->fetch();
    if ($row) $pickup_location_price = $row['price'];
}
if ($return_location) {
    $stmt = $pdo->prepare("SELECT price FROM locations WHERE name = ?");
    $stmt->execute([$return_location]);
    $row = $stmt->fetch();
    if ($row) $return_location_price = $row['price'];
}

$location_fee = $pickup_location_price + $return_location_price;

//calculate rental duration
$rental_days = 0;
$total_price = 0;
if($vehicle && $pickup_date_sql && $return_date_sql) {
    $pickup_date_obj = new DateTime($pickup_date_sql);
    $return_date_obj = new DateTime($return_date_sql);
    $interval = $pickup_date_obj->diff($return_date_obj);
    $rental_days = $interval->days + 1; // +1 to include both start and end day
    $total_price = $vehicle['price_per_day'] * $rental_days;
}
$subtotal = $total_price + $location_fee;
// Calculate total price with deposit and location fees
$total_with_fees = $subtotal + $depositLKR;

//Process form submission
$current_step = 1; //1=reservation, 2=payment, 3=confirmation
$success = false;
$error = '';
$reservation_id = null;

//Step1: Reservation details submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 1) {
  $customer_name = $_POST['customer_name'] ?? '';
  $customer_email = $_POST['customer_email'] ?? '';
  $customer_phone = $_POST['customer_phone'] ?? '';

  if($vehicle_id && $pickup_location &&$pickup_date_sql && $pickup_time && $return_location && $return_date_sql && $return_time && $customer_name && $customer_email && $customer_phone) {
    // Check for overlapping reservation
    $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status IN ('pending','confirmed') AND (pickup_date <= ? AND return_date >= ?)");
    $check->execute([$vehicle_id, $return_date_sql, $pickup_date_sql]);
    if($check->fetchColumn() == 0) {
    // No overlapping reservation, move to payment step
    $current_step = 2;
    // Store all reservation details in session or hidden fields for Step 2
    $_SESSION['reservation_data'] = [
        'vehicle_id' => $vehicle_id,
        'pickup_location' => $pickup_location,
        'pickup_date_sql' => $pickup_date_sql,
        'pickup_time' => $pickup_time,
        'return_location' => $return_location,
        'return_date_sql' => $return_date_sql,
        'return_time' => $return_time,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'total_price' => $total_price
    ];
    } else {
        // Overlapping reservation
        $error = 'Sorry, this vehicle has just been booked for those dates.Please choose different dates.';
    }
  } else {
    // Missing required fields
    $error = 'Please fill in all required fields.';
  }
}

// Step 2: Payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 2) {
    $card_name = $_POST['card_name'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $card_expiry = $_POST['card_expiry'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';

    if ($card_name && $card_number && $card_expiry && $card_cvv && isset($_SESSION['reservation_data'])) {
        $data = $_SESSION['reservation_data'];
        // Double-check for overlap before inserting
        $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status = 'confirmed' AND (pickup_date <= ? AND return_date >= ?)");
        $check->execute([$data['vehicle_id'], $data['return_date_sql'], $data['pickup_date_sql']]);
        if($check->fetchColumn() == 0) {
            // Insert reservation now, with status 'confirmed'
            $insert = $pdo->prepare("INSERT INTO reservations (vehicle_id, pickup_location, pickup_date, pickup_time, return_location, return_date, return_time, customer_name, customer_email, customer_phone, status, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?)");
            $insert->execute([
                $data['vehicle_id'],
                $data['pickup_location'],
                $data['pickup_date_sql'],
                $data['pickup_time'],
                $data['return_location'],
                $data['return_date_sql'],
                $data['return_time'],
                $data['customer_name'],
                $data['customer_email'],
                $data['customer_phone'],
                $data['total_price']
            ]);
            $reservation_id = $pdo->lastInsertId();
            $current_step = 3;
            $success = true;
            unset($_SESSION['reservation_data']);
        } else {
            $error = 'Sorry, this vehicle has just been booked for those dates. Please choose different dates.';
        }
    } else {
        $error = 'Please fill in all payment details.';
    }
}

// Only show error if this is a POST request and step 1 failed
$showError = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 1 && $error) {
    $showError = true;
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
    <link rel="stylesheet" href="../assets/css/reservation.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <?php getSocialIconsStyles(); ?>
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>

    <header class="reservation-header text-center">
        <div class="reservation-header-content">
          <div class="container">
            <h1 class="display-5 fw-bold">Complete Your Reservation</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center custom-breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="vehicles.php">Vehicles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservation Details</li>
                </ol>
            </nav>
          </div>
        </div>
    </header>
    
    <section class="container mb-5">
      <!-- Step Indicator -->
      <div class="step-indicator">
        <div class="step <?php echo $current_step >= 1 ? 'completed' : ($current_step == 1 ? 'active' : ''); ?>">
          <span class="step-number">1</span>
          <span class="step-label">Reservation Details</span>
          <span class="step-connector"></span>
        </div>
        <div class="step <?php echo $current_step >= 2 ? 'completed' : ($current_step == 2 ? 'active' : ''); ?>">
          <span class="step-number">2</span>
          <span class="step-label">Payment</span>
          <span class="step-connector"></span>
        </div>
        <div class="step <?php echo $current_step >= 3 ? 'completed' : ($current_step == 3 ? 'active' : ''); ?>">
          <span class="step-number">3</span>
          <span class="step-label">Confirmation</span>
          <span class="step-connector"></span>
        </div>
      </div>
      
      <?php if ($showError): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <!-- Step 1: Reservation Details -->
      <?php if ($current_step == 1 && $vehicle): ?>
      <div class="row">
        <div class="col-lg-8">
          <div class="reservation-card card">
            <div class="card-header">
              <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i> Reservation Details</h4>
            </div>
            <div class="card-body p-4">
              <div class="vehicle-image-container">
                <img src="../<?php echo htmlspecialchars($vehicle['main_image'] ?? 'assets/images/default-vehicle.jpg'); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="vehicle-image">
              </div>
              
              <h5><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></h5>
              
              <div class="detail-row">
                <div class="detail-label"><i class="fas fa-map-marker-alt me-2"></i>Pick-Up Location</div>
                <div class="detail-value"><?php echo htmlspecialchars($pickup_location); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label"><i class="far fa-calendar-alt me-2"></i>Pick-Up Date</div>
                <div class="detail-value"><?php echo htmlspecialchars($pickup_date); ?> at <?php echo htmlspecialchars($pickup_time); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label"><i class="fas fa-map-marker-alt me-2"></i>Return Location</div>
                <div class="detail-value"><?php echo htmlspecialchars($return_location); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label"><i class="far fa-calendar-alt me-2"></i>Return Date</div>
                <div class="detail-value"><?php echo htmlspecialchars($return_date); ?> at <?php echo htmlspecialchars($return_time); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label"><i class="fas fa-clock me-2"></i>Rental Duration</div>
                <div class="detail-value"><?php echo $rental_days; ?> day<?php echo $rental_days != 1 ? 's' : ''; ?></div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4">
          <div class="reservation-card card">
            <div class="card-header">
              <h4 class="mb-0"><i class="fas fa-receipt me-2"></i> Pricing Summary</h4>
            </div>
            <div class="card-body p-4">
              <div class="detail-row">
                <div class="detail-label">Daily Rate</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $vehicle['price_per_day']; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">LKR</span>
                    <span class="amount"><?php echo number_format($vehicle['price_per_day'], 2); ?></span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Rental Days</div>
                <div class="detail-value"><?php echo $rental_days; ?></div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Location Fee</div>
              <div class="detail-value vehicle-price"
                  data-price="<?php echo $location_fee; ?>"
                  data-rate="<?php echo $usdRate; ?>">
                  <span class="currency">LKR</span>
                  <span class="amount"><?php echo number_format($location_fee, 2); ?></span>
              </div>
            </div>
            <div class="detail-row">
                <div class="detail-label fw-bold">Subtotal</div>
                <div class="detail-value fw-bold vehicle-price"
                    data-price="<?php echo $subtotal; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">LKR</span>
                    <span class="amount"><?php echo number_format($subtotal, 2); ?></span>
                </div>
            </div>
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label">Deposit</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $depositLKR; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">LKR</span>
                    <span class="amount"><?php echo number_format($depositLKR, 2); ?></span>
                </div>
            </div>
            <hr>
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label fw-bold">Total Amount</div>
                <div class="detail-value fw-bold vehicle-price" style="color:rgb(255, 0, 0);"
                    data-price="<?php echo $total_with_fees; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">LKR</span>
                    <span class="amount"><?php echo number_format($total_with_fees, 2); ?></span>
                </div>
            </div>
            </div>
          </div>
          
          <div class="reservation-card card mt-4">
            <div class="card-header">
              <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i> Your Information</h4>
            </div>
            <div class="card-body p-4">
              <form method="post">
                <input type="hidden" name="step" value="1">
                <div class="mb-3">
                  <label for="customer_name" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
                <div class="mb-3">
                  <label for="customer_email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                </div>
                <div class="mb-3">
                  <label for="customer_phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Continue to Payment</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <!-- Step 2: Payment -->
      <?php if ($current_step == 2): ?>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="reservation-card card">
            <div class="card-header">
              <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i> Payment Information</h4>
            </div>
            <div class="card-body p-4">
              <h5 class="mb-4">Payment Method</h5>
              <div class="payment-methods">
                <div class="payment-method active">
                  <i class="far fa-credit-card"></i>
                  <div>Credit/Debit Card</div>
                </div>
                <div class="payment-method">
                  <i class="fab fa-paypal"></i>
                  <div>PayPal</div>
                </div>
              </div>
              
              <form method="post" id="paymentForm" novalidate>
              <input type="hidden" name="step" value="2">
              <div class="mb-3">
                <label for="card_name" class="form-label">Name on Card</label>
                <input type="text" class="form-control" id="card_name" name="card_name" required pattern="^[A-Za-z\s]{2,}$" maxlength="50">
                <div class="invalid-feedback">Please enter the name on the card (letters and spaces only).</div>
              </div>
              <div class="mb-3">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" class="form-control" id="card_number" name="card_number" required pattern="^(\d{4} ?){4}$" maxlength="19" inputmode="numeric" placeholder="1234 5678 9012 3456" autocomplete="cc-number">
                <div class="invalid-feedback">Please enter a valid 16-digit card number.</div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="card_expiry" class="form-label">Expiration Date</label>
                  <input type="text" class="form-control" id="card_expiry" name="card_expiry" required pattern="^(0[1-9]|1[0-2])\/([0-9]{2})$" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
                  <div class="invalid-feedback">Please enter a valid expiration date (MM/YY).</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="card_cvv" class="form-label">CVV</label>
                  <input type="text" class="form-control" id="card_cvv" name="card_cvv" required pattern="^\d{3,4}$" maxlength="4" inputmode="numeric" placeholder="123" autocomplete="cc-csc">
                  <div class="invalid-feedback">Please enter a valid 3 or 4 digit CVV.</div>
                </div>
              </div>
                
                <hr class="my-4">
                
                <h5 class="mb-3">Order Summary</h5>
                <div class="detail-row">
                  <div class="detail-label">Vehicle</div>
                  <div class="detail-value"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Rental Period</div>
                  <div class="detail-value"><?php echo $rental_days; ?> day<?php echo $rental_days != 1 ? 's' : ''; ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Total Amount</div>
                  <div class="detail-value fw-bold vehicle-price"
                      data-price="<?php echo $total_with_fees; ?>"
                      data-rate="<?php echo $usdRate; ?>">
                      <span class="currency">LKR</span>
                      <span class="amount"><?php echo number_format($total_with_fees, 2); ?></span>
                  </div>
                </div>
                
                <div class="form-check mt-4 mb-3">
                <input class="form-check-input" type="checkbox" id="terms_agreement" required>
                <label class="form-check-label" for="terms_agreement">
                  I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Cancellation Policy</a>
                </label>
                <div class="invalid-feedback">You must agree before submitting.</div>
              </div>
              <button type="submit" class="btn btn-primary w-100">Complete Reservation</button>
            </form>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <!-- Step 3: Confirmation -->
      <?php if ($current_step == 3 && $success): ?>
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <div class="reservation-card card">
            <div class="card-body p-5">
              <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <h2 class="mb-3">Reservation Confirmed!</h2>
              <p class="lead mb-4">Thank you for your reservation. We've sent a confirmation to your email.</p>
              
              <div class="card bg-light p-4 mb-4 text-start">
                <h5 class="mb-3"><i class="fas fa-receipt me-2"></i> Reservation #<?php echo $reservation_id; ?></h5>
                <div class="detail-row">
                  <div class="detail-label">Vehicle</div>
                  <div class="detail-value"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Pick-Up</div>
                  <div class="detail-value"><?php echo htmlspecialchars($pickup_date); ?> at <?php echo htmlspecialchars($pickup_time); ?>, <?php echo htmlspecialchars($pickup_location); ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Return</div>
                  <div class="detail-value"><?php echo htmlspecialchars($return_date); ?> at <?php echo htmlspecialchars($return_time); ?>, <?php echo htmlspecialchars($return_location); ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Total Paid</div>
                  <div class="detail-value fw-bold vehicle-price"
                      data-price="<?php echo $total_with_fees; ?>"
                      data-rate="<?php echo $usdRate; ?>">
                      <span class="currency">LKR</span>
                      <span class="amount"><?php echo number_format($total_with_fees, 2); ?></span>
                  </div>
                </div>
              </div>
              
              <p class="mb-4">We've sent all the details to <strong><?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?></strong>. Please check your inbox.</p>
              
              <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-outline-primary">Back to Home</a>
                <!-- <a href="../vehicles/" class="btn btn-primary">View Your Reservations</a> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- GSAP (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>

    <!-- Your JS File -->
    <script src="../assets/js/index.js"></script>
    <script src="../assets/js/currencyHandler.js"></script>
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

    <script>
    // Card number formatting: 1234 5678 9012 3456
    document.getElementById('card_number').addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '').substring(0,16);
      let formatted = value.replace(/(.{4})/g, '$1 ').trim();
      e.target.value = formatted;
    });

    // Expiry formatting: MM/YY and validation for not in the past
    document.getElementById('card_expiry').addEventListener('input', function (e) {
      let value = e.target.value.replace(/\D/g, '').substring(0,4);
      if (value.length > 2) {
        value = value.substring(0,2) + '/' + value.substring(2,4);
      }
      e.target.value = value;
    });

    document.getElementById('paymentForm').addEventListener('submit', function (e) {
      // Validate expiry date is not in the past
      var expiry = document.getElementById('card_expiry');
      var expVal = expiry.value;
      var valid = true;

      if (/^(0[1-9]|1[0-2])\/\d{2}$/.test(expVal)) {
        var parts = expVal.split('/');
        var month = parseInt(parts[0], 10);
        var year = parseInt(parts[1], 10) + 2000; // YY to YYYY
        var now = new Date();
        var thisMonth = now.getMonth() + 1;
        var thisYear = now.getFullYear();
        if (year < thisYear || (year === thisYear && month < thisMonth)) {
          valid = false;
        }
      } else {
        valid = false;
      }

      if (!valid) {
        expiry.setCustomValidity('Invalid');
        expiry.classList.add('is-invalid');
        e.preventDefault();
        e.stopPropagation();
      } else {
        expiry.setCustomValidity('');
        expiry.classList.remove('is-invalid');
      }
    });

    // Bootstrap 5 validation
    document.addEventListener('DOMContentLoaded', function () {
      var form = document.getElementById('paymentForm');
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
    </script>

    <?php 
    // Display the social media icons
    displaySocialIcons($social_config); 
    ?>

  </body>
</html>