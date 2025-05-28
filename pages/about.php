<?php
// Sample PHP data that would typically come from a database
$stats = [
    'happy_customers' => '20k+',
    'tuk_count' => '540+',
    'years_experience' => '25+'
];

$faqs = [
    [
        'question' => 'How does it work?',
        'answer' => 'Important: at Tuk Tuk vehicle rents, vehicles will not accept excess funds with payment freeze. Premium rents of at all in buckle amount also hardware profiles opportunities or confirmation. transport check Distributive only digital allowed vehicle accessories made for hire.'
    ],
    [
        'question' => 'Can I rent a tuk without a credit card?',
        'answer' => 'Yes, we offer alternative payment methods. Please contact our customer service for more details.'
    ],
    [
        'question' => 'What are the requirements for renting a tuk?',
        'answer' => 'You must be at least 21 years old, have a valid driver\'s license, and provide a security deposit.'
    ],
    [
        'question' => 'Does tuk Rental allow me to tow with or attach a hitch to the rental tuk?',
        'answer' => 'This depends on the tuk type. Please contact us for specific information regarding the tuk you wish to rent.'
    ],
    [
        'question' => 'Does tuk Rental offer coverage products for purchase with my rental?',
        'answer' => 'Yes, we offer various insurance options to ensure your peace of mind during the rental period.'
    ]
];

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
    ]
];
?>

<!-- Main landing page -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/about.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>
    <!-- Breadcrumb -->
    <div class="breadcrumb-section py-3">
            <div class="container">
            <div class="text-center">
                <h1 class="fw-bold">About Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none" style="color: gray;">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #375FE0;font-weight: 500;">About Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="main_content py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <h2 class="headline mb-4">Where every drive feels extraordinary</h2>
                    <p class="text-muted">Experience the freedom of the open road with our diverse fleet of vehicles. From compact city cars to luxury SUVs, we have something for every journey.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card p-4">
                                <h4>Variety Brands</h4>
                                <p class="mb-0 text-muted">Select from quality Handpicked, premium, high performance lineup of each notable rental market worldwide brands.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card p-4">
                                <h4>Awesome Support</h4>
                                <p class="mb-0 text-muted">Our customer support team is available 24/7 to assist you with any questions or concerns you may have.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card p-4">
                                <h4>Maximum Freedom</h4>
                                <p class="mb-0 text-muted">Lorem ipsum prototex attributes with free consistent designed as structured advanced are important to handle.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card p-4">
                                <h4>Flexibility On The Go</h4>
                                <p class="mb-0 text-muted">Total premium calix rent ipsum to test verbage, tuki and utilize optimum as discrete response, suitable.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-image mb-5">
                <img src="../assets/images/about/ColorfulTuk.png" alt="Electric Vehicle" class="img-fluid">
            </div>
            
            <!-- Stats -->
            <div class="row text-center mb-5">
                <?php foreach($stats as $key => $value): ?>
                <div class="col-md-4 mb-4 mb-md-0" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stats-number"><?php echo $value; ?></div>
                    <div class="stats-text">
                        <?php 
                        switch($key) {
                            case 'happy_customers':
                                echo 'HAPPY CUSTOMERS';
                                break;
                            case 'tuk_count':
                                echo 'COUNT OF TUKS';
                                break;
                            case 'years_experience':
                                echo 'YEAR OF EXPERIENCE';
                                break;
                        }
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Memories Section -->
    <section class="Memories py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="headline mb-4">Unlock unforgettable memories on the road</h2>
                    <p class="text-muted mb-4">Lorem, ipsum dolor sit amet consectetur. Sed suscipit sit velit amet faucibus a porttitor semper hendrerit. Proin dui elit vehicula a.</p>
                    
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3" data-aos="fade-up" data-aos-delay="100">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h5 data-aos="fade-up" data-aos-delay="100">24/7 customer support</h5>
                            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Our team is available for immediate assistance at any time.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3"data-aos="fade-up" data-aos-delay="100">
                            <i class="fas fa-car"></i>
                        </div>
                        <div>
                            <h5 data-aos="fade-up" data-aos-delay="100">Multiple tuk booking possible</h5>
                            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Need more than one tuk? No problem, book as many as you need.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3" data-aos="fade-up" data-aos-delay="100">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 data-aos="fade-up" data-aos-delay="100">Delivery, return and pickup</h5>
                            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">We offer convenient delivery and pickup services for your rental.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="feature-icon me-3" data-aos="fade-up" data-aos-delay="100">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h5 data-aos="fade-up" data-aos-delay="100">Security deposit guides</h5>
                            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Clear information about security deposits and how they work.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="../assets/images/about/happy.png" alt="Happy Customer" class="img-fluid rounded" style="width: 1250px;">
                </div>
            </div>
        </div>
    </section>
    
    <!-- App Download Section -->
    <section class="py-4">
    <div class="container">
        <div class="purple-bg">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-5 mb-4 mb-md-0">
                    <img src="../assets/images/home/mobileApp.png" alt="Mobile App" class="img-fluid">
                </div>
                <div class="col-lg-8 col-md-7">
                    <h2>Download our app</h2>
                    <p>Fusce minim ipsum nibh previous dos tan duris. Transform digitalize remover dolores meti sauis inem duos cultivate malesuada vehicula vis veneris ad finibus augue.</p>
                    <div class="store-buttons">
                        <a href="#" class="store-btn app-store">
                            <i class="fa-brands fa-apple store-icon me-2"></i>
                            <span>App Store</span>
                        </a>
                        <a href="#" class="store-btn play-store">
                            <i class="fab fa-google-play store-icon me-2"></i>
                            <span>Google Play</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
    <!-- Reviews Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Reviews from our customers</h2>
            <div class="row">
            <?php foreach($reviews as $review): 
                ?>
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 h-100">
                        <div class="quote mb-3">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="text-muted mb-4"><?php echo $review['text']; ?></p>
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 50px; height: 50px;">
                                <img src="<?php echo $review['image']; ?>" alt="<?php echo $review['name']; ?>" class="rounded-circle w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h5 class="mb-0"><?php echo $review['name']; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </section>
    
    <!-- FAQ Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-5">Top Tuk Rental Questions</h2>
            <div class="accordion" id="rentalFAQ">
                <?php foreach($faqs as $index => $faq): ?>
                <div class="accordion-item mb-3 border">
                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                        <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
                            <?php echo $faq['question']; ?>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#rentalFAQ">
                        <div class="accordion-body">
                            <?php echo $faq['answer']; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Looking for a car Section -->
    <section class="py-5" style="background-color:rgba(215, 228, 238, 0.95);">
        <div class="container">
            <div class="p-5">
                <div class="row align-items-center"  data-aos="zoom-in" data-aos-delay="100">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <h2 class="mb-3" style="font-size: 60px;font-weight:bold;margin-left:20px;">Looking for a tuk?</h2>
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-2" style="font-size: 30px;font-weight:bold;margin-left:20px;">+94 755 555 555</span>
                        </div>
                        <p class="mb-4" style="margin-left:20px;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita, libero dignissimos.Expedita, libero dignissimos.</p>
                        <a href="../pages/index.php" class="btn btn-warning rounded-pill px-4 book-now-btn" style="background-color: #FF9E0C;margin-left:20px;">Book Now</a>
                    </div>
                    <div class="col-lg-4"  data-aos="zoom-in" data-aos-delay="100">
                        <img src="../assets/images/about/tuk.png" alt="Colorful Tuk Tuk Vehicle" class="img-fluid rounded">
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