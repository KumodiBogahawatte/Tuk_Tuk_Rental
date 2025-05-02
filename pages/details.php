<!-- Main landing page -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental -Details </title>
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
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>

    <!-- Vehicle details Section -->
    <section class="vehicle-details-section py-5">
        <div class="container">
            <div class="row">
                <!-- Vehicle Image on Left -->
                <h2 class="vehicle-brand">Bajaj</h2>
                <p class="vehicle-price">LKR 3000.00 <span>/day</span></p>
                <div class="col-md-6 mb-4">
                <!-- Main Image -->
                <img id="mainImage" src="../assets/images/home/tuktuk.png" alt="Bajaj Tuk Tuk" class="mb-3" data-aos="zoom-in" data-aos-delay="100">

                <!-- Thumbnail Images -->
                <div class="row g-2">
                    <div class="col-4">
                        <img src="../assets/images/details/tuktuk1.jpg" class="img-fluid img-thumbnail thumb-img fixed-thumb" alt="Tuk 1" onclick="changeImage(this)">
                    </div>
                    <div class="col-4">
                        <img src="../assets/images/details/tuktuk2.jpg" class="img-fluid img-thumbnail thumb-img fixed-thumb" alt="Tuk 2" onclick="changeImage(this)">
                    </div>
                    <div class="col-4">
                        <img src="../assets/images/details/tuktuk3.jpg" class="img-fluid img-thumbnail thumb-img fixed-thumb" alt="Tuk 3" onclick="changeImage(this)">
                    </div>
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
                                    <i class="fas fa-cogs fa-2x mb-2" style="color: #5937E0;"></i>
                                    <h5 class="card-title">Gear Box</h5>
                                    <p class="card-text">Manual</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gas-pump fa-2x mb-2" style="color: #5937E0;"></i>
                                    <h5 class="card-title">Fuel Type</h5>
                                    <p class="card-text">Diesel</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-tachometer-alt fa-2x mb-2" style="color: #5937E0;"></i>
                                    <h5 class="card-title">Max Speed</h5>
                                    <p class="card-text">80 km/h</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2" style="color: #5937E0;"></i>
                                    <h5 class="card-title">Capacity</h5>
                                    <p class="card-text">4 passengers</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gas-pump fa-2x mb-2" style="color: #5937E0;"></i>
                                    <h5 class="card-title">Fuel Tank</h5>
                                    <p class="card-text">15 liters</p>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-road fa-2x mb-2" style="color: #5937E0;"></i>
                                    <h5 class="card-title">Mileage</h5>
                                    <p class="card-text">30 km/l</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Book Now button below the grid -->
                    <div class="mt-4">
                        <button class="btn btn-sm btn-primary" onclick="window.location.href='../pages/reservationDetails.php';">Book Now</button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Vehicle Selection Section -->
    <section class="container py-5 vehicles-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">OTHER THREE WHEELS</h2>
            <a href="../pages/vehicles.php" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row">
            <!-- Vehicle Card 1 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-down" data-aos-delay="100">
                    <img src="../assets/images/home/tuktuk.png"alt="Bajaj Tuk Tuk" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand">Bajaj</h5>
                            <div class="vehicle-price">LKR3000.00 <span>/day</span></div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> Manual
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-snowflake"></i> PB 92
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-luggage-cart"></i> Hood Rack
                            </div>
                        </div>
                        <button class="view-details-btn" onclick="location.href='../pages/details.php'">View Details</button>
                    </div>
                </div>
            </div>
            
            <!-- Vehicle Card 2 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-down" data-aos-delay="100">
                    <img src="../assets/images/home/tuktuk.png" alt="Bajaj Tuk Tuk" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand">Bajaj</h5>
                            <div class="vehicle-price">LKR3000.00 <span>/day</span></div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> Manual
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-snowflake"></i> PB 92
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-luggage-cart"></i> No Hood Rack
                            </div>
                        </div>
                        <button class="view-details-btn" onclick="location.href='../pages/details.php'">View Details</button>
                    </div>
                </div>
            </div>
            
            <!-- Vehicle Card 3 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-down" data-aos-delay="100">
                    <img src="../assets/images/home/tuktuk.png" alt="Bajaj Tuk Tuk" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand">Bajaj</h5>
                            <div class="vehicle-price">LKR3000.00 <span>/day</span></div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> Manual
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-snowflake"></i> PB 92
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-luggage-cart"></i> No Hood Rack
                            </div>
                        </div>
                        <button class="view-details-btn" onclick="location.href='../pages/details.php'">View Details</button>
                    </div>
                </div>
            </div>
            
            <!-- Vehicle Card 4 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-down" data-aos-delay="100">
                    <img src="../assets/images/home/tuktuk.png" alt="Bajaj Tuk Tuk" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand">Bajaj</h5>
                            <div class="vehicle-price">LKR3000.00 <span>/day</span></div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> Manual
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-snowflake"></i> PB 92
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-luggage-cart"></i> Hood Rack
                            </div>
                        </div>
                        <button class="view-details-btn" onclick="location.href='../pages/details.php'">View Details</button>
                    </div>
                </div>
            </div>
            
            <!-- Vehicle Card 5 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-down" data-aos-delay="100">
                    <img src="../assets/images/home/tuktuk.png" alt="Bajaj Tuk Tuk" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand">Bajaj</h5>
                            <div class="vehicle-price">LKR3000.00 <span>/day</span></div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> Manual
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-snowflake"></i> PB 92
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-luggage-cart"></i> Hood Rack
                            </div>
                        </div>
                        <button class="view-details-btn" onclick="location.href='../pages/details.php'">View Details</button>
                    </div>
                </div>
            </div>
            
            <!-- Vehicle Card 6 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-down" data-aos-delay="100">
                    <img src="../assets/images/home/tuktuk.png" alt="Bajaj Tuk Tuk" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand">Bajaj</h5>
                            <div class="vehicle-price">LKR3000.00 <span>/day</span></div>
                        </div>
                        <div class="specs-row">
                            <div class="spec-item">
                                <i class="fas fa-cog"></i> Manual
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-snowflake"></i> PB 92
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-luggage-cart"></i> No Hood Rack
                            </div>
                        </div>
                        <button class="view-details-btn" onclick="location.href='../pages/details.php'">View Details</button>
                    </div>
                </div>
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