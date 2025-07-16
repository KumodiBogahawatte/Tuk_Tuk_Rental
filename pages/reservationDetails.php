<?php
session_start();
require_once '../config/db_connect.php'; // Ensures $pdo and $company_phone are available
include_once '../includes/social_icons.php';
require_once '../service/CurrencyService.php'; // Ensure this file exists and works
$currencyService = new CurrencyService($pdo);
$usdRate = $currencyService->getExchangeRate('USD', 'LKR'); // Rate for display if LKR is used, calculations in USD

$showError = false; // Flag to control error display

// --- FIXED GLOBAL DEPOSIT (in USD) ---
// IMPORTANT: Adjust this value to your actual deposit amount in USD
$depositUSD = 150.00; // Fixed deposit in USD

// Get vehicle and booking info from query params
$vehicle_id = $_GET['id'] ?? null;
$pickup_location = $_GET['pickup_location'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? '';
$pickup_time = $_GET['pickup_time'] ?? '';
$return_location = $_GET['return_location'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$return_time = $_GET['return_time'] ?? '';
$image_url = $_GET['image_url'] ?? ''; // Vehicle image URL

// New: Get selected extras from query params (these will come from details.php modal)
$selected_extra_ids = isset($_GET['extras']) && is_array($_GET['extras']) ? array_map('intval', $_GET['extras']) : [];

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

// --- Fetch location prices (Pickup/Return Charges) ---
$pickup_location_price = 0;
$return_location_price = 0;

function cleanLocation($location) {
    // Remove anything after the first " ("
    return preg_replace('/\s+\(.*$/', '', $location);
}

$pickup_location_cleaned = cleanLocation($pickup_location);
$return_location_cleaned = cleanLocation($return_location);

if ($pickup_location_cleaned) {
    $stmt = $pdo->prepare("SELECT usd_price FROM locations WHERE name = ?");
    $stmt->execute([$pickup_location_cleaned]);
    $row = $stmt->fetch();
    if ($row) $pickup_location_price = $row['usd_price'];
}
if ($return_location_cleaned) {
    $stmt = $pdo->prepare("SELECT usd_price FROM locations WHERE name = ?");
    $stmt->execute([$return_location_cleaned]);
    $row = $stmt->fetch();
    if ($row) $return_location_price = $row['usd_price'];
}

$location_fee = $pickup_location_price + $return_location_price;


// --- Fetch selected extras pricing ---
$selected_extras_details = [];
$total_extras_fee = 0;
// Note: 'Night time charges' handled separately below to avoid double-counting if selected as an extra
$night_charge_extra_id = null; // Store ID for potential special handling if needed
if (!empty($selected_extra_ids)) {
    $placeholders = implode(',', array_fill(0, count($selected_extra_ids), '?'));
    $stmt_extras = $pdo->prepare("SELECT id, name, description, usd_price FROM extras WHERE id IN ($placeholders) AND is_active = 1");
    $stmt_extras->execute($selected_extra_ids);
    $all_selected_extras = $stmt_extras->fetchAll(PDO::FETCH_ASSOC);

    foreach ($all_selected_extras as $extra) {
        if ($extra['name'] === 'Night time charges') {
            $night_charge_extra_id = $extra['id']; // Store ID if we need to remove it later from generic extras
            // We don't add its price to $total_extras_fee here, it's calculated in $night_time_charge
        } else {
            $selected_extras_details[] = $extra; // Add to display list
            $total_extras_fee += $extra['usd_price']; // Sum for total
        }
    }
}


// --- Calculate rental duration and pricing components ---
$rental_days = 0;
$effective_daily_rate = $vehicle['usd_price'] ?? 0; // Default to vehicle's base price from DB
$total_vehicle_rental_price = 0;
$night_time_charge = 0;
$license_fee_calc = 0; // The license fee from the vehicle record

if ($vehicle && $pickup_date_sql && $return_date_sql) {
    $pickup_date_obj = new DateTime($pickup_date_sql);
    $return_date_obj = new DateTime($return_date_sql);
    $interval = $pickup_date_obj->diff($return_date_obj);
    $rental_days = $interval->days + 1; // +1 to include both start and end day

    // Get the effective daily rate based on rental_days from duration_pricing table
    // ORDER BY min_days DESC to ensure the longest applicable range is found first for 'OVER 60'
    $stmt_duration_rules = $pdo->prepare("SELECT effective_daily_rate_usd FROM duration_pricing WHERE min_days <= ? AND (max_days >= ? OR max_days IS NULL) ORDER BY min_days DESC LIMIT 1");
    $stmt_duration_rules->execute([$rental_days, $rental_days]);
    $duration_rule = $stmt_duration_rules->fetch(PDO::FETCH_ASSOC);

    if ($duration_rule) {
        $effective_daily_rate = $duration_rule['effective_daily_rate_usd'];
    }
    // else: if no specific rule applies, use the vehicle's base usd_price (already set as default)

    $total_vehicle_rental_price = $effective_daily_rate * $rental_days;

    // --- Calculate Night Time Charges ---
    // Only apply night charge if vehicle allows it (night_charge_active column in vehicles)
    // and if a night charge extra is defined in the extras table.
    if (($vehicle['night_charge_active'] ?? 0) == 1) {
        // Function to calculate night charge based on rental period
        function calculateNightCharge($pickupDateStr, $pickupTimeStr, $returnDateStr, $returnTimeStr, $pdo_connection) {
            $startDateTime = new DateTime($pickupDateStr . ' ' . $pickupTimeStr);
            $endDateTime = new DateTime($returnDateStr . ' ' . $returnTimeStr);

            $nightChargePerNight = 0;
            // Fetch the night time charge from the 'extras' table
            $stmt_night_extra = $pdo_connection->prepare("SELECT usd_price FROM extras WHERE name = 'Night time charges' AND is_active = 1");
            $stmt_night_extra->execute();
            $night_extra_row = $stmt_night_extra->fetch(PDO::FETCH_ASSOC);
            if ($night_extra_row) {
                $nightChargePerNight = $night_extra_row['usd_price'];
            }

            if ($nightChargePerNight == 0) {
                return 0; // No night charge configured or active
            }

            $totalNightsCharged = 0;
            $currentProcessingDate = clone $startDateTime;

            // Loop day by day within the rental period
            while ($currentProcessingDate < $endDateTime) {
                $nightStartForDay = (clone $currentProcessingDate)->setTime(18, 0, 0); // 6 PM today
                $nightEndForDay = (clone $currentProcessingDate)->modify('+1 day')->setTime(6, 0, 0); // 6 AM tomorrow

                // Determine effective start/end for overlap check for THIS night window
                $overlapStart = max($startDateTime, $nightStartForDay);
                $overlapEnd = min($endDateTime, $nightEndForDay);

                // If there's any overlap duration, charge for this night.
                // A more precise implementation would calculate partial hours of night time.
                // For simplicity as per requirement: if rental touches any part of the 6PM-6AM window for a given night,
                // we count it as a night charged.
                if ($overlapStart < $overlapEnd) {
                    $totalNightsCharged++;
                }

                // Move to the next day for checking the next night window
                // Set time to 00:00:00 of the next day to prevent issues with date changes across 24h intervals
                $currentProcessingDate->modify('+1 day')->setTime(0, 0, 0);
            }
            return $totalNightsCharged * $nightChargePerNight;
        }

        $night_time_charge = calculateNightCharge($pickup_date_sql, $pickup_time, $return_date_sql, $return_time, $pdo);
    }

    // --- Apply License Fee ---
    if (($vehicle['license_required'] ?? 0) == 1 && ($vehicle['license_fee_usd'] ?? 0) > 0) {
        $license_fee_calc = $vehicle['license_fee_usd'];
    }
}

// --- Calculate Subtotal and Total ---
// Subtotal = Vehicle Rental Cost + Location Fee + Total Extras Fee + Night Time Charge + License Fee
$subtotal = $total_vehicle_rental_price + $location_fee + $total_extras_fee + $night_time_charge + $license_fee_calc;

// Total amount due (for display, typically including deposit for initial payment)
$total_display_amount = $subtotal + $depositUSD;


//Process form submission
$current_step = 1; //1=reservation, 2=payment, 3=confirmation
$success = false;
$error = '';
$reservation_id = null;

//Step1: Reservation details submission (from customer info form)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 1) {
  // Use filter_input for safer input retrieval
  $customer_name = filter_input(INPUT_POST, 'customer_name' );
  $customer_email = filter_input(INPUT_POST, 'customer_email', FILTER_VALIDATE_EMAIL);
  $customer_phone = filter_input(INPUT_POST, 'customer_phone' );

  // Retrieve existing GET parameters that were passed via hidden fields to preserve state
  // This is crucial because Step 1 form is POST, but subsequent steps need initial GET data
  $vehicle_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  $pickup_location = filter_input(INPUT_POST, 'pickup_location' );
  $pickup_date = filter_input(INPUT_POST, 'pickup_date' );
  $pickup_time = filter_input(INPUT_POST, 'pickup_time' );
  $return_location = filter_input(INPUT_POST, 'return_location' );
  $return_date = filter_input(INPUT_POST, 'return_date' );
  $return_time = filter_input(INPUT_POST, 'return_time' );
  $image_url = filter_input(INPUT_POST, 'image_url' );
  $selected_extra_ids = isset($_POST['extras']) && is_array($_POST['extras']) ? array_map('intval', $_POST['extras']) : [];

  // Re-run calculations with the posted data to ensure consistency
  // (This part is redundant if the above calculations are done before POST check, but good for self-containedness)
  // For clarity, in this full example, the calculation block above is executed first using $_GET data.
  // The $_SESSION['reservation_data'] below will capture these values.

  if($vehicle_id && $pickup_location && $pickup_date && $pickup_time && $return_location && $return_date && $return_time && $customer_name && $customer_email && $customer_phone) {
    $pickup_date_sql = parseDate($pickup_date);
    $return_date_sql = parseDate($return_date);

    // Re-fetch vehicle details (or pass from initial GET, but fetching again is safer)
    $vehicle_stmt = $pdo->prepare('SELECT * FROM vehicles WHERE id = ?');
    $vehicle_stmt->execute([$vehicle_id]);
    $vehicle = $vehicle_stmt->fetch();

    if (!$vehicle) {
        $error = 'Vehicle not found.';
        $showError = true;
        // Keep current step as 1
    } else {
        // Re-calculate all pricing details (as done above based on initial GET vars)
        // Ensure these match if you re-do them here. For this example, we assume they are already computed.

        // Check for overlapping reservation
        $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status IN ('pending','confirmed', 'pending_deposit', 'pending_pickup_payment') AND (pickup_date <= ? AND return_date >= ?)");
        $check->execute([$vehicle_id, $return_date_sql, $pickup_date_sql]);
        if($check->fetchColumn() == 0) {
            // No overlapping reservation, move to payment step
            $current_step = 2;
            // Store all reservation details in session for Step 2
            $_SESSION['reservation_data'] = [
                'vehicle_id' => $vehicle_id,
                'pickup_location' => cleanLocation($pickup_location),
                'pickup_date_sql' => $pickup_date_sql,
                'pickup_time' => $pickup_time,
                'return_location' => cleanLocation($return_location),
                'return_date_sql' => $return_date_sql,
                'return_time' => $return_time,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                // Store all calculated price components and totals
                'effective_daily_rate' => $effective_daily_rate, // New
                'total_vehicle_rental_price' => $total_vehicle_rental_price,
                'location_fee' => $location_fee,
                'total_extras_fee' => $total_extras_fee,
                'night_time_charge' => $night_time_charge,
                'license_fee_calc' => $license_fee_calc,
                'subtotal' => $subtotal, // Subtotal including all fees but not deposit
                'deposit_amount' => $depositUSD, // Deposit amount
                'total_display_amount' => $total_display_amount, // Sum of everything for display
                'selected_extra_ids' => $selected_extra_ids, // Pass selected extra IDs to session
                'rental_days' => $rental_days, // Pass rental days for confirmation display
                'image_url' => $image_url // Pass image URL for confirmation
            ];
        } else {
            // Overlapping reservation
            $error = 'Sorry, this vehicle has just been booked for those dates. Please choose different dates.';
            $showError = true;
        }
    }
  } else {
    // Missing required fields
    $error = 'Please fill in all required fields for your information.';
    $showError = true;
  }
}

// Step 2: Payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 2) {
    if (!isset($_SESSION['reservation_data'])) {
        $error = 'Session data missing. Please restart the reservation process.';
        $current_step = 1; // Go back to step 1
        $showError = true;
    } else {
        $data = $_SESSION['reservation_data']; // This contains all calculated totals
        $payment_method = filter_input(INPUT_POST, 'payment_method' );

        // Common validation for all methods
        if (!in_array($payment_method, ['card', 'bank_deposit', 'cash_on_pickup'])) {
            $error = 'Invalid payment method selected.';
            $current_step = 2;
            $showError = true;
        } elseif ($payment_method === 'card') {
            // Placeholder for real card payment validation - THIS MUST BE REPLACED WITH GATEWAY LOGIC
            $card_name = filter_input(INPUT_POST, 'card_name' );
            $card_number = filter_input(INPUT_POST, 'card_number' );
            $card_expiry = filter_input(INPUT_POST, 'card_expiry' );
            $card_cvv = filter_input(INPUT_POST, 'card_cvv' );

            if (!$card_name || !$card_number || !$card_expiry || !$card_cvv) {
                $error = 'Please fill in all credit card details.';
                $current_step = 2;
                $showError = true;
            }
            // *** HERE IS WHERE YOUR REAL PAYMENT GATEWAY INTEGRATION WOULD GO ***
            // You would call the gateway API to process the payment for $data['deposit_amount']
            // If successful, proceed. If not, set $error.
            $reservation_status = 'confirmed'; // Assuming card payment is successful and confirms immediately
        } elseif ($payment_method === 'bank_deposit') {
            $reservation_status = 'pending_deposit'; // Requires manual verification
        } elseif ($payment_method === 'cash_on_pickup') {
            $reservation_status = 'pending_pickup_payment'; // Payment due at pickup
            // No payment processed now for cash on pickup. The 'total_display_amount' is what's due at pickup.
        }

        if (empty($error)) {
            // Double-check for overlap before inserting to prevent race conditions
            $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status IN ('confirmed', 'pending_deposit', 'pending_pickup_payment') AND (pickup_date <= ? AND return_date >= ?)");
            $check->execute([$data['vehicle_id'], $data['return_date_sql'], $data['pickup_date_sql']]);

            if ($check->fetchColumn() == 0) {
                // Prepare extras for storage (e.g., comma-separated IDs)
                $extras_to_store = !empty($data['selected_extra_ids']) ? implode(',', $data['selected_extra_ids']) : NULL;

                // Insert reservation
                $insert = $pdo->prepare("INSERT INTO reservations (vehicle_id, pickup_location, pickup_date, pickup_time, return_location, return_date, return_time, customer_name, customer_email, customer_phone, payment_method, status, total_price, currency, exchange_rate, extras_selected) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
                    $payment_method, // New payment method column value
                    $reservation_status, // Dynamic status
                    $data['total_display_amount'], // Store the full amount for record
                    'USD', // All calculations in USD
                    $usdRate, // Store the exchange rate at time of booking
                    $extras_to_store // Store selected extras
                ]);
                $reservation_id = $pdo->lastInsertId();
                $current_step = 3;
                $success = true;
                // Preserve vehicle data for confirmation display in Step 3
                $_SESSION['last_reservation_vehicle'] = $vehicle;
                // Preserve customer email for confirmation display
                $_SESSION['last_reservation_customer_email'] = $data['customer_email'];
                // Preserve payment method for confirmation display
                $_SESSION['last_reservation_payment_method'] = $payment_method;
                $_SESSION['last_reservation_status'] = $reservation_status;

                unset($_SESSION['reservation_data']); // Clear session data after successful booking
            } else {
                $error = 'Sorry, this vehicle has just been booked for those dates. Please choose different dates or vehicles.';
                $showError = true;
                $current_step = 1; // Send back to step 1 to re-evaluate availability
            }
        }
    }
}

