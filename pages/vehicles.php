<?php
require_once '../config/db_connect.php';
require_once('../service/CurrencyService.php');
require_once('../service/ReservationService.php'); // Add this

$currencyService = new CurrencyService($pdo);
$reservationService = new ReservationService($pdo, $currencyService); // Instantiate

// Get filter from URL
$filter = $_GET['type'] ?? 'All';

$sql = "SELECT * FROM vehicles";
if ($filter !== 'All') {
    $sql .= " WHERE fuel_type = ?";
}

$stmt = $pdo->prepare($sql);
if ($filter !== 'All') {
    $stmt->execute([$filter]);
} else {
    $stmt->execute();
}
$vehicles = $stmt->fetchAll();
?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/social_icons.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - Vehicles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/vehicles.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <?php getSocialIconsStyles(); ?>
  </head>
  <body>
    <!-- Header Section -->
    <header class="vehicles-header">
        <div class="vehicles-header-content">
            <div class="container">
                <h1 class="display-5 fw-bold">Vehicles</h1>
                <p>Choose your perfect ride from our wide selection of reliable tuktuks</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center custom-breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">vehicles</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>

    <!-- Professional Tuk-Tuk Showcase (no changes) -->
    <section class="tuktuk-showcase">
        <!-- Content will be dynamically generated -->
    </section>

    <!-- Vehicle Selection Section -->
    <section class="container py-5 vehicles-section vehicles-page">
        <div class="brand-list-section">
            <ul>
                <li><img src="../assets/images/Vehicles/brand-1.png" alt="brand-1"></li>
                <li><img src="../assets/images/Vehicles/brand-3.png" alt="brand-3"></li>
                <li><img src="../assets/images/Vehicles/brand-2.png" alt="brand-2"></li>
            </ul>
        </div>

        <br>
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h2 class="section-title">SELECT A VEHICLE GROUP</h2>
        </div>
        
        <!-- Fuel Type Filter Buttons -->
        <div class="d-flex justify-content-center mb-5">
            <div class="btn-group" role="group" aria-label="Fuel Type Filter">
                <a href="?type=All" class="btn btn-outline-primary <?= $filter === 'All' ? 'active' : '' ?>">All</a>
                <a href="?type=Petrol" class="btn btn-outline-primary <?= $filter === 'Petrol' ? 'active' : '' ?>">Petrol</a>
                <a href="?type=Diesel" class="btn btn-outline-primary <?= $filter === 'Diesel' ? 'active' : '' ?>">Diesel</a>
                <a href="?type=Electric" class="btn btn-outline-primary <?= $filter === 'Electric' ? 'active' : '' ?>">Electric</a>
            </div>
        </div>

        <div class="row" data-aos="fade-down" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card">
                    <img src="../<?php echo htmlspecialchars($vehicle['main_image']); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand"><?php echo htmlspecialchars($vehicle['brand']); ?></h5>
                            <div class="vehicle-price"
                                data-price-usd="<?php echo $vehicle['price_per_day']; ?>">
                                <span class="currency">LKR</span>
                                <span class="amount"></span>
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
                        <a href="details.php?id=<?php echo $vehicle['id']; ?>" class="btn view-details-btn mx-auto d-flex align-items-center justify-content-center">
                            View Details
                            <span class="arrow-circle ms-2">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="../assets/js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init();
    </script>
    <script src="../assets/js/currencyHandler.js"></script>
    <script src="../assets/js/tukTukShowcase.js"></script>
    <?php displaySocialIcons($social_config); ?>
  </body>
</html>