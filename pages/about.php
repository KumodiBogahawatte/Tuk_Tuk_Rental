<?php
// Sample PHP data that would typically come from a database
$stats = [
    'happy_customers' => '20k+',
    'tuk_count' => '540+',
    'years_experience' => '25+'
];

$faqs = [
    [
        'question' => 'What areas are covered by your insurance?',
        'answer' => 'Our tuk-tuks come with comprehensive insurance that covers:
            •	The vehicle itself
            •	The driver and passengers
            •	Third-party damages
            We also recommend that you get your own travel insurance for added personal coverage.'
    ],
    [
        'question' => 'What’s included in the tuk-tuk rental package?',
        'answer' => '•	Well-maintained tuk-tuk
        •	Local Sri Lankan driving permit
        •	Comprehensive third-party insurance
        •	Full driving lesson upon arrival
        •	Unlimited kilometers
        •	Flexible pick-up and drop-off locations
        •	24/7 customer support
        •	Digital navigation tools (Google Maps compatible)'
    ],
    [
        'question' => 'What are the requirements for renting a tuk?',
        'answer' => 'Absolutely! Just inform us at least 48 hours in advance and we’ll make arrangements. A small relocation fee may apply depending on the distance.'
    ],
    [
        'question' => 'What happens if the tuk-tuk is stolen or hijacked?',
        'answer' => 'Although rare in Sri Lanka, if this happens:
        •	Insurance covers theft under most scenarios
        •	If the keys were left inside the tuk-tuk, coverage may be limited
        •	Always park safely and take basic precautions'
    ],
    [
        'question' => 'What if my personal items are stolen during the trip?',
        'answer' => 'Please notify us immediately — we’ll assist you in reporting the issue to local police. For lost travel documents, insurance usually offers coverage. Note: replacing the tuk-tuk’s license and documents may cost around $90.'
    ],
    [
        'question' => 'Can I pay with a credit card?',
        'answer' => 'Yes! We accept credit and debit card payments via secure gateways like Stripe and PayPal.'
    ],
    [
        'question' => 'Do I need to make an advance payment to book?',
        'answer' => 'Yes, a small deposit is required to secure your tuk-tuk and allow us to begin the licensing process and trip arrangements.'
    ],
    [
        'question' => 'Will I get a refund if I cancel my booking?',
        'answer' => 'Please refer to our Rental Terms Summary for full cancellation and refund policy details.'
    ]
];
function renderFaqAnswer($answer) {
    // Split answer into lines
    $lines = preg_split('/\r\n|\r|\n/', $answer);
    $inList = false;
    foreach ($lines as $line) {
        $trimmed = trim($line);
        // Check if line starts with a bullet
        if (preg_match('/^(•|-)/u', $trimmed)) {
            if (!$inList) {
                echo '<ul>';
                $inList = true;
            }
            // Remove bullet and whitespace
            $text = trim(mb_substr($trimmed, 1));
            echo '<li>' . htmlspecialchars($text) . '</li>';
        } else {
            if ($inList) {
                echo '</ul>';
                $inList = false;
            }
            if ($trimmed !== '') {
                echo '<p>' . htmlspecialchars($trimmed) . '</p>';
            }
        }
    }
    if ($inList) {
        echo '</ul>';
    }
}

// $reviews = [
//     [
//         'text' => 'Et eleifend velut at sapien pulvermusce mollis non dignissim Donec tincidunt dui at dui vulputate, feugis ac semper ante porttitor sit.',
//         'name' => 'Emanuel Ratie',
//         'image' => '../assets/images/about/dp1.png'
//     ],
//     [
//         'text' => 'Fusce consectetur varius quis orns effendus arnut hendre, bank montes iaculis nulla vivamus gott finces vulputate ligula.',
//         'name' => 'Rose Greene',
//         'image' => '../assets/images/about/dp2.png'
//     ],
//     [
//         'text' => 'Creen risque nibh ante euismond nibh, sit dignisse nullgrit sit judicabit semper duin et mattis wulp qui git performance.',
//         'name' => 'Taylor Kinsoe',
//         'image' => '../assets/images/about/dp3.png'
//     ]
// ];
// ?>

