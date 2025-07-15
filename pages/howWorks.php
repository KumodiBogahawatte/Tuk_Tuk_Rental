<?php
include_once '../config/db_connect.php';

// Handle meeting booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_meeting'])) {
    $meeting_time = $_POST['meeting_time'];
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    
    if ($meeting_time) {
        // Insert meeting booking
        $sql = "INSERT INTO meeting_bookings (meeting_time, customer_name, customer_email, customer_phone, type, status) VALUES (?, ?, ?, ?, 'trip_planning', 'pending')";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$meeting_time, $customer_name, $customer_email, $customer_phone])) {
            $meeting_id = $pdo->lastInsertId();
            
            // Add notification for admin
            $notification_message = "New meeting booking #$meeting_id for trip planning.";
            $notification_sql = "INSERT INTO notifications (type, message) VALUES ('meeting_booking', ?)";
            $notification_stmt = $pdo->prepare($notification_sql);
            $notification_stmt->execute([$notification_message]);
            
            $meeting_success = "Meeting booked successfully! You will receive a confirmation email with Zoom/Teams link.";
        } else {
            $meeting_error = "Failed to book meeting. Please try again.";
        }
    } else {
        $meeting_error = "Please select a date and time for your meeting.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tuk Tuk Rental - How It Works</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="../assets/css/howWorks.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <?php 
    if (file_exists('../includes/social_icons.php')) {
        include '../includes/social_icons.php';
        getSocialIconsStyles();
    }
    ?>
</head>
<body>
    <?php 
    if (file_exists('../includes/navbar.php')) {
        include '../includes/navbar.php';
    }
    ?>
    
    <div class="main-content">
        <!-- Header Section -->
        <header class="how-it-works-header text-center">
            <div class="how-it-works-header-content">
                <div class="container">
                    <h1 class="display-5 fw-bold">How It Works</h1>
                    <p>The latest way to travel around Sri Lanka in style. Hire a tuk-tuk and drive it yourself as an expert</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center custom-breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">How It Works</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <div class="main-container">
            
            <!-- How It Functions Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2 class="section-title">How it functions</h2>
                            <div class="dropdown-container">
                                <button class="dropdown-btn" onclick="toggleDropdown('functions-dropdown')">
                                    How it functions <i class="fas fa-chevron-down"></i>
                                </button>
                                <div id="functions-dropdown" class="dropdown-content">
                                    <div class="content-box">
                                        <p>Let's first look at what you need to do to drive a tuk-tuk.</p>
                                        <p>Driving a tuk-tuk in Sri Lanka is very easy if you have a valid driving license. We're here to make it even easier and happier. Here's how.</p>
                                        
                                        <div class="step-card">
                                            <h3>Book a tuk-tuk from our <span class="highlight">tuktukslrental.com</span> site</h3>
                                            <p>First, confirm your trip by entering the start date and location, as well as the end date and location, and then select the type of tuk-tuk you want and additional options.</p>
                                            <a href="vehicles.php" class="btn btn-primary">Book here</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Additional Facilities Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Need additional facilities?</h2>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="facility-card">
                                <div class="facility-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5>Security</h5>
                                <p>Do you have valuables? We'll provide security for them.</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="facility-card">
                                <div class="facility-icon">
                                    <i class="fas fa-water"></i>
                                </div>
                                <h5>Surf Equipment</h5>
                                <p>Do you have any surfing equipment? We have surf racks.</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="facility-card">
                                <div class="facility-icon">
                                    <i class="fas fa-baby"></i>
                                </div>
                                <h5>Baby Seats</h5>
                                <p>Do you have small children? We'll provide baby seats.</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="facility-card">
                                <div class="facility-icon">
                                    <i class="fas fa-bluetooth-b"></i>
                                </div>
                                <h5>Tech Accessories</h5>
                                <p>Do you need Bluetooth speakers / phone charging facilities / coolers, sure … we are here to provide all your needs?</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Facilities Details -->
                    <div class="row mt-5">
                        <div class="col-lg-12">
                            <h3 class="mb-4">Additional facilities</h3>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="facility-detail-card">
                                        <div class="facility-detail-icon">
                                            <i class="fas fa-baby"></i>
                                        </div>
                                        <div class="facility-detail-content">
                                            <h4>Baby Seat</h4>
                                            <p>Carry your little ones comfortably and safely.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="facility-detail-card">
                                        <div class="facility-detail-icon">
                                            <i class="fas fa-water"></i>
                                        </div>
                                        <div class="facility-detail-content">
                                            <h4>Surfboard Rack</h4>
                                            <p>The easiest way to carry your surfboard safely.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="facility-detail-card">
                                        <div class="facility-detail-icon">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <div class="facility-detail-content">
                                            <h4>Seat Belt</h4>
                                            <p>This ensures your safety even better.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="facility-detail-card">
                                        <div class="facility-detail-icon">
                                            <i class="fas fa-bluetooth-b"></i>
                                        </div>
                                        <div class="facility-detail-content">
                                            <h4>Bluetooth Speaker</h4>
                                            <p>The way to listen to your favorite music while traveling.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="facility-detail-card">
                                        <div class="facility-detail-icon">
                                            <i class="fas fa-snowflake"></i>
                                        </div>
                                        <div class="facility-detail-content">
                                            <h4>Cooler/ Esky</h4>
                                            <p>The easiest way to keep your chilled.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="facility-detail-card">
                                        <div class="facility-detail-icon">
                                            <i class="fas fa-train"></i>
                                        </div>
                                        <div class="facility-detail-content">
                                            <h4>Train Transfer</h4>
                                            <p>Travel by train without having to carry your bags. We'll carry your bags.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Trip Planning Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">We will plan your trip</h2>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="planning-content">
                                <p>If you want to see beautiful places in Sri Lanka, tuktukslrental.net can plan your trip. We will teach you how to see more places in less time, for free. All you have to do is book a tuk tuk ride with us.</p>
                                
                                <h4>How to Plan a Trip</h4>
                                <p>Sri Lanka is a beautiful country. If you are planning to visit Sri Lanka for a few weeks or a month, make sure to make the most of your time. Decide in advance on the places you want to visit and make a list of them. If you need help with this, talk to our team. We are ready to arrange a free tour session for you. Tell us what you need. tuktukslrental.net service is open 24 hours a day for you.</p>
                                
                                <!-- Guidebook Button -->
                                <button type="button" class="guidebook-trigger btn btn-primary" id="open-guidebook">
                                    <i class="fas fa-book"></i> View Tuk Tuk Guidebook
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="meeting-book">
                                <h4>Book a meeting with us</h4>
                                <div class="meeting-details">
                                    <h5>Tuk tuk trip planning with us.</h5>
                                    <p><i class="fas fa-clock"></i> 30 Minutes</p>
                                    <p><i class="fas fa-video"></i> Conference Details (Zoom/Teams)</p>
                                    <p><i class="fas fa-calendar-alt"></i> Select Date & Time</p>
                                    
                                    <!-- Success/Error Messages -->
                                    <?php if (isset($meeting_success)): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> <?php echo $meeting_success; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($meeting_error)): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i> <?php echo $meeting_error; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="">
                                        <input type="hidden" name="book_meeting" value="1">
                                        <div class="form-group mb-3">
                                            <input type="text" class="form-control" name="customer_name" placeholder="Your Name" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="email" class="form-control" name="customer_email" placeholder="Your Email" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="tel" class="form-control" name="customer_phone" placeholder="Your Phone" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="datetime-local" class="form-control" name="meeting_time"  placeholder="Meeting Date & Time" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Book Meeting</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Local License Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">How to get a local license to drive a tuk-tuk</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="license-info">
                                <p style="text-align: center;">You are few steps behind to collect your local license. A foreigner needs a local license to drive a vehicle in Sri Lanka. Don't worry we're here. As soon as you book your tuk tuk trip, send us a clear photo of your driver's license and passport. We'll take care of the rest.</p>
                                
                                <div class="info-box">
                                    <h4>More details</h4>
                                    <p>To get an International Driving Permit (IDP) in Sri Lanka, you need to visit the Automobile Association of Ceylon (AAC). You have to bring your national driving license and two passport-size photos, then complete the application form and pay the applicable fee.</p>
                                    
                                    <div class="legal-requirements">
                                        <h5>*Conversion of Foreign Driving License into Sri Lankan Driving License</h5>
                                        <p>Procedure for issuing a Sri Lankan driving license to a holder of driving license issued outside of Sri Lanka, under the provisions of section 131 of Motor Traffic Act.</p>
                                        
                                        <h6>01. Common Considerations</h6>
                                        <ul>
                                            <li>a. The driving license produced for the conversion must be issued by one of the countries signatories to the Vienna convention of 1968 or Geneva convention of 1949 listed in schedule 1, or a SAARC country listed in schedule 2</li>
                                            <li>b. The driving license produced for the conversion (foreign license) must be valid for at least one year from the date of conversion.</li>
                                            <li>c. Only full driving license are considered for conversion. Training/learner's license/permit, novice license, temporary license, probationary license or other similar types of licenses are not considered for conversion.</li>
                                            <li>d. Conversion is applied for motorcycles vehicle class (denoted by A in Sri Lankan license) and light vehicles class (denoted by B in Sri Lankan license) only.</li>
                                            <li>e. All restrictions and conditions applied in the foreign license (e.g. corrective lens user, automatic transmission only, hearing aid user, etc.) will be applied in the converted Sri Lankan license.</li>
                                        </ul>
                                        
                                        <h6>02. Conversion of Foreign Driving License for Foreigners</h6>
                                        <p>All conditions mentioned in "01. Common Considerations" and following conditions are applicable.</p>
                                        <ul>
                                            <li>i. Sri Lankan visa for at least one-year duration.</li>
                                            <li>ii. Certificate to confirm mental and physical fitness, issued by the National Transport Medical Institute.</li>
                                            <li>iii. Passing the practical examination conducted by an Examiner of Motor Vehicles.</li>
                                            <li>iv. If the driving license is not issued by the home country of the applicant, visa for the country which issued the license (validity of the visa must overlap the validity of the driving license).</li>
                                            <li>v. Validity period of the Sri Lankan driving license must be decided based on the remaining Sri Lankan visa of the applicant.</li>
                                        </ul>
                                        
                                        <h6>03. Conversion of Foreign Driving License for Sri Lankans/Sri Lankan Dual Citizens</h6>
                                        <p>All conditions mentioned in "01. Common Considerations" and following conditions are applicable.</p>
                                        <ul>
                                            <li>i. Valid Sri Lankan passport and Sri Lankan national identity card (Sri Lankan citizenship).</li>
                                            <li>ii. Certificate to confirm mental and physical fitness, issued by the National Transport Medical Institute.</li>
                                            <li>iii. Passing the practical examination conducted by an Examiner of Motor Vehicles.</li>
                                            <li>iv. Visa for the country which issued the driving license (validity of the visa must overlap the validity of the driving license).</li>
                                        </ul>
                                        
                                        <h6>04. Issuing a temporary Sri Lankan driving license under the provisions of section 132 of Motor Traffic Act</h6>
                                        <p>A temporary driving license shall be issued as per the section 132 of Motor Traffic Act, to foreign national applicants who does not meet the requirement in paragraph "02. i.". All conditions from "b." to "g." in "01. Common Considerations", as well as following conditions are applicable in issuing a temporary driving license to a foreign national visiting Sri Lanka.</p>
                                        <ul>
                                            <li>i. The maximum validity of the temporary driving license will the lesser period from 5-month validity or remaining Sri Lankan visa period of the applicant.</li>
                                            <li>ii. Fee is applied based on the number of months of validity of the temporary license.</li>
                                        </ul>
                                        
                                        <h6>05. Exceptions</h6>
                                        <ul>
                                            <li>a. Sri Lankan driving license will be issued to foreign diplomats and staff members of foreign missions in Sri Lanka and without considering aforementioned conditions, according to the written requests from the Ministry of Foreign Affairs, Sri Lanka.</li>
                                            <li>b. For citizens of countries which has signed reciprocal driving license exchange agreements, Sri Lankan driving licenses will be issued according to the provisions of relevant agreements.</li>
                                            <li>c. In occasions where significant justifications are produced for converting the heavy vehicle classes of foreign driving licenses, consideration is given to such conversions with the special approval from the Commissioner General of Motor Traffic and subject to passing a practical test.</li>
                                        </ul>
                                        
                                        <h6>06. Procedures</h6>
                                        <p>Procedures mentioned in this circular shall not obstruct Sri Lankan and foreign national applicants from obtaining a Sri Lankan driving license through the normal procedure.</p>
                                        
                                        <div class="authority-signature">
                                            <p><strong>Nishantha Anurudhdha Weerasinghe</strong><br>
                                            Commissioner General Department of Motor Traffic</p>
                                            <p style="font-size: 0.9rem; font-style: italic; color: #666; margin-top: 1rem;">
                                                * If an inconsistency occurs between the original circular issued in Sinhala language and this translation, the original circular issued in Sinhala must be deemed accurate and legally valid.
                                            </p>
                                        </div>
                                        
                                        <div class="link-reference">
                                            <p><strong>LINK:</strong> <a href="https://dmt.gov.lk/index.php?option=com_content&view=article&id=53&Itemid=169&lang=en" target="_blank">https://dmt.gov.lk/index.php?option=com_content&view=article&id=53&Itemid=169&lang=en</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Driving Lessons Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Driving Lessons</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="lessons-content">
                                <p style="text-align: center;">Once you arrive in Sri Lanka, you will be given a private driving lesson by one of our affiliated tuk tuk instructors. This will teach you in simple terms how to safely ride a tuk tuk on Sri Lankan roads.</p>
                                
                                <div class="lesson-steps">
                                    <div class="step-item">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <h4>Now Ride Your Tuk Tuk</h4>
                                            <p>After you have been given a driving test by your driving instructor and are confident enough to drive, you can start riding Tuk Tuks around Sri Lanka. We are committed to providing you with 24/7 support if you need our assistance.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="driving-lesson-details mt-4" style="text-align: center;">
                                    <h4>Driving Lesson Details</h4>
                                    <p>Before you drive a tuk tuk on the roads of Sri Lanka, you will be given proper instructions and training by a qualified tuk tuk driver. You will also be given prior training on how to fix the tuk tuk yourself if something goes wrong. This will take a very short time, and we kindly request that you take your time for it. If you are a slow learner, we are willing to spend more time than planned.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Legal Information Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Can a foreigner drive a tuk-tuk in Sri Lanka? Is it legal?</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="legal-content">
                                <p style="text-align: center;">If you have a valid driving license from your home country, you can drive a vehicle in Sri Lanka. You will also need to meet two other qualifications:</p>
                                <p style="text-align: center;">1. Sri Lankan Driving Permit </p>
                                <p style="text-align: center;">2. Valid Vehicle Insurance</p>
                                <p style="text-align: center;">Meanwhile, choosing a safe tuk-tuk for your trip, taking private driving lessons, 24-hour roadside vehicle repair facilities, and obtaining a repair kit will make your tuk-tuk trip even easier.</p>
                                <div class="row mt-4">
                                    <div class="col-lg-6">
                                        <div class="requirement-card">
                                            <h4>1. Sri Lankan Driving Permit</h4>
                                            <p>To drive any vehicle within Sri Lanka, you must have a special local license. This is because several countries, including Sri Lanka, do not accept international driving licenses. Due to this legal issue, even if you have a valid license from another country, you must obtain a special driving license approved by the Sri Lankan government. Driving a vehicle in Sri Lanka without such a special license is a serious offense that can be punished.</p>
                                            <p>If you book a tuk tuk ride through our agency, we will arrange for the relevant special vehicle license to be obtained for you.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Insurance Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Insurance Information</h2>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="insurance-card">
                                <h4>Vehicle Insurance</h4>
                                <p>Your safety is ensured by having appropriate vehicle insurance for all vehicles driven within Sri Lanka. 
                                    Our company obtains special insurance coverage for all foreigners who rent tuk-tuks. 
                                    This further ensures your safety. The insurance we have obtained covers you, the driver of the vehicle, the passengers, and the third party.</p>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="insurance-card">
                                <h4>Personal Travel Insurance</h4>
                                <p>In addition to the insurance coverage we provide, if you also take out individual insurance coverage, 
                                    it will double your protection. Even if no one who has hired a tuk tuk has ever had to face a serious accident, 
                                    it is extremely important for you to have personal insurance coverage in your country in case you suddenly have to face such an accident.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Travel Kit Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Travel Kit & Extras</h2>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="kit-card">
                                <h4>Travel Kit</h4>
                                <p>Basic Toolset, Reserve Fuel Tank, Spare Tyre, Lockable Storage Box, Phone Holder and USB Charging Port</p>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="kit-card">
                                <h4>Extras</h4>
                                <p>Surfboard roof racks, Baby-seats, Cooler / Esky, Bluetooth Speakers and Train transfer.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Payment Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Secure Online Payment</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="payment-card text-center">
                                <i class="fas fa-credit-card payment-icon"></i>
                                <p>All your payments can be made via credit or debit card. We also remind you that a deposit of $150 is required for this. This deposit will be given to a designated account or in cash at the end of your trip.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Social Impact Section -->
            <section class="section" data-aos="fade-up">
                <div class="container">
                    <h2 class="section-title">Benefits of Tuk Tuk Rent Service to Society</h2>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="social-impact-content">
                                <p>We work as a social organization. Our organization does not own tuk tuks and we obtain the services of ordinary tuk tuk drivers and improve their quality of life. This enables us to provide you with a more reliable and safer tuk tuk trip. We are also happy to inform you that they have been properly trained and have introduced a set of rules for working with the organization.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Guidebook Modal -->
    <div id="guidebook-modal" class="guidebook-modal">
        <div class="guidebook-overlay">
            <div class="guidebook-container">
                <button class="close-book" id="close-guidebook">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="book">
                    <div class="book-spine"></div>
                    
                    <!-- Page 1-2 (Cover & Introduction) -->
                    <div class="page-spread active" id="page-1">
                        <div class="page left-page">
                            <div class="page-content cover-page">
                                <h1>The Tuk Tuk Guidebook</h1>
                                <div class="cover-image">
                                    <i class="fas fa-taxi"></i>
                                </div>
                                <p class="subtitle">Let's see Sri Lanka while driving a tuk-tuk</p>
                                <div class="cover-footer">
                                    <p>TukTuksLRental.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="page right-page">
                            <div class="page-content">
                                <h2>Welcome to Your Adventure</h2>
                                <p>We invite you to experience the beauty of Sri Lanka with our Tuk Tuk trip. The aim of this trip is to make your trip a memorable one. We provide you with all the facilities you need to travel to your desired destinations with utmost freedom in a Tuk Tuk.</p>
                                <p>Our hope is to make the memories of your trip live forever.</p>
                                <div class="page-decoration">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page 3-4 (Why Guidebook) -->
                    <div class="page-spread" id="page-2">
                        <div class="page left-page">
                            <div class="page-content">
                                <h2>Why you need a Guidebook?</h2>
                                <p>Once you have decided to take this beautiful journey with us, we are committed to doing everything that we can make it easier. We have organized the activities in such a way that this trip feels like it was specially designed for you.</p>
                                <p>We believe that by preparing a handbook for your trip, that goal can be achieved to a greater extent. Sri Lanka is a country with an extremely beautiful and rich history. Even the weather remains very mild throughout the year.</p>
                            </div>
                        </div>
                        <div class="page right-page">
                            <div class="page-content">
                                <p>There is hardly any other country where you can see mountain ranges, waterfalls, ruins and forests so easily. Sri Lanka also has a very beautiful coastline. You can visit all these geographical features in a few hours' drive.</p>
                                <p>We provide you with a detailed map of them. We believe that this will undoubtedly make this beautiful tuk tuk trip more exciting.</p>
                                <div class="highlight-box">
                                    <p><strong>*We are ready to organize short tuk tuk trips, long trips, and medium-term trips for you. You can choose them according to your wishes.</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page 5-6 (FAQ Part 1) -->
                    <div class="page-spread" id="page-3">
                        <div class="page left-page">
                            <div class="page-content">
                                <h2>Most Common Questions</h2>
                                <p>We hope that you will find our frequently asked questions helpful. If you have any new questions that are not listed here, please feel free to contact us.</p>
                                
                                <div class="faq-item">
                                    <h3>Q: Can I start my trip from a different starting point?</h3>
                                    <p><strong>A:</strong> Of course, you can. After you have notified us that your starting point has changed, an Instructor affiliated with our company will come to your new starting point with the tuk-tuk you booked, along with your documents and license.</p>
                                </div>
                            </div>
                        </div>
                        <div class="page right-page">
                            <div class="page-content">
                                <div class="faq-item">
                                    <h3>Q: Can I change my destination?</h3>
                                    <p><strong>A:</strong> It is very easy to do. If you need to change your destination, you must inform us at least two days before the end of the trip. Then a driver affiliated with our company will come to the relevant destination and take over the tuk-tuk. However, kindly note that there will be an additional fee for this.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3>Q: What areas are covered by insurance?</h3>
                                    <p><strong>A:</strong> In the event of an accident, the vehicles and passengers are fully insured. This insurance will also cover injuries and damages to the passengers. The third party will also be covered.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page 7-8 (FAQ Part 2) -->
                    <div class="page-spread" id="page-4">
                        <div class="page left-page">
                            <div class="page-content">
                                <div class="faq-item">
                                    <h3>Q: What's included in the rental package?</h3>
                                    <p><strong>A:</strong> Your package includes:</p>
                                    <ul>
                                        <li>A tuk-tuk in excellent condition</li>
                                        <li>Comprehensive third-party insurance</li>
                                        <li>Comprehensive driving lesson</li>
                                        <li>Unlimited kilometers of driving</li>
                                        <li>Flexible pickup and drop-off locations</li>
                                        <li>A valid Sri Lankan driving license (Local)</li>
                                        <li>Company covers repair costs</li>
                                        <li>Digital map facilities</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="page right-page">
                            <div class="page-content">
                                <div class="faq-item">
                                    <h3>Q: What if my tuk-tuk is stolen?</h3>
                                    <p><strong>A:</strong> Theft or hijacking of a tuk-tuk is not a common occurrence in Sri Lanka. However, if the tuk-tuk you are driving is stolen under some unusual circumstances, it is covered under insurance.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3>Q: What about stolen valuables?</h3>
                                    <p><strong>A:</strong> Report such an incident to us immediately. We will inform the nearest police station. You can also get insurance coverage for all categories covered by the insurance you have purchased. However, you will have to pay approximately $90 for the license and insurance related to the tuk-tuk you have lost.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page 9-10 (FAQ Part 3 & Pricing) -->
                    <div class="page-spread" id="page-5">
                        <div class="page left-page">
                            <div class="page-content">
                                <div class="faq-item">
                                    <h3>Q: Can I get a refund if I cancel?</h3>
                                    <p><strong>A:</strong> You can find all the details in our Rental Terms Summary section.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3>Q: Can I pay with credit card?</h3>
                                    <p><strong>A:</strong> You can pay through PayPal or a secure credit card payment gateway (Stripe).</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3>Q: Do I need advance payment?</h3>
                                    <p><strong>A:</strong> Yes. You will need to make an advance payment to secure a tuk tuk rental from a local owner and to prepare the vehicle.</p>
                                </div>
                            </div>
                        </div>
                        <div class="page right-page">
                            <div class="page-content">
                                <h2>Packages / Price Chart</h2>
                                <p>It is wise to choose a tuk tuk package according to your budget. You can also choose which package suits you according to the number of days you are spending in Sri Lanka.</p>
                                
                                <div class="price-note">
                                    <p><em>We promise to provide you with the maximum value for the money you pay.</em></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page 11-12 (Pricing Tables) -->
                    <div class="page-spread" id="page-6">
                        <div class="page left-page">
                            <div class="page-content">
                                <h3>Tuk Tuk Models</h3>
                                <div class="price-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Model/Days</th>
                                                <th>2-13</th>
                                                <th>14-22</th>
                                                <th>23-38</th>
                                                <th>39-55</th>
                                                <th>56-365</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Regular Tuk tuk</td>
                                                <td>$40</td>
                                                <td>$35</td>
                                                <td>$30</td>
                                                <td>$25</td>
                                                <td>$20</td>
                                            </tr>
                                            <tr>
                                                <td>Electric Tuk tuk</td>
                                                <td>$50</td>
                                                <td>$45</td>
                                                <td>$40</td>
                                                <td>$35</td>
                                                <td>$30</td>
                                            </tr>
                                            <tr>
                                                <td>Removable roof</td>
                                                <td>$45</td>
                                                <td>$40</td>
                                                <td>$35</td>
                                                <td>$30</td>
                                                <td>$25</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="page right-page">
                            <div class="page-content">
                                <h3>Rental Options</h3>
                                <div class="price-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Option</th>
                                                <th>Per Day</th>
                                                <th>Deposit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Local Driving License</td>
                                                <td>$40</td>
                                                <td>Not required</td>
                                            </tr>
                                            <tr>
                                                <td>Baby Seat</td>
                                                <td>$1</td>
                                                <td>Not required</td>
                                            </tr>
                                            <tr>
                                                <td>Bluetooth Speaker</td>
                                                <td>$1</td>
                                                <td>Not required</td>
                                            </tr>
                                            <tr>
                                                <td>Cooler/Esky</td>
                                                <td>$1</td>
                                                <td>Not required</td>
                                            </tr>
                                            <tr>
                                                <td>Seatbelts</td>
                                                <td>$0</td>
                                                <td>Not required</td>
                                            </tr>
                                            <tr>
                                                <td>Surfboard Racks</td>
                                                <td>$1</td>
                                                <td>Not required</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="book-navigation">
                        <button class="nav-btn prev-btn" id="prev-page">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="page-indicator">
                            Page <span id="current-page">1</span> of <span id="total-pages">6</span>
                        </span>
                        <button class="nav-btn next-btn" id="next-page">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php 
    if (file_exists('../includes/footer.php')) {
        include '../includes/footer.php';
    }
    ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="../assets/js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInputs = document.querySelectorAll('input[type="datetime-local"]');
            if (dateInputs.length > 0) {
                flatpickr(dateInputs, {
                    dateFormat: "Y-m-d H:i",
                    enableTime: true,
                    time_24hr: true,
                    minDate: "today"
                });
            }
        });
    </script>
    <script src="../assets/js/howWorks.js"></script>

    <?php 
    // Display the social media icons
    if (function_exists('displaySocialIcons') && isset($social_config)) {
        displaySocialIcons($social_config); 
    }
    ?>
</body>
</html>