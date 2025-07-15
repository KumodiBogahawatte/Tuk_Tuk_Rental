<nav class="navbar navbar-expand-lg navbar-light py-2 sticky-top add-shadow">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="../assets/images/TUK TUK LOGO 1.png" alt="Logo" height="80px">
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
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'vehicles.php') ? 'active' : ''; ?>" href="vehicles.php">VEHICLES</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php">ABOUT US</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'howWorks.php') ? 'active' : ''; ?>" href="howWorks.php">HOW IT WORKS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">CONTACT US</a>
                </li>
                <li class="nav-item">
                    <div class="currency-selector">
                        <select class="form-select" id="currency-select">
                            <option>LKR</option>
                            <option>USD</option>
                        </select>
                    </div>
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

<!-- Ring Loader -->
<div id="site-loader" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:2000;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.97);">
  <div class="ring-loader"></div>
</div>

<script>
window.addEventListener('load', function() {
  document.getElementById('site-loader').style.display = 'none';
});
</script>
