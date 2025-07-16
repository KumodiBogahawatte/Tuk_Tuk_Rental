<?php
require_once '../config/db_connect.php';
require_once('../service/currencyService.php');
$currencyService = new CurrencyService($pdo);
$usdRate = $currencyService->getExchangeRate('USD', 'LKR');

// Get vehicle ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch vehicle data
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

// Fetch locations for datalist
$stmt = $pdo->query("SELECT name, usd_price FROM locations ORDER BY name ASC");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If vehicle not found, redirect to vehicles page
if (!$vehicle) {
    header('Location: vehicles.php');
    exit();
}

// Fetch other vehicles for the "OTHER THREE WHEELS" section
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id != ? ORDER BY RAND() LIMIT 6");
$stmt->execute([$id]);
$other_vehicles = $stmt->fetchAll();
?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/social_icons.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?> - TukTuk Rental</title>
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
    <link rel="stylesheet" href="../assets/css/details.css">
    <?php getSocialIconsStyles(); ?>
  </head>
  <body>

    <!-- Vehicle details Section -->
    <section class="vehicle-details-section py-5">
        <div class="container">
            <div class="row">
                <!-- Vehicle Image on Left -->
                <h2 class="vehicle-brand"><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></h2>
                <div class="vehicle-price" 
                                data-price="<?php echo $vehicle['usd_price']; ?>" 
                                data-rate="<?php echo $usdRate; ?>">
                                <span class="currency">USD</span>
                                <span class="amount"><?php echo number_format($vehicle['usd_price'], 2); ?></span>
                                <span class="per-day">/day</span>
                            </div>
                <div class="col-md-6 mb-4">
                    <!-- Main Image -->
                    <img id="mainImage" src="../<?php echo htmlspecialchars($vehicle['main_image']); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="mb-3" data-aos="zoom-in" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">

                    <!-- Thumbnail Images -->
                    <div class="row g-2">
                        <?php if ($vehicle['image1']): ?>
                        <div class="col-4">
                            <img src="../<?php echo htmlspecialchars($vehicle['image1']); ?>" class="img-fluid img-thumbnail thumb-img fixed-thumb" alt="Additional Image 1" onclick="changeImage(this)">
                        </div>
                        <?php endif; ?>
                        <?php if ($vehicle['image2']): ?>
                        <div class="col-4">
                            <img src="../<?php echo htmlspecialchars($vehicle['image2']); ?>" class="img-fluid img-thumbnail thumb-img fixed-thumb" alt="Additional Image 2" onclick="changeImage(this)">
                        </div>
                        <?php endif; ?>
                        <?php if ($vehicle['image3']): ?>
                        <div class="col-4">
                            <img src="../<?php echo htmlspecialchars($vehicle['image3']); ?>" class="img-fluid img-thumbnail thumb-img fixed-thumb" alt="Additional Image 3" onclick="changeImage(this)">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Vehicle Details on Right -->
                <div class="col-md-6 mb-4">
                    <!-- Heading above the card grid -->
                    <div class="mb-4 mt-2">
                        <h2 class="fw-bold" style="margin-top: -40px;">Technical Specifications</h2>
                    </div>

                    <!-- Card Grid for Technical Specifications -->
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-cogs fa-2x mb-2" style="color: #375FE0;"></i>
                                    <h5 class="card-title">Gear Box</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($vehicle['gear_box']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gas-pump fa-2x mb-2" style="color: #375FE0;"></i>
                                    <h5 class="card-title">Fuel Type</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($vehicle['fuel_type']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-tachometer-alt fa-2x mb-2" style="color: #375FE0;"></i>
                                    <h5 class="card-title">Max Speed</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($vehicle['max_speed']); ?> km/h</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2" style="color: #375FE0;"></i>
                                    <h5 class="card-title">Capacity</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($vehicle['capacity']); ?> passengers</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gas-pump fa-2x mb-2" style="color: #375FE0;"></i>
                                    <h5 class="card-title">Fuel Tank</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($vehicle['fuel_tank']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-road fa-2x mb-2" style="color: #375FE0;"></i>
                                    <h5 class="card-title">Mileage</h5>
                                    <p class="card-text"><?php echo htmlspecialchars($vehicle['mileage']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Book Now button below the grid -->
                    <div class="mt-4">
                        <button class="btn btn-sm btn-book" onclick="openReservationModal()">Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reservation Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Book Your Three Wheel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reservationForm" action="availability.php" method="GET">
                        <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                        
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label class="form-label">Pick-Up Location</label>
                                <input list="pickup_locations" name="pickup_location" id="pickup_location" class="form-control" required autocomplete="off">
                                <datalist id="pickup_locations">
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc['name']); ?>" data-price="<?php echo $loc['usd_price']; ?>">
                                            <?php echo htmlspecialchars($loc['name']); ?> ($ <?php echo number_format($loc['usd_price'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                                <span id="pickup-location-price" class="text-muted"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Return Location</label>
                                <input list="return_locations" name="return_location" id="return_location" class="form-control" required autocomplete="off">
                                <datalist id="return_locations">
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc['name']); ?>" data-price="<?php echo $loc['usd_price']; ?>">
                                            <?php echo htmlspecialchars($loc['name']); ?> ($ <?php echo number_format($loc['usd_price'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </datalist>
                                <span id="return-location-price" class="text-muted"></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pick-Up Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 date-input" name="pickup_date" placeholder="Select Date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pick-Up Time</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-clock"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 time-input" name="pickup_time" placeholder="Select Time" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Return Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 date-input" name="return_date" placeholder="Select Date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Return Time</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-clock"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 time-input" name="return_time" placeholder="Select Time" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Check Availability</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Selection Section -->
    <section class="container py-5 vehicles-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">OTHER THREE WHEELS</h2>
            <a href="vehicles.php" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row">
            <?php foreach ($other_vehicles as $other_vehicle): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card">
                    <img src="../<?php echo htmlspecialchars($other_vehicle['main_image']); ?>" alt="<?php echo htmlspecialchars($other_vehicle['brand'] . ' ' . $other_vehicle['model']); ?>" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand"><?php echo htmlspecialchars($other_vehicle['brand']); ?></h5>
                            <div class="vehicle-price" 
                                data-price="<?php echo $other_vehicle['usd_price']; ?>" 
                                data-rate="<?php echo $usdRate; ?>">
                                <span class="currency">USD</span>
                                <span class="amount">
                                    <?php
                                        $usd_price = $other_vehicle['usd_price'];
                                        echo is_numeric($usd_price) ? number_format($usd_price, 2) : htmlspecialchars($usd_price);
                                    ?>
                                </span>
                                <span class="per-day">/day</span>
                            </div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> <?php echo htmlspecialchars($other_vehicle['gear_box']); ?>
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($other_vehicle['fuel_type']); ?>
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-users"></i> <?php echo htmlspecialchars($other_vehicle['capacity']); ?> seats
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- GSAP (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <!-- AOS Library JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Custom JS -->
    <script src="../assets/js/index.js"></script>
    <script src="../assets/js/details.js"></script>
    <script src="../assets/js/currencyHandler.js"></script>
    <script>
        AOS.init();
    </script>

    <?php 
    // Display the social media icons
    displaySocialIcons($social_config); 
    ?>
  </body>
</html>