<!-- Main landing page -->
<?php
$filter = $_GET['type'] ?? 'All';
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - Vehicles </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/vehicles.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>

    <!--Brands-->
    <section>
    
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

        <div class="row">
            <!-- Vehicle Card 1 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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

            <!-- Vehicle Card 2 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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


            <!-- Vehicle Card 4 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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

            <!-- Vehicle Card 5 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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

            <!-- Vehicle Card 7 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
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

            <!-- Repeat for other vehicle cards... -->
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

  </body>
</html>