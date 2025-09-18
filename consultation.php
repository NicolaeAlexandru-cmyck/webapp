<?php
// consultation.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = "Book Consultation - Elegant Weddings";
$pageDescription = "Schedule a free consultation with our wedding planning experts.";

// Handle form submission
$success = false;
$error = '';

if ($_POST && isset($_POST['submit_consultation'])) {
    $result = processConsultationBooking($_POST);
    if ($result['success']) {
        $success = true;
    } else {
        $error = $result['message'];
    }
}
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
                <h1>Book Your Consultation</h1>
                <p>Let's start planning your dream wedding together. Schedule a free consultation with our expert team</p>
            </div>
        </div>
    </section>

    <!-- Consultation Section -->
    <section class="consultation-section">
        <div class="container">
            <div class="consultation-content">
                <div class="consultation-info">
                    <h3>What to Expect</h3>
                    <div class="info-items">
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h4>60-Minute Session</h4>
                                <p>Comprehensive discussion about your wedding vision and requirements</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-heart"></i>
                            <div>
                                <h4>Personalized Planning</h4>
                                <p>Tailored recommendations based on your style, budget, and preferences</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-gift"></i>
                            <div>
                                <h4>Complimentary Service</h4>
                                <p>No cost consultation to help you get started on your wedding journey</p>
                            </div>
                        </div>
                    </div>

                    <!-- Available Time Slots -->
                    <div class="time-slots">
                        <h4>Available Time Slots</h4>
                        <div class="slots-grid" id="timeSlotsGrid">
                            <!-- Time slots loaded via JavaScript -->
                        </div>
                    </div>
                </div>

                <form class="consultation-form" id="consultationForm" method="POST">
                    <?php if ($success): ?>
                        <div class="success-message show">
                            Thank you! Your consultation request has been submitted successfully. We'll contact you within 24 hours to confirm your appointment.
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="error-message show">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="first_name" class="form-control" required 
                                   value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="last_name" class="form-control" required
                                   value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="weddingDate">Wedding Date</label>
                            <input type="date" id="weddingDate" name="wedding_date" class="form-control"
                                   value="<?php echo isset($_POST['wedding_date']) ? htmlspecialchars($_POST['wedding_date']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="budget">Estimated Budget</label>
                            <select id="budget" name="budget" class="form-control">
                                <option value="">Select Budget Range</option>
                                <option value="under-50k" <?php echo (isset($_POST['budget']) && $_POST['budget'] == 'under-50k') ? 'selected' : ''; ?>>Under 50,000 LEI</option>
                                <option value="50k-125k" <?php echo (isset($_POST['budget']) && $_POST['budget'] == '50k-125k') ? 'selected' : ''; ?>>50,000 - 125,000 LEI</option>
                                <option value="125k-250k" <?php echo (isset($_POST['budget']) && $_POST['budget'] == '125k-250k') ? 'selected' : ''; ?>>125,000 - 250,000 LEI</option>
                                <option value="250k-500k" <?php echo (isset($_POST['budget']) && $_POST['budget'] == '250k-500k') ? 'selected' : ''; ?>>250,000 - 500,000 LEI</option>
                                <option value="over-500k" <?php echo (isset($_POST['budget']) && $_POST['budget'] == 'over-500k') ? 'selected' : ''; ?>>Over 500,000 LEI</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="guestCount">Expected Guest Count</label>
                        <input type="number" id="guestCount" name="guest_count" class="form-control" min="1" max="1000" 
                               placeholder="e.g. 100" value="<?php echo isset($_POST['guest_count']) ? htmlspecialchars($_POST['guest_count']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="preferredDate">Preferred Consultation Date</label>
                        <input type="date" id="preferredDate" name="preferred_date" class="form-control" min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo isset($_POST['preferred_date']) ? htmlspecialchars($_POST['preferred_date']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="preferredTime">Preferred Time</label>
                        <select id="preferredTime" name="preferred_time" class="form-control">
                            <option value="">Select Time</option>
                            <option value="09:00" <?php echo (isset($_POST['preferred_time']) && $_POST['preferred_time'] == '09:00') ? 'selected' : ''; ?>>9:00 AM</option>
                            <option value="10:00" <?php echo (isset($_POST['preferred_time']) && $_POST['preferred_time'] == '10:00') ? 'selected' : ''; ?>>10:00 AM</option>
                            <option value="11:00" <?php echo (isset($_POST['preferred_time']) && $_POST['preferred_time'] == '11:00') ? 'selected' : ''; ?>>11:00 AM</option>
                            <option value="14:00" <?php echo (isset($_POST['preferred_time']) && $_POST['preferred_time'] == '14:00') ? 'selected' : ''; ?>>2:00 PM</option>
                            <option value="15:00" <?php echo (isset($_POST['preferred_time']) && $_POST['preferred_time'] == '15:00') ? 'selected' : ''; ?>>3:00 PM</option>
                            <option value="16:00" <?php echo (isset($_POST['preferred_time']) && $_POST['preferred_time'] == '16:00') ? 'selected' : ''; ?>>4:00 PM</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Tell us about your dream wedding</label>
                        <textarea id="message" name="message" class="form-control" rows="4" 
                                  placeholder="Share your vision, style preferences, or any special requirements..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-submit">
                        <button type="submit" name="submit_consultation" class="btn btn-primary btn-large">
                            <i class="fas fa-calendar-check"></i>
                            Schedule Consultation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/consultation.js"></script>
</body>
</html>
