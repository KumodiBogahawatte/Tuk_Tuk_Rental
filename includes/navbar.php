<nav class="navbar navbar-expand-lg navbar-light bg-light py-2 shadow-sm sticky-top" style="z-index: 1030;">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="../assets/images/logo.png" alt="Logo" height="60px">
        </a>

        <!-- Toggler button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- <button id="themeToggle" class="btn btn-sm btn-outline-light">
            <i id="themeIcon" class="fa-solid fa-moon"></i> Updated icon class -->
        <!-- </button> -->

        <!-- Navbar links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav gap-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'vehicles.php') ? 'active' : ''; ?>" href="vehicles.php">Vehicles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                </li>
            </ul>
        </div>

        <!-- Contact Info -->
        <div class="d-none d-lg-flex align-items-center gap-3 ms-3">
            <div class="bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <img src="../assets/images/phone.svg" alt="Phone Icon" style="height: 40px;">
            </div>
            <div class="d-flex flex-column lh-sm">
                <span class="text-muted small">Need Help?</span>
                <span class="fw-semibold text-dark">+94 755 555 555</span>
            </div>
        </div>
    </div>
</nav>
