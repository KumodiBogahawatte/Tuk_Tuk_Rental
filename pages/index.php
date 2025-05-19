<?php
require_once '../config/db_connect.php';

// Fetch 6 vehicles for display
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC LIMIT 6");
$vehicles = $stmt->fetchAll();
?>

<!-- Main landing page -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental </title>
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
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>
<br>
    <!--Hero Section-->
    <section class="container-fluid py-5 hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <h1 class="hero-title">Experience the road like never before</h1>
                    <p class="hero-text">Discover exhilarating ride without hassle. Enjoy the city's greatest attractions at any time, quick and comfortable.</p>
                    <a href="vehicles.php" class="btn booking-btn d-inline-block">View all Three Wheel</a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="booking-form">
                        <h4 class="mb-4">Book your ThreeWheel</h4>
                        <form action="availability.php" method="GET">
                            <p class="form-label-title" style="color: #000;font-weight: 500;margin-bottom: 10px; margin-left:1px;"><i class="fa-solid fa-location-dot me-2"></i>Pick-Up Information</p>
                            <select class="form-select mb-3" name="pickup_location" id="pickup_location" required>
                                <option value="">Select pickup location</option>
                            </select>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fa-regular fa-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 date-input" name="pickup_date" placeholder="Rental Date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fa-regular fa-clock"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 time-input" name="pickup_time" placeholder="Select Time" required>
                                    </div>
                                </div>
                            </div>
                            <p class="form-label-title" style="color: #000;font-weight: 500;margin-bottom: 10px; margin-left:1px;"><i class="fa-solid fa-flag-checkered me-2"></i>Return Information</p>
                            <select class="form-select mb-3" name="return_location" id="return_location" required>
                                <option value="">Select return location</option>
                            </select>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fa-regular fa-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 date-input" name="return_date" placeholder="Return Date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fa-regular fa-clock"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 time-input" name="return_time" placeholder="Select Time" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn booking-btn w-100">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container py-5 features-section">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="feature-title">Availability</h3>
                    <p class="feature-text">Open locations throughout and all-season availability at preferred time.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-couch"></i>
                    </div>
                    <h3 class="feature-title">Comfort</h3>
                    <p class="feature-text">Greatest cushion formulation model recreates on average customer comfort.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h3 class="feature-title">Savings</h3>
                    <p class="feature-text">Premium rewards at start and continuous membership rewards system.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="container py-5 testimonials-section">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="../assets/images/home/tuk-tuk-night.jpg" alt="Tuk Tuk at night" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <div class="testimonial-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-image">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="testimonial-content">
                        <h4>Erat et semper</h4>
                        <p>Mauris vitae dapibus ligula. Praesent aliquam et elit adipiscing. Mi elementum dictum felis.</p>
                    </div>
                </div>
                <div class="testimonial-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-image">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="testimonial-content">
                        <h4>Urna nec volutpat rhoncus duis arcu</h4>
                        <p>Nullam at tincidunt enim. Duis viverra metus at dictum porttitor. Proin auctor dolor sodales.</p>
                    </div>
                </div>
                <div class="testimonial-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-image">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="testimonial-content">
                        <h4>Laorette accumsan imperdiet tempus</h4>
                        <p>Vivamus sollicitudin mauris et dignissim malesuada et. Augue adipiscing nibh. Sed scelerisque orci.</p>
                    </div>
                </div>
                <div class="testimonial-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-image">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="testimonial-content">
                        <h4>Cras nulla aliquet non eleifend amet et</h4>
                        <p>Praesent adipiscing elit dictum dolore. Fusce nisi diam justo pulvinar dui neque. Euismod mollestia blandit imperdit volutpat nibero.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicle Selection Section -->
    <section class="container py-5 vehicles-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">Choose The Three Wheel That Suits You</h2>
            <a href="vehicles.php" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row">
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="vehicle-card" data-aos="fade-up" data-aos-delay="100">
                    <img src="../<?php echo htmlspecialchars($vehicle['main_image']); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>" class="vehicle-image">
                    <div class="vehicle-details">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="vehicle-brand"><?php echo htmlspecialchars($vehicle['brand']); ?></h5>
                            <div class="vehicle-price">LKR <?php echo number_format($vehicle['price_per_day'], 2); ?> <span>/day</span></div>
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
                        <button class="view-details-btn" onclick="location.href='details.php?id=<?php echo $vehicle['id']; ?>'">View Details</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Statistics Section-->
    <section class="container-fluid py-5 stats-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="stats-title">Facts in Numbers</h2>
                <p class="stats-text">
                    Amet esse felis erat lorem. Praesent ipsum arcu tellus integer dignissim adipiscing consectetur 
                    volutpat orci et dictum hendrerit amet et aliquet hendrerit.
                </p>
            </div>
            <div class="row">
                <!-- Stat Card 1 -->
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-card bg-white rounded shadow-sm" data-aos="zoom-in" data-aos-delay="100">
                        <div class="d-flex">
                            <!-- Icon on the left -->
                            <div class="stat-icon me-3">
                                <img src="../assets/images/home/FactIcon-2.png" alt="Tuk Tuks icon" class="img-fluid">
                            </div>
                            <!-- Text on the right -->
                            <div>
                                <h3 class="stat-number" style="color: black;">540+</h3>
                                <p class="stat-label" style="color: black;">Tuk Tuks</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-card bg-white rounded shadow-sm" data-aos="zoom-in" data-aos-delay="100">
                        <div class="d-flex">
                            <div class="stat-icon me-3">
                                <img src="../assets/images/home/FactIcon-1.png" alt="Customers icon" class="img-fluid">
                            </div>
                            <div>
                                <h3 class="stat-number" style="color: black;">20k+</h3>
                                <p class="stat-label" style="color: black;">Customers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-card bg-white rounded shadow-sm" data-aos="zoom-in" data-aos-delay="100">
                        <div class="d-flex">
                            <!-- Icon on the left -->
                            <div class="stat-icon me-3">
                                <img src="../assets/images/home/FactIcon-3.png" alt="Years icon" class="img-fluid">
                            </div>
                            <!-- Text on the right -->
                            <div>
                                <h3 class="stat-number" style="color: black;">25+</h3>
                                <p class="stat-label" style="color: black;">Years</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 4 -->
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-card bg-white rounded shadow-sm" data-aos="zoom-in" data-aos-delay="100">
                        <div class="d-flex">
                            <!-- Icon on the left -->
                            <div class="stat-icon me-3">
                                <img src="../assets/images/home/FactIcon-4.png" alt="Miles icon" class="img-fluid">
                            </div>
                            <!-- Text on the right -->
                            <div>
                                <h3 class="stat-number" style="color: black;">200+</h3>
                                <p class="stat-label" style="color: black;">Miles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Mobile App Section -->
    <section class="container py-5 app-section">
        <div class="row align-items-center">
            <!-- Text Content -->
            <div class="col-lg-6 app-content mb-4 mb-lg-0">
                <h2 class="mb-3">Download Our Mobile App</h2>
                <p class="mb-4">Experience seamless ordering and tracking. Get exclusive deals right at your fingertips with our mobile app.</p>
                <div class="store-buttons d-flex gap-3">
                    <a href="#" class="store-btn app-store d-flex align-items-center gap-2">
                        <i class="fa-brands fa-apple fa-2x store-icon"></i>
                        <span>App Store</span>
                    </a>
                    <a href="#" class="store-btn play-store d-flex align-items-center gap-2">
                        <i class="fab fa-google-play fa-2x store-icon"></i>
                        <span>Google Play</span>
                    </a>
                </div>
            </div>

            <!-- Phone Stack -->
            <div class="col-lg-6 text-center device-showcase" data-aos="zoom-in" data-aos-duration="1200">
                <div class="phone-stack position-relative">
                    <img src="../assets/images/home/mobileApp.png" 
                        class="phone back-phone position-absolute img-fluid" 
                        style="width: 180px;" 
                        alt="Back phone"
                        >
                        
                    <img src="../assets/images/home/mobileApp.png" 
                        class="phone front-phone position-absolute img-fluid" 
                        style="width: 180px;" 
                        alt="Front phone">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container cta-section">
        <div class="cta-content">
            <h2>Enjoy every mile with adorable companionship.</h2>
            <p>Nullam augue felis erat dolor facilisis. Pretium tellus interdum amet eu consectetur imperdiet adipiscing in. Tempus consequat hendrerit amet.</p>
            <div class="cta-buttons">
                <a href="../pages/index.php" class="cta-btn cta-primary">Book Now</a>
                <a href="../pages/contact.php" class="cta-btn cta-secondary">Contact Us</a>
            </div>
        </div>
        <div class="cta-image">
            <img src="../assets/images/home/ColorfulTukTuk.png" alt="Colorful Tuk Tuk" class="img-fluid" style="width: 350px;">
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
  </body>
</html>