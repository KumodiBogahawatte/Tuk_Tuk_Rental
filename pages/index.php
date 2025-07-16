<?php
require_once '../config/db_connect.php';
include_once '../includes/social_icons.php';
require_once '../service/CurrencyService.php';
require_once '../service/ReservationService.php';

$currencyService = new CurrencyService($pdo);
$reservationService = new ReservationService($pdo, $currencyService);

$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC LIMIT 6");
$vehicles = $stmt->fetchAll();

// Fetch locations for datalist (only name and base charge, JS will handle conversion)
$stmt = $pdo->query("SELECT location_name AS name, charge_usd AS price FROM pickup_charges ORDER BY location_name ASC");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuk Tuk Rental </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <?php getSocialIconsStyles(); ?>
  </head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="main-content">
        <!--Hero Section-->
        <section class="container-fluid py-5 hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-mirror="true" data-aos-once="false">
                        <h1 class="hero-title" style="font-size: 50px;">RENT A TUK-TUK FOR THE REAL SRI LANKAN EXPERIENCE</h1>
                        <p class="hero-text">Rent a tuk-tuk, the most popular vehicle among Sri Lankans,  <br>
                            and roam freely around the country.</p>
                        <a href="vehicles.php" class="btn view-btn d-inline-block">View all Three Wheel</a>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <div class="booking-form">
                            <h4 class="mb-4">Book your tuktuk</h4>
                            <form action="reservation.php" id="booking-form" method="GET">
                                <p class="form-label-title" style="color: black !important;"><i class="fa-solid fa-location-dot me-2"></i>Pick-Up Information</p>
                                <label for="pickup_location">Pick-Up Location</label>
                                <input list="pickup_locations" name="pickup_location" id="pickup_location" class="form-control" required autocomplete="off">
                                <datalist id="pickup_locations">
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc['name']); ?>" data-price-usd="<?php echo $loc['price']; ?>"></option>
                                    <?php endforeach; ?>
                                </datalist><br>
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
                                <p class="form-label-title" style="color: black !important;"><i class="fa-solid fa-flag-checkered me-2"></i>Return Information</p>
                                <label for="return_location">Return Location</label>
                                <input list="return_locations" name="return_location" id="return_location" class="form-control" required autocomplete="off">
                                <datalist id="return_locations">
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc['name']); ?>" data-price-usd="<?php echo $loc['price']; ?>"></option>
                                    <?php endforeach; ?>
                                </datalist><br>
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
                        <div class="hero-image">
                            <img src="../assets/images/home/transport.png" alt="Colorful Tuk Tuk">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section (no changes here as they are static content) -->
        <section class="container py-5 features-section">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="100" data-aos-mirror="true"
        data-aos-once="false">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="feature-title">Availability</h3>
                        <p class="feature-text">Open locations throughout and all-season availability at preferred time.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="100" data-aos-mirror="true"
        data-aos-once="false">
                        <div class="feature-icon">
                            <i class="fas fa-couch"></i>
                        </div>
                        <h3 class="feature-title">Comfort</h3>
                        <p class="feature-text">Greatest cushion formulation model recreates on average customer comfort.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card" data-aos="zoom-in" data-aos-delay="100" data-aos-mirror="true"
        data-aos-once="false">
                        <div class="feature-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <h3 class="feature-title">Savings</h3>
                        <p class="feature-text">Premium rewards at start and continuous membership rewards system.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section (no changes here) -->
        <section class="container-fluid py-5 why-choose-section">
            <div class="container">
                <div class="why-choose-container">
                    <div class="text-center mb-5">
                        <h2 class="why-choose-section-title">Why Choose Our Tuk Tuk Rental</h2>
                        <p class="why-choose-section-subtitle">We’re not just another rental company — we’re your travel partner with a purpose.</p>
                    </div>
                    <div class="row align-items-center">
                        <!-- Left Side Features -->
                        <div class="col-lg-4">
                            <div class="choose-us-feature-item left-feature" data-aos="fade-right" data-aos-delay="100" data-aos-mirror="true"
        data-aos-once="false">
                                <div class="choose-us-feature-content text-end">
                                    <h4 class="choose-us-feature-title">Fully Insured Rental Service</h4>
                                    <p class="choose-us-feature-description">Complete insurance coverage for peace of mind. Travel worry-free with our comprehensive protection plans.</p>
                                </div>
                                <div class="choose-us-feature-icon-wrapper">
                                    <div class="choose-us-feature-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="choose-us-feature-item left-feature" data-aos="fade-right" data-aos-delay="200" data-aos-mirror="true"
        data-aos-once="false">
                                <div class="choose-us-feature-content text-end">
                                    <h4 class="choose-us-feature-title">Different Types of Tuk Tuks</h4>
                                    <p class="choose-us-feature-description">Wide variety of three-wheelers to choose from. Find the perfect vehicle for your adventure needs.</p>
                                </div>
                                <div class="choose-us-feature-icon-wrapper">
                                    <div class="choose-us-feature-icon">
                                        <i class="fas fa-car-side"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Center Image -->
                        <div class="col-lg-4 text-center">
                            <div class="central-image" data-aos="zoom-in" data-aos-delay="300" data-aos-mirror="true"
        data-aos-once="false">
                                <div class="image-circle">
                                    <img src="../assets/images/home/transport.png" alt="Tuk Tuk Illustration" class="img-fluid">
                                </div>
                                <div class="floating-dots">
                                    <div class="dot dot-1"></div>
                                    <div class="dot dot-2"></div>
                                    <div class="dot dot-3"></div>
                                    <div class="dot dot-4"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side Features -->
                        <div class="col-lg-4">
                            <div class="choose-us-feature-item right-feature" data-aos="fade-left" data-aos-delay="100" data-aos-mirror="true"
        data-aos-once="false">
                                <div class="choose-us-feature-icon-wrapper">
                                    <div class="choose-us-feature-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                </div>
                                <div class="choose-us-feature-content">
                                    <h4 class="choose-us-feature-title">Smart Mobility for Clients</h4>
                                    <p class="choose-us-feature-description">Modern booking system with real-time tracking. Easy reservations through our user-friendly platform.</p>
                                </div>
                            </div>
                            
                            <div class="choose-us-feature-item right-feature" data-aos="fade-left" data-aos-delay="200" data-aos-mirror="true"
        data-aos-once="false">
                                <div class="choose-us-feature-icon-wrapper">
                                    <div class="choose-us-feature-icon">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                </div>
                                <div class="choose-us-feature-content">
                                    <h4 class="choose-us-feature-title">Designed for Modern World</h4>
                                    <p class="choose-us-feature-description">Contemporary vehicles with modern amenities. Experience comfort and style in every journey.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vehicle Selection Section -->
        <section class="container py-5 vehicles-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Select Your Ride</h2>
                <a href="vehicles.php" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row">
                <?php foreach ($vehicles as $vehicle): ?>
                <div class="col-md-5 col-lg-4 mb-4">
                    <div class="card vehicle-card" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true"
        data-aos-once="false">
                        <img src="../<?php echo htmlspecialchars($vehicle['main_image']); ?>"
                            class="card-img-top vehicle-image"
                            alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>">
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title vehicle-brand mb-0"><?php echo htmlspecialchars($vehicle['brand']); ?></h5>
                                <div class="vehicle-price"
                                    data-price-usd="<?php echo htmlspecialchars($vehicle['price_per_day']); ?>">
                                    <span class="currency">LKR</span>
                                    <span class="amount"></span>
                                    <span class="per-day">/day</span>
                                </div>
                            </div>
                            
                            <div class="specs-row mb-3">
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

        <!-- Statistics Section (no changes here) -->
        <section class="container-fluid py-5 stats-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="stats-title">Our Impact in Numbers</h2>
                    <p class="stats-text">
                        Delivering excellence across the city for over two decades
                    </p>
                    <div class="stats-divider"></div>
                </div>
                
                <div class="stats-container">
                    <!-- Stat Item 1 -->
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-taxi"></i>
                            </div>
                            <div class="icon-pulse"></div>
                        </div>
                        <div class="stat-content">
                            <h3 class="counter" data-target="540" data-speed="200">0</h3>
                            <p>Tuk Tuks in Our Fleet</p>
                            <div class="stat-underline"></div>
                        </div>
                    </div>

                    <!-- Stat Item 2 -->
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="icon-pulse"></div>
                        </div>
                        <div class="stat-content">
                            <h3 class="counter" data-target="20000" data-speed="5000">0</h3>
                            <p>Happy Customers</p>
                            <div class="stat-underline"></div>
                        </div>
                    </div>

                    <!-- Stat Item 3 -->
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="icon-pulse"></div>
                        </div>
                        <div class="stat-content">
                            <h3 class="counter" data-target="25" data-speed="10">0</h3>
                            <p>Years of Service</p>
                            <div class="stat-underline"></div>
                        </div>
                    </div>

                    <!-- Stat Item 4 -->
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="icon-pulse"></div>
                        </div>
                        <div class="stat-content">
                            <h3 class="counter" data-target="200" data-speed="50">0</h3>
                            <p>Miles Covered Daily</p>
                            <div class="stat-underline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mobile App Section - Left Devices / Right Content (no changes here) -->
        <section class="container-fluid app-section">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Left Side - Device Showcase -->
                    <div class="col-lg-6 device-showcase" data-aos="fade-right">
                        <div class="phone-stack position-relative">
                            <img src="../assets/images/home/mobileApp.png"
                                class="phone back-phone position-absolute img-fluid"
                                alt="Back phone">
                            <img src="../assets/images/home/mobileApp.png"
                                class="phone front-phone position-absolute img-fluid"
                                alt="Front phone">
                        </div>
                    </div>

                    <!-- Right Side - Text Content -->
                    <div class="col-lg-6 app-content" data-aos="fade-left">
                        <h2 class="mb-3">Download Our Mobile App</h2>
                        <p class="mb-4">Experience seamless ordering and tracking. Get exclusive deals right at your fingertips with our mobile app.</p>
                        <div class="store-buttons d-flex gap-3">
                            <a href="#" class="store-btn app-store d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-brands fa-apple store-icon"></i>
                                <div class="d-flex flex-column">
                                    <small class="store-label">Download on the</small>
                                    <strong>App Store</strong>
                                </div>
                            </a>
                            <a href="#" class="store-btn play-store d-flex align-items-center justify-content-center gap-2">
                                <i class="fab fa-google-play store-icon"></i>
                                <div class="d-flex flex-column">
                                    <small class="store-label">Get it on</small>
                                    <strong>Google Play</strong>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section (no changes here) -->
        <section class="container-fluid py-5 cta-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 cta-content" data-aos="fade-right" data-aos-mirror="true"
        data-aos-once="false">
                        <h2>Enjoy every mile <br> with adorable companionship.</h2>
                        <p>Nullam augue felis erat dolor facilisis. <br>  Pretium tellus interdum amet eu consectetur imperdiet adipiscing in. Tempus consequat hendrerit amet.</p>
                        <div class="cta-buttons">
                            <a href="../pages/index.php" class="cta-btn cta-primary">Book Now</a>
                            <a href="../pages/contact.php" class="cta-btn cta-secondary">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reviews Section (no changes here) -->
        <section class="container-fluid reviews-section py-4">
            <div class="container">
                <h2 class="text-center mb-5">Reviews from out customer</h2>
                
                <!-- Swiper -->
                <div class="swiper reviewsSwiper">
                    <div class="swiper-wrapper">
                        <?php 
                        $reviews = [
                            [
                                'text' => 'Et eleifend velut at sapien pulvermusce mollis non dignissim Donec tincidunt dui at dui vulputate, feugis ac semper ante porttitor sit.',
                                'name' => 'Emanuel Ratie',
                                'image' => '../assets/images/about/dp1.png'
                            ],
                            [
                                'text' => 'Fusce consectetur varius quis orns effendus arnut hendre, bank montes iaculis nulla vivamus gott finces vulputate ligula.',
                                'name' => 'Rose Greene',
                                'image' => '../assets/images/about/dp2.png'
                            ],
                            [
                                'text' => 'Creen risque nibh ante euismond nibh, sit dignisse nullgrit sit judicabit semper duin et mattis wulp qui git performance.',
                                'name' => 'Taylor Kinsoe',
                                'image' => '../assets/images/about/dp3.png'
                            ],
                            [
                                'text' => 'Et eleifend velut at sapien pulvermusce mollis non dignissim Donec tincidunt dui at dui vulputate, feugis ac semper ante porttitor sit.',
                                'name' => 'Emanuel Ratie',
                                'image' => '../assets/images/about/dp1.png'
                            ],
                            [
                                'text' => 'Fusce consectetur varius quis orns effendus arnut hendre, bank montes iaculis nulla vivamus gott finces vulputate ligula.',
                                'name' => 'Rose Greene',
                                'image' => '../assets/images/about/dp2.png'
                            ],
                            [
                                'text' => 'Creen risque nibh ante euismond nibh, sit dignisse nullgrit sit judicabit semper duin et mattis wulp qui git performance.',
                                'name' => 'Taylor Kinsoe',
                                'image' => '../assets/images/about/dp3.png'
                            ],
                        ];

                        foreach($reviews as $review): ?>
                            <div class="swiper-slide">
                                <div class="card p-4">
                                    <div class="quote mb-3">
                                        <i class="fas fa-quote-left"></i>
                                    </div>
                                    <p class="text-muted mb-4"><?php echo $review['text']; ?></p>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 30px; height: 30px;">
                                            <img src="<?php echo $review['image']; ?>" alt="<?php echo $review['name']; ?>" class="rounded-circle w-100 h-10 object-fit-cover">
                                        </div>
                                        <div>
                                            <h5 class="mb-0"><?php echo $review['name']; ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script src="../assets/js/index.js"></script>
    <script src="../assets/js/currencyHandler.js"></script>
    <script>
        // Pass initial data to currencyHandler.js
        const phpLocations = <?php echo json_encode($locations); ?>;
        // The main currency handler will fetch actual rates
    </script>

    <?php displaySocialIcons($social_config); ?>

    <script>
        var swiper = new Swiper(".reviewsSwiper", {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    </script>
  </body>
</html>