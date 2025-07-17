<?php
include_once '../config/db_connect.php';

$success_message = '';
$error_message = '';
$name = $email = $phone = $message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);

    // Validate input
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($message)) {
        $sql = "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $inserted = $stmt->execute([$name, $email, $phone, $message]);

        if ($inserted) {
            $contact_message_id = $pdo->lastInsertId();
            $notification_message = "$contact_message_id New contact message from $name (Phone: $phone).";
            $notification_sql = "INSERT INTO notifications (type, message) VALUES ('contact_message', ?)";
            $notification_stmt = $pdo->prepare($notification_sql);
            $notification_stmt->execute([$notification_message]);

            // Redirect to clear POST data and reset form
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } else {
            $error_message = "Failed to send your message. Please try again.";
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Show success message if redirected here with success=1
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Your message has been sent successfully!";
}

?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/social_icons.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tuk Tuk Rental - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="icon" type="image/x-icon" href="./favicon.ico" />
    <link rel="stylesheet" href="../assets/css/contact.css" />
    <link rel="stylesheet" href="../assets/css/footer.css" />
    <link rel="stylesheet" href="../assets/css/navbar.css" />
    <link rel="stylesheet" href="../assets/css/responsive.css" />
    <?php getSocialIconsStyles(); ?>
  </head>
  <body>
    <div class="main-content">
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

      <div class="main-container">
        <div class="contact-layout">
          <div class="contact-form-section" data-aos="fade-right">
            <div class="card-header">
              <h4>Send us a Message</h4>
              <p>We'd love to hear from you. Drop us a line anytime!</p>
            </div>
            <div class="card-body">
              <!-- Display success or error messages -->
              <?php if($success_message): ?>
                <div class="alert alert-success">
                  <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
                </div>
              <?php endif; ?>

              <?php if($error_message): ?>
                <div class="alert alert-danger">
                  <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                </div>
              <?php endif; ?>

              <form method="POST" action="" id="contact-form" novalidate>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="name" class="form-label">Full Name</label>
                      <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required value="<?= htmlspecialchars($name) ?>" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="email" class="form-label">Email Address</label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($email) ?>" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="phone" class="form-label">Phone Number</label>
                      <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required value="<?= htmlspecialchars($phone) ?>" />
                    </div>
                  </div>
                </div>
                <div class="form-group mb-3">
                  <label for="message" class="form-label">Message</label>
                  <textarea class="form-control textarea" id="message" name="message" rows="6" placeholder="Write your message here..." required><?= htmlspecialchars($message) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </form>
            </div>
          </div>

          <div class="contact-info-section" data-aos="fade-left">
            <div class="contact-info-item">
              <div class="contact-info-icon">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <h6>Address</h6>
              <p>123 Main Street<br />Colombo, Sri Lanka</p>
            </div>

            <div class="contact-info-item">
              <div class="contact-info-icon">
                <i class="fas fa-phone-alt"></i>
              </div>
              <h6>Phone</h6>
              <p>+94 766 053 060</p>
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
              <p>Mon - Sun<br />8:00 AM - 8:00 PM</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Blog Section -->
      <section class="blog-section">
        <h2>LATEST BLOG POSTS AND NEWS</h2>
        <div class="blog-row">
          <div class="blog-card">
            <img src="../assets/images/contact/blogImg-1.png" class="card-img-top" alt="Blog 1" />
            <div class="card-body">
              <h6>The Ultimate Sri Lanka Tuk Tuk Adventure Guide</h6>
              <p class="card-text">News / January 15, 2025</p>
              <a href="article-1.php">Read More <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="blog-card">
            <img src="../assets/images/contact/blogImg-2.png" class="card-img-top" alt="Blog 2" />
            <div class="card-body">
              <h6>Explore Sri Lanka by Tuk Tuk</h6>
              <p class="card-text">News / May 12, 2025</p>
              <a href="article-2.php">Read More <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="blog-card">
            <img src="../assets/images/contact/blogImg-3.png" class="card-img-top" alt="Blog 3" />
            <div class="card-body">
              <h6>Enjoy Speed, Choice & Total Control</h6>
              <p class="card-text">News / 12 April 2024</p>
              <a href="#">Read More <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="blog-card">
            <img src="../assets/images/contact/blogImg-4.png" class="card-img-top" alt="Blog 4" />
            <div class="card-body">
              <h6>Explore More with the Right Plan</h6>
              <p class="card-text">News / 12 April 2024</p>
              <a href="#">Read More <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </section>

      <!-- Brand Section -->
      <section class="container py-5 vehicles-section">
        <div class="brand-list-section">
          <ul>
            <li><img src="../assets/images/Vehicles/brand-1.png" alt="Brand 1" /></li>
            <li><img src="../assets/images/Vehicles/brand-2.png" alt="Brand 2" /></li>
            <li><img src="../assets/images/Vehicles/brand-3.png" alt="Brand 3" /></li>
          </ul>
        </div>
      </section>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="../assets/js/index.js"></script>
    <script>
      AOS.init();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
      flatpickr(".date-input", {
        dateFormat: "d/m/Y",
      });
    </script>

    <?php
    // Display the social media icons
    displaySocialIcons($social_config);
    ?>
  </body>
</html>
