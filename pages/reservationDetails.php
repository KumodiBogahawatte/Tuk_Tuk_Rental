<?php
session_start();
require_once '../config/db_connect.php';
include_once '../includes/social_icons.php';
require_once '../service/CurrencyService.php';
require_once '../service/ReservationService.php';

$currencyService = new CurrencyService($pdo);
$reservationService = new ReservationService($pdo, $currencyService);

// --- Handle GET parameters from booking forms (index.php, details.php) ---
// Initialize variables from GET or SESSION if navigating back/forth
$vehicle_id = $_GET['vehicle_id'] ?? $_SESSION['reservation_data']['vehicle_id'] ?? null;
$pickup_location = $_GET['pickup_location'] ?? $_SESSION['reservation_data']['pickup_location'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? $_SESSION['reservation_data']['pickup_date'] ?? '';
$pickup_time = $_GET['pickup_time'] ?? $_SESSION['reservation_data']['pickup_time'] ?? '';
$return_location = $_GET['return_location'] ?? $_SESSION['reservation_data']['return_location'] ?? '';
$return_date = $_GET['return_date'] ?? $_SESSION['reservation_data']['return_date'] ?? '';
$return_time = $_GET['return_time'] ?? $_SESSION['reservation_data']['return_time'] ?? '';
$selected_extras = isset($_GET['extras']) ? (array)$_GET['extras'] : (isset($_SESSION['reservation_data']['selected_extras']) ? json_decode($_SESSION['reservation_data']['selected_extras'], true) : []);
$vehicle_image = $_GET['vehicle_image'] ?? $_SESSION['reservation_data']['vehicle_image'] ?? '';

// --- State Management ---
$current_step = 1; // Default to step 1 (Reservation Details)
$success = false;
$error = '';
$reservation_id = null;
$calculated_costs = []; // Will store the output of calculatePricing

// --- Helper Functions ---
function parseDateForDb($dateString) {
    // Converts D/M/YYYY to YYYY-MM-DD
    $d = DateTime::createFromFormat('d/m/Y', $dateString);
    return $d ? $d->format('Y-m-d') : null;
}

function displayFormattedPrice($amountUSD, $currencyService) {
    $currentCurrency = $_SESSION['preferred_currency'] ?? 'LKR'; // Assume session stores preferred currency
    $priceData = $currencyService->formatPrice($amountUSD, $currentCurrency);
    return "{$priceData['symbol']} {$priceData['amount']}";
}

// --- Fetch Vehicle and Calculate Costs (for display in step 1, or re-calculation if needed) ---
$vehicle = null;
$rental_days = 0;
if ($vehicle_id) {
    $vehicle = $reservationService->getVehicleById($vehicle_id);

    // Calculate rental days
    $pickup_date_obj = DateTime::createFromFormat('d/m/Y', $pickup_date);
    $return_date_obj = DateTime::createFromFormat('d/m/Y', $return_date);

    if ($pickup_date_obj && $return_date_obj && $pickup_date_obj <= $return_date_obj) {
        $interval = $pickup_date_obj->diff($return_date_obj);
        $rental_days = $interval->days + 1; // +1 to include both start and end day
    } else {
        $error = 'Invalid date range provided.';
    }

    if ($vehicle && !$error) {
        $calculated_costs = $reservationService->calculatePricing(
            $vehicle,
            $pickup_location,
            $return_location,
            $rental_days,
            $selected_extras,
            $pickup_time,
            $return_time
        );
    } else if (!$vehicle) {
        $error = "Vehicle not found.";
    }
} else {
    $error = "No vehicle selected.";
}

// --- Process Form Submission (POST requests) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_step = $_POST['step'] ?? 0;

    if ($posted_step == 1) { // User submitted Reservation Details form
        $customer_name = $_POST['customer_name'] ?? '';
        $customer_email = $_POST['customer_email'] ?? '';
        $customer_phone = $_POST['customer_phone'] ?? '';

        if (!$customer_name || !$customer_email || !$customer_phone) {
            $error = 'Please fill in all required customer details.';
        } elseif (!$vehicle_id || !$pickup_location || !$pickup_date || !$pickup_time || !$return_location || !$return_date || !$return_time) {
            $error = 'Missing booking details. Please go back and select them again.';
        } elseif ($error) { // If there was a date error or vehicle error from GET params
            // Error is already set, display it.
        } else {
            // Convert dates to DB format
            $pickup_date_db = parseDateForDb($pickup_date);
            $return_date_db = parseDateForDb($return_date);

            // Check for overlapping reservation just before proceeding
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status IN ('pending','confirmed') AND (pickup_date <= ? AND return_date >= ?)");
            $check_stmt->execute([$vehicle_id, $return_date_db, $pickup_date_db]);

            if ($check_stmt->fetchColumn() == 0) {
                // Store all data in session for the next step
                $_SESSION['reservation_data'] = [
                    'vehicle_id' => $vehicle_id,
                    'pickup_location' => $pickup_location,
                    'pickup_date' => $pickup_date, // Keep original format for display
                    'pickup_time' => $pickup_time,
                    'return_location' => $return_location,
                    'return_date' => $return_date, // Keep original format for display
                    'return_time' => $return_time,
                    'selected_extras' => json_encode($selected_extras),
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'customer_phone' => $customer_phone,
                    'calculated_costs' => $calculated_costs, // Store the calculated costs in USD
                    'vehicle_image' => $vehicle_image,
                ];
                $current_step = 2; // Move to payment step
            } else {
                $error = 'Sorry, this vehicle is unavailable for the selected dates. Please adjust your dates.';
            }
        }
    } elseif ($posted_step == 2) { // User submitted Payment form
        $card_name = $_POST['card_name'] ?? '';
        $card_number = $_POST['card_number'] ?? '';
        $card_expiry = $_POST['card_expiry'] ?? '';
        $card_cvv = $_POST['card_cvv'] ?? '';
        $payment_method_id = $_POST['payment_method'] ?? null; // Added payment method ID

        if (!$card_name || !$card_number || !$card_expiry || !$card_cvv || !$payment_method_id) {
            $error = 'Please fill in all payment details and select a method.';
        } elseif (!isset($_SESSION['reservation_data'])) {
            $error = 'Reservation data missing. Please start over.';
        } else {
            $data = $_SESSION['reservation_data'];
            // Re-parse dates to DB format for insertion
            $pickup_date_db = parseDateForDb($data['pickup_date']);
            $return_date_db = parseDateForDb($data['return_date']);

            // Double-check for overlap one last time before final insert
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status IN ('pending', 'confirmed') AND (pickup_date <= ? AND return_date >= ?)");
            $check_stmt->execute([$data['vehicle_id'], $return_date_db, $pickup_date_db]);

            if ($check_stmt->fetchColumn() == 0) {
                // Insert reservation
                $stmt_insert = $pdo->prepare("
                    INSERT INTO reservations (
                        vehicle_id, pickup_location, pickup_date, pickup_time,
                        return_location, return_date, return_time,
                        customer_name, customer_email, customer_phone,
                        status, final_total_usd, selected_extras
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?, ?)
                ");
                $stmt_insert->execute([
                    $data['vehicle_id'],
                    $data['pickup_location'],
                    $pickup_date_db,
                    $data['pickup_time'],
                    $data['return_location'],
                    $return_date_db,
                    $data['return_time'],
                    $data['customer_name'],
                    $data['customer_email'],
                    $data['customer_phone'],
                    $data['calculated_costs']['total_amount_due_usd'],
                    $data['selected_extras']
                ]);
                $reservation_id = $pdo->lastInsertId();
                $current_step = 3; // Move to confirmation step
                $success = true;
                unset($_SESSION['reservation_data']); // Clear session data
            } else {
                $error = 'Sorry, this vehicle has just been booked for those dates. Please choose different dates.';
                $current_step = 1; // Go back to step 1 to allow date changes
            }
        }
    }
} else {
    // Initial GET request or fresh page load.
    // Ensure all necessary data is available from GET parameters.
    if (!$vehicle_id || !$pickup_location || !$pickup_date || !$pickup_time || !$return_location || !$return_date || !$return_time) {
        $error = 'Please provide all rental details (vehicle, locations, dates, times).';
    }
}

// If an error occurred during POST for step 1, ensure step remains 1 and error is visible
$showError = ($error && $_SERVER['REQUEST_METHOD'] === 'POST');

// Data for step 2 if coming from step 1 POST
if ($current_step == 2 && isset($_SESSION['reservation_data'])) {
    $data = $_SESSION['reservation_data'];
    $vehicle_id = $data['vehicle_id']; // Re-fetch vehicle details if needed for display
    $vehicle = $reservationService->getVehicleById($vehicle_id);
    $calculated_costs = $data['calculated_costs'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      
      <?php if ($current_step == 1 && $vehicle && !$error): // Only show if valid vehicle and no initial error ?>
      <div class="row">
        <div class="col-lg-8">
          <div class="reservation-card card">
            <div class="card-header">
              <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i> Your Reservation</h4>
            </div>
            <div class="card-body p-4">
              <div class="vehicle-image-container">
                <img src="../<?php echo htmlspecialchars($vehicle_image); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="vehicle-image">
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
                <div class="detail-label">Daily Rate (USD)</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['daily_rate_usd'], $currencyService); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Rental Days</div>
                <div class="detail-value"><?php echo $calculated_costs['rental_days']; ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Base Rental Cost</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['base_rental_cost_usd'], $currencyService); ?></div>
              </div>
              <?php if ($calculated_costs['duration_discount_usd'] > 0): ?>
              <div class="detail-row text-success">
                <div class="detail-label">Duration Discount</div>
                <div class="detail-value">-<?php echo displayFormattedPrice($calculated_costs['duration_discount_usd'], $currencyService); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Rental After Discount</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['rental_after_discount_usd'], $currencyService); ?></div>
              </div>
              <?php endif; ?>
              <div class="detail-row">
                <div class="detail-label">Pickup Charge</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['pickup_charge_usd'], $currencyService); ?></div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Return Charge</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['return_charge_usd'], $currencyService); ?></div>
              </div>
              <?php if ($calculated_costs['license_fee_usd'] > 0): ?>
              <div class="detail-row">
                <div class="detail-label">License Fee</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['license_fee_usd'], $currencyService); ?></div>
              </div>
              <?php endif; ?>

              <?php if (!empty($calculated_costs['extras_details'])): ?>
              <hr>
              <h6>Extras:</h6>
              <?php foreach ($calculated_costs['extras_details'] as $extra): ?>
                <div class="detail-row">
                  <div class="detail-label ps-3"><?php echo htmlspecialchars($extra['name']); ?></div>
                  <div class="detail-value"><?php echo displayFormattedPrice($extra['price_usd'], $currencyService); ?></div>
                </div>
              <?php endforeach; ?>
              <?php endif; ?>

              <?php if ($calculated_costs['night_charge_usd'] > 0): ?>
              <div class="detail-row">
                <div class="detail-label">Night Time Charge</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['night_charge_usd'], $currencyService); ?></div>
              </div>
              <?php endif; ?>

              <hr>
              <div class="detail-row">
                <div class="detail-label fw-bold">Subtotal</div>
                <div class="detail-value fw-bold"><?php echo displayFormattedPrice($calculated_costs['subtotal_usd'], $currencyService); ?></div>
              </div>
              <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label">Refundable Deposit</div>
                <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['deposit_usd'], $currencyService); ?></div>
              </div>
              <hr>
              <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label fw-bold">Total Amount Due</div>
                <div class="detail-value fw-bold text-danger"><?php echo displayFormattedPrice($calculated_costs['total_amount_due_usd'], $currencyService); ?></div>
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
                  <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['reservation_data']['customer_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="customer_email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($_SESSION['reservation_data']['customer_email'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="customer_phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($_SESSION['reservation_data']['customer_phone'] ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Continue to Payment</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php elseif ($current_step == 1 && $error): // Display error if present on step 1 ?>
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <a href="index.php" class="btn btn-primary mt-3">Go to Home Page</a>
        </div>
      </div>
      <?php endif; ?>
      
      <!-- Step 2: Payment -->
      <?php if ($current_step == 2 && isset($_SESSION['reservation_data']) && $vehicle && $calculated_costs): ?>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="reservation-card card">
            <div class="card-header">
              <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i> Payment Information</h4>
            </div>
            <div class="card-body p-4">
              <h5 class="mb-4">Select Payment Method</h5>
              <div class="payment-methods row mb-4">
                <?php
                    $paymentMethods = $reservationService->getPaymentMethods();
                    foreach ($paymentMethods as $method):
                ?>
                <div class="col-md-4 mb-2">
                    <div class="payment-method-option form-check card card-body text-center" style="cursor:pointer;">
                        <input class="form-check-input d-none" type="radio" name="payment_method" id="method_<?php echo $method['id']; ?>" value="<?php echo $method['id']; ?>" required>
                        <label class="form-check-label d-block py-2" for="method_<?php echo $method['id']; ?>">
                            <?php if ($method['method_name'] === 'Online card payment'): ?>
                                <i class="far fa-credit-card fa-2x mb-2"></i>
                            <?php elseif ($method['method_name'] === 'Bank deposit'): ?>
                                <i class="fas fa-university fa-2x mb-2"></i>
                            <?php elseif ($method['method_name'] === 'Cash on pick-up'): ?>
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                            <?php endif; ?>
                            <div class="fw-bold"><?php echo htmlspecialchars($method['method_name']); ?></div>
                            <?php if ($method['processing_fee_percent'] > 0): ?>
                                <small class="text-muted">(+<?php echo $method['processing_fee_percent']; ?>% fee)</small>
                            <?php endif; ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="alert alert-danger d-none" id="payment-method-error">Please select a payment method.</div>

              <form method="post" id="paymentForm" novalidate>
              <input type="hidden" name="step" value="2">
              <div id="card-details-section" class="mb-3">
                  <h5 class="mb-3">Card Details</h5>
                  <div class="mb-3">
                    <label for="card_name" class="form-label">Name on Card</label>
                    <input type="text" class="form-control" id="card_name" name="card_name" pattern="^[A-Za-z\s]{2,}$" maxlength="50">
                    <div class="invalid-feedback">Please enter the name on the card (letters and spaces only).</div>
                  </div>
                  <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" pattern="^(\d{4} ?){4}$" maxlength="19" inputmode="numeric" placeholder="1234 5678 9012 3456" autocomplete="cc-number">
                    <div class="invalid-feedback">Please enter a valid 16-digit card number.</div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="card_expiry" class="form-label">Expiration Date</label>
                      <input type="text" class="form-control" id="card_expiry" name="card_expiry" pattern="^(0[1-9]|1[0-2])\/([0-9]{2})$" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
                      <div class="invalid-feedback">Please enter a valid expiration date (MM/YY).</div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="card_cvv" class="form-label">CVV</label>
                      <input type="text" class="form-control" id="card_cvv" name="card_cvv" pattern="^\d{3,4}$" maxlength="4" inputmode="numeric" placeholder="123" autocomplete="cc-csc">
                      <div class="invalid-feedback">Please enter a valid 3 or 4 digit CVV.</div>
                    </div>
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
                  <div class="detail-label">Base Rental Cost</div>
                  <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['base_rental_cost_usd'], $currencyService); ?></div>
                </div>
                <?php if ($calculated_costs['duration_discount_usd'] > 0): ?>
                <div class="detail-row text-success">
                  <div class="detail-label">Duration Discount</div>
                  <div class="detail-value">-<?php echo displayFormattedPrice($calculated_costs['duration_discount_usd'], $currencyService); ?></div>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                  <div class="detail-label">Location Fees</div>
                  <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['location_fees_usd'], $currencyService); ?></div>
                </div>
                <?php if ($calculated_costs['license_fee_usd'] > 0): ?>
                <div class="detail-row">
                  <div class="detail-label">License Fee</div>
                  <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['license_fee_usd'], $currencyService); ?></div>
                </div>
                <?php endif; ?>
                <?php if ($calculated_costs['extras_cost_usd'] > 0): ?>
                <div class="detail-row">
                  <div class="detail-label">Extras Cost</div>
                  <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['extras_cost_usd'], $currencyService); ?></div>
                </div>
                <?php endif; ?>
                <?php if ($calculated_costs['night_charge_usd'] > 0): ?>
                <div class="detail-row">
                  <div class="detail-label">Night Time Charge</div>
                  <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['night_charge_usd'], $currencyService); ?></div>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <div class="detail-label">Refundable Deposit</div>
                    <div class="detail-value"><?php echo displayFormattedPrice($calculated_costs['deposit_usd'], $currencyService); ?></div>
                </div>
                <hr>
                <div class="detail-row" style="border-bottom: none;">
                  <div class="detail-label fw-bold">Total Amount Due</div>
                  <div class="detail-value fw-bold text-danger"><?php echo displayFormattedPrice($calculated_costs['total_amount_due_usd'], $currencyService); ?></div>
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
                  <div class="detail-value fw-bold text-danger"><?php echo displayFormattedPrice($calculated_costs['total_amount_due_usd'], $currencyService); ?></div>
                </div>
              </div>
              
              <p class="mb-4">We've sent all the details to <strong><?php echo htmlspecialchars($_SESSION['reservation_data']['customer_email'] ?? $_POST['customer_email'] ?? ''); ?></strong>. Please check your inbox.</p>
              
              <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-outline-primary">Back to Home</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="../assets/js/index.js"></script>
    <script src="../assets/js/currencyHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    flatpickr(".date-input", {
        dateFormat: "d/m/Y", // Make sure this matches PHP's expected format
        minDate: "today",
        disableMobile: "true"
    });
    flatpickr(".time-input", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // 24-hour format for consistency
        time_24hr: true,
        disableMobile: "true"
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodOptions = document.querySelectorAll('.payment-method-option');
        const cardDetailsSection = document.getElementById('card-details-section');
        const paymentMethodError = document.getElementById('payment-method-error');

        // Function to toggle card details visibility and validation based on selection
        function toggleCardDetails(selectedMethodId) {
            const onlineCardMethodId = '1'; // Assuming 'Online card payment' has ID 1 based on your inserts

            if (selectedMethodId === onlineCardMethodId) {
                cardDetailsSection.style.display = 'block';
                // Add 'required' to card inputs
                cardDetailsSection.querySelectorAll('input').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            } else {
                cardDetailsSection.style.display = 'none';
                // Remove 'required' and clear validation state
                cardDetailsSection.querySelectorAll('input').forEach(input => {
                    input.removeAttribute('required');
                    input.classList.remove('is-invalid', 'is-valid');
                    input.value = ''; // Clear inputs when not required
                });
            }
        }

        paymentMethodOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all and add to clicked
                paymentMethodOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                // Check the hidden radio button inside this option
                const radioButton = this.querySelector('input[type="radio"]');
                if (radioButton) {
                    radioButton.checked = true;
                    paymentMethodError.classList.add('d-none'); // Hide error if a method is selected
                    toggleCardDetails(radioButton.value); // Toggle card details based on selection
                }
            });
        });

        // Initialize state based on default selection or no selection
        const initiallySelectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (initiallySelectedMethod) {
            document.querySelector(`.payment-method-option input[value="${initiallySelectedMethod.value}"]`).closest('.payment-method-option').classList.add('active');
            toggleCardDetails(initiallySelectedMethod.value);
        } else {
            toggleCardDetails(null); // Hide card details if no method is pre-selected
        }

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
          // Check if a payment method is selected
          const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
          if (!selectedMethod) {
              paymentMethodError.classList.remove('d-none');
              e.preventDefault();
              e.stopPropagation();
              return;
          } else {
              paymentMethodError.classList.add('d-none');
          }

          const onlineCardMethodId = '1';
          if (selectedMethod.value === onlineCardMethodId) {
                // Validate expiry date is not in the past, only if card payment is selected
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
                  expiry.setCustomValidity('Invalid expiration date');
                  expiry.classList.add('is-invalid');
                  e.preventDefault();
                  e.stopPropagation();
                } else {
                  expiry.setCustomValidity('');
                  expiry.classList.remove('is-invalid');
                }
          }

          // Bootstrap 5 form validation
          if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
          }
          this.classList.add('was-validated');
        }, false);
    });
    </script>

    <?php displaySocialIcons($social_config); ?>
  </body>
</html>