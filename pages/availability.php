<?php
require_once '../config/db_connect.php'; // Ensures $pdo and $company_phone are available
include_once '../includes/social_icons.php';
require_once('../service/currencyService.php'); // Ensure this file exists and works
$currencyService = new CurrencyService($pdo);
$usdRate = $currencyService->getExchangeRate('USD', 'LKR'); // Rate for display if LKR is used

// Get form data (these come from index.php booking form or details.php modal)
$pickup_location = $_GET['pickup_location'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? '';
$pickup_time = $_GET['pickup_time'] ?? '';
$return_location = $_GET['return_location'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$return_time = $_GET['return_time'] ?? '';
// New: Capture extras selected from the details.php modal
$selected_extra_ids = isset($_GET['extras']) && is_array($_GET['extras']) ? $_GET['extras'] : [];

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

// Find vehicles NOT reserved for any part of the requested period
$sql = "SELECT * FROM vehicles WHERE id NOT IN (
    SELECT vehicle_id FROM reservations WHERE status IN ('pending','confirmed','pending_deposit','pending_pickup_payment')
    AND (
        (pickup_date <= :return_date AND return_date >= :pickup_date)
    )
)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':pickup_date' => $pickup_date_sql,
    ':return_date' => $return_date_sql
]);
$available_vehicles = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Available Three Wheels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/availability.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <?php getSocialIconsStyles(); ?>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<section class="container py-5 vehicles-section vehicles-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Available Three Wheels</h2>
        <a href="vehicles.php" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-2"></i></a>
    </div>
    <div class="row">
        <?php if (count($available_vehicles) > 0): ?>
            <?php foreach ($available_vehicles as $vehicle): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card">
                    <img src="../<?php echo htmlspecialchars($vehicle['main_image']); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand"><?php echo htmlspecialchars($vehicle['brand']); ?></h5>
                            <div class="vehicle-price"
                                data-price="<?php echo $vehicle['usd_price']; ?>"
                                data-rate="<?php echo $usdRate; ?>">
                                <span class="currency">USD</span>
                                <span class="amount"><?php echo number_format($vehicle['usd_price'], 2); ?></span>
                                <span class="per-day">/day</span>
                            </div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> <?php echo htmlspecialchars($vehicle['gear_box']); ?>
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($vehicle['fuel_type']); ?>
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-users"></i> <?php echo htmlspecialchars($vehicle['capacity']); ?> seats
                            </div>
                        </div>
                        <?php
                            // Prepare base URL params
                            $baseUrlParams = [
                                'id' => $vehicle['id'],
                                'pickup_location' => $pickup_location,
                                'pickup_date' => $pickup_date,
                                'pickup_time' => $pickup_time,
                                'return_location' => $return_location,
                                'return_date' => $return_date,
                                'return_time' => $return_time,
                                'image_url' => $vehicle['main_image']
                            ];

                            // Add extras to URL params if any were selected
                            if (!empty($selected_extra_ids)) {
                                $baseUrlParams['extras'] = $selected_extra_ids; // Add array directly, http_build_query handles it
                            }
                        ?>
                        <a href="reservationDetails.php?<?php echo http_build_query($baseUrlParams); ?>" class="btn view-details-btn">Book Now</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    Sorry, no vehicles are available for the selected dates.
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
    <?php
    // Display the social media icons
    displaySocialIcons($social_config);
    ?>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/currencyHandler.js"></script>
</body>
</html>