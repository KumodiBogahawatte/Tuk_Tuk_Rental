<?php
include_once '../config/db_connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Validate the input
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Prepare SQL query
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $message]);

        // Execute query
        if ($stmt->execute()) {
            // Get the ID of the inserted contact message
            $contact_message_id = $pdo->lastInsertId();
            // Add notification for admin
            $notification_message = "$contact_message_id New contact message from $name.";
            $notification_sql = "INSERT INTO notifications (type, message) VALUES ('contact_message', ?)";
            $notification_stmt = $pdo->prepare($notification_sql);
            $notification_stmt->execute([$notification_message]);

            $success_message = "Your message has been sent successfully!";
        } else {
            $error_message = "Failed to send your message. Please try again.";
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/social_icons.php'; ?>
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
    <link rel="stylesheet" href="../assets/js/index.js">
    <?php getSocialIconsStyles(); ?>
  </head>
  <body>
    <div class="main-content">
    <!-- Header Section -->
    <header class="contact-header text-center">
        <div class="contact-header-content">
          <div class="container">
            <h1 class="display-5 fw-bold">Contact Us</h1>
            <p>Get in touch with our team for any questions or support</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center custom-breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ol>
            </nav>
          </div>
        </div>
    </header>
    <!-- <div class="contact-header">
        <div class="contact-header-content">
            <h1>Contact Us</h1>
            <p>Get in touch with our team for any questions or support</p>
            
            <nav aria-label="breadcrumb">
                <ol class="custom-breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ol>
            </nav>
        </div>
    </div> -->
    
    <!-- Main Content -->
    <div class="main-container">
        <div class="contact-layout">
            <!-- Form Section - Left Side, Wider -->
            <div class="contact-form-section" data-aos="fade-right">
                <div class="card-header">
                    <h4>Send us a Message</h4>
                    <p>We'd love to hear from you. Drop us a line anytime!</p>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div class="alert alert-success" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        Your message has been sent successfully!
                    </div>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control textarea" id="message" name="message" rows="6" placeholder="Write your message here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info Section - Right Side, 2x2 Grid -->
            <div class="contact-info-section" data-aos="fade-left">
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h6>Address</h6>
                    <p>123 Main Street<br>Colombo, Sri Lanka</p>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h6>Phone</h6>
                    <p>+94 77 123 4567</p>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h6>Email</h6>
                    <p>info@tukrental.com</p>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h6>Hours</h6>
                    <p>Mon - Sun<br>8:00 AM - 8:00 PM</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Blog Section -->
    <section class="blog-section">
        <h2>LATEST BLOG POSTS AND NEWS</h2>
        <div class="blog-row">
            <div class="blog-card">
                <img src="../assets/images/contact/blogImg-1.png" class="card-img-top" alt="Blog 1">
                <div class="card-body">
                    <h6>The Ultimate Sri Lanka Tuk Tuk Adventure Guide</h6>
                    <p class="card-text">News / January 15, 2025</p>
                    <a href="article-1.php">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="blog-card">
                <img src="../assets/images/contact/blogImg-2.png" class="card-img-top" alt="Blog 2">
                <div class="card-body">
                    <h6>Explore Sri Lanka by Tuk Tuk</h6>
                    <p class="card-text">News / May 12, 2025</p>
                    <a href="article-2.php">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- <div class="blog-card">
                <img src="../assets/images/contact/blogImg-3.png" class="card-img-top" alt="Blog 3">
                <div class="card-body">
                    <h6>Enjoy Speed, Choice & Total Control</h6>
                    <p class="card-text">News / 12 April 2024</p>
                    <a href="#">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="blog-card">
                <img src="../assets/images/contact/blogImg-4.png" class="card-img-top" alt="Blog 4">
                <div class="card-body">
                    <h6>Explore More with the Right Plan</h6>
                    <p class="card-text">News / 12 April 2024</p>
                    <a href="#">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div> -->
        </div>
    </section>
    
    <!-- Brand Section -->
    <section class="container py-5 vehicles-section">
        <div class="brand-list-section">
            <ul>
                <li><img src="../assets/images/Vehicles/brand-1.png" alt="Brand 1"></li>
                <li><img src="../assets/images/Vehicles/brand-2.png" alt="Brand 2"></li>
                <li><img src="../assets/images/Vehicles/brand-3.png" alt="Brand 3"></li>
            </ul>
        </div>
    </section>
</div>
    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Scripts -->
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
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Demo form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const successAlert = document.querySelector('.alert-success');
            successAlert.style.display = 'flex';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 3000);
        });
    </script>

    <?php 
    // Display the social media icons
    displaySocialIcons($social_config); 
    ?>
</body>
</html>