// Data for confirmation step, retrieved from session after successful booking
if ($current_step == 3 && $success) {
    // If coming directly from payment success, $vehicle would be set from previous session.
    // However, if the page is refreshed on step 3, $vehicle might be null.
    // So, we use data stored in $_SESSION for reliable display.
    $vehicle = $_SESSION['last_reservation_vehicle'] ?? $vehicle;
    $customer_email_for_confirmation = $_SESSION['last_reservation_customer_email'] ?? '';
    $payment_method_for_confirmation = $_SESSION['last_reservation_payment_method'] ?? 'N/A';
    $reservation_status_for_confirmation = $_SESSION['last_reservation_status'] ?? 'N/A';

    // Recalculate totals for display only if session data is still available,
    // otherwise use the values passed into the form on step 2
    if (isset($_SESSION['reservation_data'])) {
        $total_display_amount = $_SESSION['reservation_data']['total_display_amount'] ?? $total_display_amount;
        $pickup_location = $_SESSION['reservation_data']['pickup_location'] ?? $pickup_location;
        $pickup_date = parseDateReverse($_SESSION['reservation_data']['pickup_date_sql'] ?? $pickup_date_sql);
        $pickup_time = $_SESSION['reservation_data']['pickup_time'] ?? $pickup_time;
        $return_location = $_SESSION['reservation_data']['return_location'] ?? $return_location;
        $return_date = parseDateReverse($_SESSION['reservation_data']['return_date_sql'] ?? $return_date_sql);
        $return_time = $_SESSION['reservation_data']['return_time'] ?? $return_time;
    }
}

