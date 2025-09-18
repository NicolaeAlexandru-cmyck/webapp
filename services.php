// services.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = "Wedding Services - Elegant Weddings";
$pageDescription = "Comprehensive wedding planning services including full planning, coordination, and design.";

// Get services from database or array
$services = getServices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content">
                <h1>Our Wedding Services</h1>
                <p>Comprehensive wedding planning services designed to make your special day absolutely perfect</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card" data-service-id="<?php echo $service['id']; ?>">
                    <div class="service-icon">
                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="service-features">
                        <?php if (!empty($service['features'])): ?>
                            <ul>
                                <?php foreach ($service['features'] as $feature): ?>
                                    <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div class="service-price">
                        <?php if ($service['price_from']): ?>
                            <span class="price">From <?php echo formatPrice($service['price_from']); ?></span>
                        <?php else: ?>
                            <span class="price">Contact for Quote</span>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-primary service-inquiry-btn" data-service="<?php echo htmlspecialchars($service['title']); ?>">
                        <i class="fas fa-envelope"></i>
                        Get Quote
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Service Inquiry Modal -->
    <div class="modal" id="serviceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Service Inquiry</h3>
                <button class="modal-close" id="modalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="service-inquiry-form" id="serviceInquiryForm">
                <input type="hidden" id="inquiryService" name="service">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="inquiryName">Full Name *</label>
                        <input type="text" id="inquiryName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="inquiryEmail">Email *</label>
                        <input type="email" id="inquiryEmail" name="email" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="inquiryPhone">Phone</label>
                        <input type="tel" id="inquiryPhone" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="inquiryDate">Wedding Date</label>
                        <input type="date" id="inquiryDate" name="wedding_date" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="inquiryMessage">Message</label>
                    <textarea id="inquiryMessage" name="message" class="form-control" rows="4" placeholder="Tell us about your requirements..."></textarea>
                </div>
                
                <div class="form-submit">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Send Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/services.js"></script>
</body>
</html>
