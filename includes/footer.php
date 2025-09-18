<?php
// includes/footer.php
// Shared footer component
?>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Elegant Weddings</h3>
                <p>Creating unforgettable wedding experiences with attention to every detail. Your dream wedding is our passion.</p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
                    <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="services.php">Our Services</a></p>
                <p><a href="gallery.php">Wedding Gallery</a></p>
                <p><a href="testimonials.php">Testimonials</a></p>
                <p><a href="consultation.php">Contact Us</a></p>
            </div>
            
            <div class="footer-section">
                <h3>Contact Info</h3>
                <p><i class="fas fa-map-marker-alt"></i> Bucharest, Romania</p>
                <p><i class="fas fa-phone"></i> +40 123 456 789</p>
                <p><i class="fas fa-envelope"></i> info@elegantweddings.ro</p>
                <p><i class="fas fa-clock"></i> Mon-Fri: 9AM-6PM</p>
            </div>
            
            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Subscribe for wedding tips and exclusive offers</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" class="newsletter-input" placeholder="Your email" required>
                    <button type="submit" class="newsletter-btn">Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Elegant Weddings. All rights reserved.</p>
        </div>
    </div>
</footer>