// Function to convert Y-m-d back to d/m/Y for display
function parseDateReverse($date_sql) {
    $date_obj = DateTime::createFromFormat('Y-m-d', $date_sql);
    return $date_obj ? $date_obj->format('d/m/Y') : $date_sql;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
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
                <img src="./<?php echo htmlspecialchars($vehicle['main_image'] ?? 'assets/images/default-vehicle.jpg'); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="vehicle-image">
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
              <?php if (!empty($selected_extras_details)): ?>
              <div class="detail-row">
                  <div class="detail-label"><i class="fas fa-plus-circle me-2"></i>Selected Extras</div>
                  <div class="detail-value">
                      <ul>
                          <?php foreach ($selected_extras_details as $extra): ?>
                              <li><?php echo htmlspecialchars($extra['name']); ?>: <span class="currency">USD</span> <?php echo number_format($extra['usd_price'], 2); ?></li>
                          <?php endforeach; ?>
                      </ul>
                  </div>
              </div>
              <?php endif; ?>
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
                <div class="detail-label">Effective Daily Rate</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $effective_daily_rate; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($effective_daily_rate, 2); ?></span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Rental Days</div>
                <div class="detail-value"><?php echo $rental_days; ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Vehicle Rental Cost</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $total_vehicle_rental_price; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($total_vehicle_rental_price, 2); ?></span>
                </div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Location Fee</div>
              <div class="detail-value vehicle-price"
                  data-price="<?php echo $location_fee; ?>"
                  data-rate="<?php echo $usdRate; ?>">
                  <span class="currency">USD</span>
                  <span class="amount"><?php echo number_format($location_fee, 2); ?></span>
              </div>
            </div>
            <?php if ($total_extras_fee > 0): ?>
            <div class="detail-row">
                <div class="detail-label">Extras Fee</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $total_extras_fee; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($total_extras_fee, 2); ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($night_time_charge > 0): ?>
            <div class="detail-row">
                <div class="detail-label">Night Time Charge</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $night_time_charge; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($night_time_charge, 2); ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($license_fee_calc > 0): ?>
            <div class="detail-row">
                <div class="detail-label">License Fee</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $license_fee_calc; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($license_fee_calc, 2); ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="detail-row">
                <div class="detail-label fw-bold">Subtotal</div>
                <div class="detail-value fw-bold vehicle-price"
                    data-price="<?php echo $subtotal; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($subtotal, 2); ?></span>
                </div>
            </div>
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label">Deposit</div>
                <div class="detail-value vehicle-price"
                    data-price="<?php echo $depositUSD; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($depositUSD, 2); ?></span>
                </div>
            </div>
            <hr>
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label fw-bold">Total Amount Due</div>
                <div class="detail-value fw-bold vehicle-price total-amount-due" style="color:rgb(255, 0, 0);"
                    data-price="<?php echo $total_display_amount; ?>"
                    data-rate="<?php echo $usdRate; ?>">
                    <span class="currency">USD</span>
                    <span class="amount"><?php echo number_format($total_display_amount, 2); ?></span>
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
                <!-- Pass all original query parameters to maintain state for Step 2 POST -->
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if (is_array($value)): ?>
                        <?php foreach ($value as $val): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($val); ?>">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="mb-3">
                  <label for="customer_name" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="customer_email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="customer_phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? ''); ?>" required>
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
                <div class="payment-method active" data-method="card">
                  <i class="far fa-credit-card"></i>
                  <div>Credit/Debit Card</div>
                </div>
                <div class="payment-method" data-method="bank_deposit">
                  <i class="fas fa-bank"></i>
                  <div>Bank Deposit</div>
                </div>
                <div class="payment-method" data-method="cash_on_pickup">
                  <i class="fas fa-money-bill-wave"></i>
                  <div>Cash on Pick-up</div>
                </div>
              </div>

              <form method="post" id="paymentForm" novalidate>
              <input type="hidden" name="step" value="2">
              <input type="hidden" name="payment_method" value="card"> <!-- Default to card -->

              <!-- Pass all original session data as hidden fields for submission -->
              <?php if (isset($_SESSION['reservation_data'])): ?>
                  <?php foreach ($_SESSION['reservation_data'] as $key => $value): ?>
                      <?php if (is_array($value)): ?>
                          <?php foreach ($value as $val): ?>
                              <input type="hidden" name="session_<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($val); ?>">
                          <?php endforeach; ?>
                      <?php else: ?>
                          <input type="hidden" name="session_<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                      <?php endif; ?>
                  <?php endforeach; ?>
              <?php endif; ?>

              <div id="card_payment_form" class="mt-4">
                <h6>Pay Deposit: <span class="currency">USD</span> <span class="amount"><?php echo number_format($depositUSD, 2); ?></span></h6>
                <p class="text-muted">The remaining balance of <span class="currency">USD</span> <span class="amount"><?php echo number_format($subtotal, 2); ?></span> will be due at pickup.</p>
                <hr>
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
              </div>

              <div id="bank_deposit_info" class="mt-4" style="display:none;">
                  <h6>Bank Deposit Details</h6>
                  <p class="text-muted">Please transfer the total amount of <span class="currency">USD</span> <span class="amount"><?php echo number_format($total_display_amount, 2); ?></span> to the following bank account:</p>
                  <ul class="list-group list-group-flush mb-3">
                      <li class="list-group-item"><strong>Bank Name:</strong> [Your Bank Name Here]</li>
                      <li class="list-group-item"><strong>Account Name:</strong> [Your Account Holder Name Here]</li>
                      <li class="list-group-item"><strong>Account Number:</strong> [Your Account Number Here]</li>
                      <li class="list-group-item"><strong>SWIFT/BIC:</strong> [Your SWIFT/BIC Code Here]</li>
                  </ul>
                  <p class="alert alert-info">Your reservation status will be "Pending Deposit". Please email your deposit receipt to *[Your Email Here]* or use our chat service for faster confirmation. We will confirm your booking once the payment is verified.</p>
              </div>

              <div id="cash_on_pickup_info" class="mt-4" style="display:none;">
                  <h6>Cash on Pick-up</h6>
                  <p class="text-muted">You will pay the full amount of <span class="currency">USD</span> <span class="amount"><?php echo number_format($total_display_amount, 2); ?></span> in cash upon picking up the vehicle.</p>
                  <p class="alert alert-warning">Please ensure you have the exact amount ready as our drivers may not carry change. Your reservation status will be "Pending Pickup Payment".</p>
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
                      data-price="<?php echo $total_display_amount; ?>"
                      data-rate="<?php echo $usdRate; ?>">
                      <span class="currency">USD</span>
                      <span class="amount"><?php echo number_format($total_display_amount, 2); ?></span>
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
                  <div class="detail-label">Payment Method</div>
                  <div class="detail-value"><?php echo htmlspecialchars($payment_method_for_confirmation ?? 'N/A'); ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Reservation Status</div>
                  <div class="detail-value"><?php echo htmlspecialchars($reservation_status_for_confirmation ?? 'N/A'); ?></div>
                </div>
                <div class="detail-row">
                  <div class="detail-label">Total Amount</div>
                  <div class="detail-value fw-bold vehicle-price"
                      data-price="<?php echo $total_display_amount; ?>"
                      data-rate="<?php echo $usdRate; ?>">
                      <span class="currency">USD</span>
                      <span class="amount"><?php echo number_format($total_display_amount, 2); ?></span>
                  </div>
                </div>
              </div>

              <p class="mb-4">We've sent all the details to <strong><?php echo htmlspecialchars($customer_email_for_confirmation); ?></strong>. Please check your inbox.</p>

              <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-outline-primary">Back to Home</a>
                <!-- <a href="./vehicles/" class="btn btn-primary">View Your Reservations</a> -->
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
    flatpickr(".time-input", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // 24-hour format
        time_24hr: true
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

      // Only validate card fields if card payment is selected
      const paymentMethodInput = document.querySelector('input[name="payment_method"]');
      if (paymentMethodInput && paymentMethodInput.value === 'card') {
          if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expVal)) {
              valid = false;
          } else {
              var parts = expVal.split('/');
              var month = parseInt(parts[0], 10);
              var year = parseInt(parts[1], 10) + 2000; // YY to YYYY
              var now = new Date();
              var thisMonth = now.getMonth() + 1;
              var thisYear = now.getFullYear();
              if (year < thisYear || (year === thisYear && month < thisMonth)) {
                  valid = false;
              }
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
      } else {
          // If not card payment, ensure custom validity is cleared for card fields
          expiry.setCustomValidity('');
          expiry.classList.remove('is-invalid');
      }

      // Also ensure terms agreement is checked
      const termsCheckbox = document.getElementById('terms_agreement');
      if (!termsCheckbox.checked) {
          termsCheckbox.setCustomValidity('You must agree to the terms and conditions.');
          termsCheckbox.classList.add('is-invalid');
          e.preventDefault();
          e.stopPropagation();
      } else {
          termsCheckbox.setCustomValidity('');
          termsCheckbox.classList.remove('is-invalid');
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

    // --- Payment Method Toggle JS ---
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodButtons = document.querySelectorAll('.payment-method');
        const cardForm = document.getElementById('card_payment_form');
        const bankDepositInfo = document.getElementById('bank_deposit_info');
        const cashOnPickupInfo = document.getElementById('cash_on_pickup_info');
        const paymentMethodHiddenInput = document.querySelector('input[name="payment_method"]');
        const termsCheckbox = document.getElementById('terms_agreement');

        // Function to update required status of card fields
        function updateCardFieldsRequired(isRequired) {
            const cardFields = cardForm.querySelectorAll('input[required]');
            cardFields.forEach(field => {
                field.required = isRequired;
                if (!isRequired) {
                    field.value = ''; // Clear values if not required
                    field.classList.remove('is-invalid'); // Remove validation state
                    field.setCustomValidity('');
                }
            });
        }

        paymentMethodButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove 'active' from all, add to clicked
                paymentMethodButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const selectedMethod = this.dataset.method;
                paymentMethodHiddenInput.value = selectedMethod;

                // Hide all payment detail sections
                cardForm.style.display = 'none';
                bankDepositInfo.style.display = 'none';
                cashOnPickupInfo.style.display = 'none';

                // Update required status for card fields
                updateCardFieldsRequired(false); // Default to false, enable only if card is selected

                // Show the relevant section and update required status
                if (selectedMethod === 'card') {
                    cardForm.style.display = 'block';
                    updateCardFieldsRequired(true);
                } else if (selectedMethod === 'bank_deposit') {
                    bankDepositInfo.style.display = 'block';
                } else if (selectedMethod === 'cash_on_pickup') {
                    cashOnPickupInfo.style.display = 'block';
                }

                // If terms checkbox was invalid, reset its state when payment method changes
                // (Optional: might be too aggressive, but ensures user re-checks)
                termsCheckbox.setCustomValidity('');
                termsCheckbox.classList.remove('is-invalid');
            });
        });

        // Initialize: Trigger click on the default active method (Credit/Debit Card) on page load
        document.querySelector('.payment-method[data-method="card"]').click();
    });
    </script>

    <?php
    // Display the social media icons
    displaySocialIcons($social_config);
    ?>

  </body>
</html>