<?php include '../includes/navbar.php'; ?>
<?php include '../includes/social_icons.php'; ?>

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
    <!-- Add Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/about.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <?php getSocialIconsStyles(); ?>
  </head>
  <body>

    <!-- Header Section -->
    <header class="about-header">
        <div class="about-header-content">
            <div class="container">
                <h1 class="display-5 fw-bold">About Us</h1>
                <p>We’re passionate about making your travel experience safe, affordable, and memorable with every tuk tuk ride.</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center custom-breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">About Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>

    <!-- About Experience Section -->
    <section class="about-experience-section py-5">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-15">
                <div class="about-experience-box p-3 p-md-4">
                <h2 class="mb-3 fw-bold" style="font-size:2.5rem; font-size:clamp(1.5rem, 6vw, 2.5rem);">Discover Sri Lanka from behind the wheel of the country’s most iconic vehicle — the tuk-tuk!
                </h2>
                <p>We are a small business in Sri Lanka. To be more precise, this is our family business. We started this business because we believed that in addition to the income that tourism brings to Sri Lanka, we can make an impactful difference in Sri Lanka. Our business has also been able to provide additional income sources to many small entrepreneurs.
                            All the entrepreneurs who have gathered around our business are small-scale entrepreneurs. 
                            We are sincerely happy about the economic strength this gives them.</p>
        
                        <P>There are currently at least 1.5 million tuk-tuks plying on the roads of Sri Lanka. Most of these tuk-tuks have been purchased on credit. They have to pay a large monthly fee for them. But it is quite difficult to pay such a large monthly fee based on their income. 
                        They also have to maintain their daily lives with the money they earn from driving tuk-tuks. This is a big economic struggle.</P>
                        
                        <p>We believe that all of this can have a small impact or help. If so, join us in this effort.</p>     
                </div>
            </div>
        </div>
    </div>
    </section>

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
            
            <!-- <div class="header-image mb-5">
                <img src="../assets/images/about/ColorfulTuk.png" alt="Electric Vehicle" class="img-fluid">
            </div> -->
            
            <!-- Stats -->
            <div class="row text-center mb-5">
                <?php foreach($stats as $key => $value): ?>
                <div class="col-md-4 mb-4 mb-md-0" data-aos="zoom-in" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
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


    <!-- Gallery Section -->
    <section class="section gallery-about">
        <div class="gallery-flickity" id="gallery-scroll">
            <?php
            $gallery_images = array(
                "../assets/images/about/gallery/gallery-1.jpg",
                "../assets/images/about/gallery/gallery-2.jpg",
                "../assets/images/about/gallery/gallery-3.jpg",
                "../assets/images/about/gallery/gallery-4.jpg",
                "../assets/images/about/gallery/gallery-5.jpg",
                "../assets/images/about/gallery/gallery-6.jpg",
                "../assets/images/about/gallery/gallery-7.jpg",
                "../assets/images/about/gallery/gallery-8.jpg",
                "../assets/images/about/gallery/gallery-9.jpg",
                "../assets/images/about/gallery/gallery-10.jpg",
                "../assets/images/about/gallery/gallery-11.jpg",
                "../assets/images/about/gallery/gallery-12.jpg"
            );
            foreach ($gallery_images as $image): ?>
                <div class="gallery-cell">
                    <img alt="" src="<?php echo $image; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
        <a href="gallery.php" class="btn btn-primary mb-4 rounded-pill px-4">See More Photos <i class="fas fa-arrow-right"></i></a>
    </div>
    </section>
    
    <!-- Memories Section -->
        <section class="Memories py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="headline mb-4">UNLOCK UNFORGETTABLE MEMORIES ON THE ROAD</h2>
                        <p class="text-muted mb-4">When you book with TukTukSLRental.net, you're choosing a service that puts your safety, comfort, and community impact first. Here’s why hundreds of travelers choose us every year:</p>
                        
                        <div class="d-flex mb-3">
                            <div class="feature-icon me-3" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h5 data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">Safety First</h5>
                                <p class="text-muted" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">All our tuk-tuks are road-tested, regularly maintained, and come with comprehensive insurance. Plus, you’ll receive a personal driving lesson before hitting the road.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="feature-icon me-3"data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h5 data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">24/7 Emergency Support</h5>
                                <p class="text-muted" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">Need more than one tuk? No problem, book as many as you need.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-3">
                            <div class="feature-icon me-3" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">Free Attractions Map & Local Tips</h5>
                                <p class="text-muted" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">Get insider access to hidden gems, scenic routes, and local dining spots. We don’t just give you a vehicle; we help you explore smarter.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex">
                            <div class="feature-icon me-3" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
                                <i class="fas fa-car"></i>
                            </div>
                            <div>
                                <h5 data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">Driving Lessons & Luggage Advice</h5>
                                <p class="text-muted" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">We’ll teach you how to handle a tuk-tuk like a local — including tips on navigating traffic, carrying your gear, and staying safe on the road.</p>
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-lg-6">-->
                    <!--    <img src="./assets/images/about/happy.png" alt="Happy Customer" class="img-fluid rounded" style="width: 1250px;">-->
                    <!--</div>-->
                </div>
            </div>
        </section>
    
    <!-- App Download Section -->
    <section class="download-section py-4">
        <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
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
    </section>
    
    <!-- Reviews Section -->
    <section class="reviews-section py-4">
        <div class="container">
            <h2 class="text-center mb-5">REVIEWS FROM OUR CUSTOMER</h2>
            
            <!-- Swiper -->
            <div class="swiper reviewsSwiper">
                <div class="swiper-wrapper">
                    <?php 
                    // Add more reviews here
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
                        // Add more reviews as needed
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
    
    <!-- FAQ Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-5">Frequently Asked Questions (FAQ)</h2>
            <div class="accordion" id="rentalFAQ">
                <?php foreach($faqs as $index => $faq): ?>
                <div class="accordion-item mb-3 border" data-aos="fade-up" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                        <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
                            <?php echo $faq['question']; ?>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#rentalFAQ">
                        <div class="accordion-body">
                            <?php renderFaqAnswer($faq['answer']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Looking for a tuk Section -->
    <section class="looking-section py-5">
        <div class="container">
            <div class="p-4">
                <div class="row align-items-center text-center text-lg-start"
                    data-aos="zoom-in" data-aos-delay="100" data-aos-mirror="true" data-aos-once="false">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <h2 class="mb-3">Looking for a tuk?</h2>
                        <div class="d-flex justify-content-center justify-content-lg-start align-items-center mb-3">
                            <span class="me-2">+94 755 555 555</span>
                        </div>
                        <p class="mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita, libero dignissimos. Expedita, libero dignissimos.</p>
                        <a href="../pages/index.php" class="btn btn-warning rounded-pill px-4 book-now-btn">Book Now <i class="fa fa-arrow-right"></i></a>
                    </div>
                    <div class="col-lg-4">
                        <img src="../assets/images/about/tuk.png" alt="Colorful Tuk Tuk Vehicle"
                            class="img-fluid rounded mx-auto d-block">
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

    <?php 
    // Display the social media icons
    displaySocialIcons($social_config); 
    ?>
    <!-- Add Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

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
<script src="../assets/js/about.js"></script>
  </body>
</html>