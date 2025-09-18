<?php
// includes/header.php
// Shared navigation component

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="navbar" id="navbar">
    <div class="container">
        <div class="nav-container">
            <a href="index.html" class="logo">Elegant Weddings</a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.html" class="nav-link <?php echo ($currentPage == 'index') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="services.php" class="nav-link <?php echo ($currentPage == 'services') ? 'active' : ''; ?>">Services</a></li>
                <li><a href="gallery.php" class="nav-link <?php echo ($currentPage == 'gallery') ? 'active' : ''; ?>">Gallery</a></li>
                <li><a href="testimonials.php" class="nav-link <?php echo ($currentPage == 'testimonials') ? 'active' : ''; ?>">Testimonials</a></li>
                <li><a href="consultation.php" class="nav-link <?php echo ($currentPage == 'consultation') ? 'active' : ''; ?>">Book Consultation</a></li>
            </ul>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>
