<?php include '../includes/navbar.php'; ?>
<?php include '../includes/social_icons.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuk Tuk Rental - Gallery</title>
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
    <link rel="stylesheet" href="../assets/css/gallery.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <?php getSocialIconsStyles(); ?>
</head>
<body>
    <div class="main-content">
        <!-- Header Section -->
        <header class="gallery-header">
            <div class="gallery-header-content">
                <div class="container">
                    <h1 class="display-5 fw-bold">Our Gallery</h1>
                    <p>We’re passionate about making your travel experience safe, affordable, and memorable with every tuk tuk ride.</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center custom-breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Gallery Section -->
        <section class="gallery-section py-6">
            <div class="container">
                <h1 class="mb-4 mt-4 text-center">Photos</h1>
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
                        "../assets/images/about/gallery/gallery-12.jpg",
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
                ?>
                <div class="gallery-masonry" id="gallery-masonry">
                    <?php for ($i = 0; $i < min(6, count($gallery_images)); $i++): ?>
                        <div class="gallery-masonry-item">
                            <div class="img-loader"></div>
                            <img src="<?php echo $gallery_images[$i]; ?>" alt="Gallery Photo" style="display:none;">
                        </div>
                    <?php endfor; ?>
                </div>
                <?php if (count($gallery_images) > 6): ?>
                    <div class="text-center mt-4">
                        <button id="show-more-btn" class="btn px-2">
                            Show More <span class="spinner-border spinner-border-sm d-none" id="show-more-loader"></span>
                        </button>
                    </div>
                <?php endif; ?>
                <script>
                    // Pass the rest of the images to JS
                    window.galleryImages = <?php echo json_encode(array_slice($gallery_images, 9)); ?>;
                </script>
            </div>
        </section>

        <!-- Modal for zoomed image -->
        <div id="gallery-modal" class="gallery-modal">
            <span class="gallery-modal-arrow gallery-modal-arrow-left"><i class="fas fa-chevron-left"></i></span>
            <img class="gallery-modal-content" id="gallery-modal-img" alt="Zoomed Photo">
            <span class="gallery-modal-arrow gallery-modal-arrow-right"><i class="fas fa-chevron-right"></i></span>
            <span class="gallery-modal-close">&times;</span>
        </div>
    </div>
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
    <?php 
    // Display the social media icons
    displaySocialIcons($social_config); 
    ?>
    <script src="../assets/js/gallery.js"></script>
</body>
</html>