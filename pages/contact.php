<!-- Main landing page -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AOS Library CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/contact.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
  </head>
  <body>
    <?php include '../includes/navbar.php'; ?>
    <!--Hero Section-->
    <section class="container-fluid py-5 hero-section">
        <div class="container">
            <div class="text-center">
                <h1 class="fw-bold">Contact Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color: gray;">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #5937E0;font-weight: 500;">Contact Us</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="booking-form" data-aos="fade-right">
                        <h4 class="mb-4">Book your ThreeWheel</h4>
                        <form>
                            <p class="form-label-title" style="color: #fff;font-weight: 500;margin-bottom: 10px; margin-left:1px;"><i class="fa-solid fa-location-dot me-2"></i>Pick-Up Information</p>
                            <select class="form-select mb-3">
                                <option selected>Place of pickup</option>
                                <option>Maharagama</option>
                                <option>Kottawa</option>
                                <option>Homagama</option>
                            </select>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 date-input" placeholder="Rental Date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-clock"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 time-input" placeholder="Select Time">
                                    </div>
                                </div>
                            </div>
                            <p class="form-label-title" style="color: #fff;font-weight: 500;margin-bottom: 10px; margin-left:1px;"><i class="fa-solid fa-flag-checkered me-2"></i>Return Information</p>
                            <select class="form-select mb-3">
                                <option selected>Place of return</option>
                                <option>Maharagama</option>
                                <option>Kottawa</option>
                                <option>Homagama</option>
                            </select>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 date-input" placeholder="Return Date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-regular fa-clock"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 time-input" placeholder="Select Time">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn booking-btn w-100">Search</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-left">
                    <img src="../assets/images/details/tuktuk.png" alt="tuktuk" class="mb-3" width="100%">
                </div>
            </div>
        </div>
    </section>

    <section class="contact-info-section">
        <div class="contact-info-row">
            <div class="contact-info-box">
            <i class="fas fa-map-marker-alt"></i>
            <div class="contact-info-text">
                <h6>Address</h6>
                <p>123 Main Street, Colombo, Sri Lanka</p>
            </div>
            </div>
            <div class="contact-info-box">
            <i class="fas fa-envelope"></i>
            <div class="contact-info-text">
                <h6>Email</h6>
                <p>info@tukrental.com</p>
            </div>
            </div>
            <div class="contact-info-box">
            <i class="fas fa-phone-alt"></i>
            <div class="contact-info-text">
                <h6>Phone</h6>
                <p>+94 77 123 4567</p>
            </div>
            </div>
            <div class="contact-info-box">
            <i class="fas fa-clock"></i>
            <div class="contact-info-text">
                <h6>Opening Hours</h6>
                <p>Mon - Sun: 8am - 8pm</p>
            </div>
            </div>
        </div>
    </section>
    
    <section class="blog-section">
        <h2>Latest blog posts & news</h2>
        <div class="blog-row">
            <div class="blog-card" style="width: 18rem;">
                <img src="../assets/images/contact/blogImg-1.png" class="card-img-top" alt="blogImg-1">
                <div class="card-body">
                    <h6>How to choose the right Tuk</h6>
                    <p class="card-text">News / 12April 2024</p>
                </div>
            </div>

            <div class="blog-card" style="width: 18rem;">
                <img src="../assets/images/contact/blogImg-2.png" class="card-img-top" alt="blogImg-2">
                <div class="card-body">
                    <h6>Which plan is right for me?</h6>
                    <p class="card-text">News / 12April 2024</p>
                </div>
            </div>

            <div class="blog-card" style="width: 18rem;">
                <img src="../assets/images/contact/blogImg-3.png" class="card-img-top" alt="blogImg-3">
                <div class="card-body">
                    <h6>Enjoy Speed, Choice & Total Control</h6>
                    <p class="card-text">News / 12April 2024</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="container py-5 vehicles-section vehicles-page">
        <div class="brand-list-section">
            <ul>
                <li><img src="../assets/images/Vehicles/brand-1.png" alt="brand-1"></li>
                <li><img src="../assets/images/Vehicles/brand-3.png" alt="brand-3"></li>
                <li><img src="../assets/images/Vehicles/brand-2.png" alt="brand-2"></li>
            </ul>
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
            dateFormat: "d/m/Y",
        });
    </script>
    <script>
        flatpickr(".time-input", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false
        });
    </script>

  </body>
</html>