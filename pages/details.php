<?php
require_once '../config/db_connect.php';
require_once '../includes/social_icons.php';
require_once '../service/CurrencyService.php';
require_once '../service/ReservationService.php';

$currencyService = new CurrencyService($pdo);
$reservationService = new ReservationService($pdo, $currencyService);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicle = $reservationService->getVehicleById($id);

if (!$vehicle) {
    header('Location: vehicles.php');
    exit();
}

$other_vehicles = $pdo->prepare("SELECT * FROM vehicles WHERE id != ? ORDER BY RAND() LIMIT 6");
$other_vehicles->execute([$id]);
$other_vehicles = $other_vehicles->fetchAll();

// Fetch all available extras
$allExtras = $reservationService->getAllExtras();

// Fetch all locations with their base USD price
// Note: Frontend JS will use the ReservationService to get dynamic charges
$stmt = $pdo->query("SELECT location_name AS name, charge_usd AS price FROM pickup_charges ORDER BY location_name ASC");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                                data-price-usd="<?php echo $vehicle['price_per_day']; ?>">
                                <span class="currency">LKR</span>
                                <span class="amount"></span>
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

                    <?php if ($vehicle['license_required']): ?>
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <i class="fas fa-id-card fa-2x mb-2" style="color: #375FE0;"></i>
                            <h5 class="card-title">License Required</h5>
                            <p class="card-text">
                                This vehicle requires a valid local or international three-wheeler license.
                                <?php if ($vehicle['license_fee_usd'] > 0): ?>
                                You will be charged an additional fee of
                                <span class="license-fee-display" data-price-usd="<?php echo $vehicle['license_fee_usd']; ?>"></span>
                                for license processing.
                                <?php endif; ?>
                                <?php if ($vehicle['license_details']): ?>
                                <br><?php echo nl2br(htmlspecialchars($vehicle['license_details'])); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

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
                    <form id="reservationForm" action="reservation.php" method="GET">
                        <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                        <input type="hidden" name="vehicle_image" value="<?php echo htmlspecialchars($vehicle['main_image']); ?>">
                        
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label class="form-label">Pick-Up Location</label>
                                <input list="pickup_locations" name="pickup_location" id="pickup_location" class="form-control" required autocomplete="off">
                                <datalist id="pickup_locations">
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc['name']); ?>" data-price-usd="<?php echo $loc['price']; ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                                <small id="pickup-location-info" class="text-muted"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Return Location</label>
                                <input list="return_locations" name="return_location" id="return_location" class="form-control" required autocomplete="off">
                                <datalist id="return_locations">
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc['name']); ?>" data-price-usd="<?php echo $loc['price']; ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                                <small id="return-location-info" class="text-muted"></small>
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

                        <h5 class="mt-4 mb-3">Additional/Extras</h5>
                        <div class="row">
                            <?php foreach ($allExtras as $extra): ?>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input extra-checkbox" type="checkbox"
                                            name="extras[]" value="<?php echo $extra['id']; ?>"
                                            id="extra_<?php echo $extra['id']; ?>"
                                            data-price-usd="<?php echo $extra['price_usd']; ?>">
                                        <label class="form-check-label" for="extra_<?php echo $extra['id']; ?>">
                                            <?php echo htmlspecialchars($extra['name']); ?>
                                            <span class="extra-price-display" data-price-usd="<?php echo $extra['price_usd']; ?>"></span>
                                        </label>
                                        <?php if ($extra['description']): ?>
                                            <small class="text-muted d-block ms-4"><?php echo htmlspecialchars($extra['description']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Proceed to Reservation</button>
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
                                data-price-usd="<?php echo $other_vehicle['price_per_day']; ?>">
                                <span class="currency">LKR</span>
                                <span class="amount"></span>
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
                        <a href="details.php?id=<?php echo $other_vehicle['id']; ?>" class="btn view-details-btn mx-auto d-flex align-items-center justify-content-center">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../assets/js/index.js"></script>
    <script src="../assets/js/details.js"></script>
    <script src="../assets/js/currencyHandler.js"></script>
    <script>
        AOS.init();
        // Pass PHP data to JavaScript
        const phpLocations = <?php echo json_encode($locations); ?>;
        const phpExtras = <?php echo json_encode($allExtras); ?>;
        const phpVehicleDailyRate = <?php echo $vehicle['price_per_day']; ?>;
        const phpLicenseFee = <?php echo $vehicle['license_fee_usd']; ?>;
        const phpLicenseRequired = <?php echo json_encode($vehicle['license_required']); ?>;
    </script>
    <?php displaySocialIcons($social_config); ?>
  </body>
</html>