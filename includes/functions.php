<?php
// includes/functions.php
// All PHP functions for data handling

require_once 'config.php';

// Security Functions
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Services Functions
function getServices($limit = null) {
    global $pdo;
    
    $sql = "SELECT * FROM services WHERE is_active = TRUE ORDER BY id ASC";
    if ($limit) {
        $sql .= " LIMIT :limit";
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        $services = $stmt->fetchAll();
        
        // Decode JSON features
        foreach ($services as &$service) {
            if ($service['features']) {
                $service['features'] = json_decode($service['features'], true);
            }
        }
        
        return $services;
    } catch (PDOException $e) {
        error_log("Error fetching services: " . $e->getMessage());
        return getDefaultServices();
    }
}

function getDefaultServices() {
    return [
        [
            'id' => 1,
            'title' => 'Full Wedding Planning',
            'description' => 'Complete wedding planning service from engagement to honeymoon. We handle every detail so you can enjoy your special day stress-free.',
            'icon' => 'fas fa-heart',
            'price_from' => 15000,
            'features' => ['Complete planning', 'Vendor coordination', 'Timeline management', 'Day-of coordination']
        ],
        [
            'id' => 2,
            'title' => 'Partial Wedding Planning',
            'description' => 'Perfect for couples who want professional guidance for specific aspects of their wedding planning journey.',
            'icon' => 'fas fa-calendar-check',
            'price_from' => 8000,
            'features' => ['Vendor recommendations', 'Timeline creation', 'Budget planning', 'Design consultation']
        ],
        [
            'id' => 3,
            'title' => 'Day-of Coordination',
            'description' => 'Ensure your wedding day runs smoothly with our experienced day-of coordination services and timeline management.',
            'icon' => 'fas fa-clock',
            'price_from' => 3000,
            'features' => ['Timeline management', 'Vendor coordination', 'Setup supervision', 'Emergency handling']
        ],
        [
            'id' => 4,
            'title' => 'Venue Selection',
            'description' => 'Find the perfect venue that matches your vision and budget with our extensive network of premier locations.',
            'icon' => 'fas fa-map-marker-alt',
            'price_from' => 2000,
            'features' => ['Venue scouting', 'Site visits', 'Contract negotiation', 'Availability checking']
        ],
        [
            'id' => 5,
            'title' => 'Wedding Design & Styling',
            'description' => 'Create a cohesive and beautiful aesthetic for your wedding with our expert design and styling services.',
            'icon' => 'fas fa-palette',
            'price_from' => 5000,
            'features' => ['Color palette', 'Decor planning', 'Floral design', 'Lighting design']
        ],
        [
            'id' => 6,
            'title' => 'Vendor Coordination',
            'description' => 'Connect with trusted vendors and manage all vendor relationships throughout your planning process.',
            'icon' => 'fas fa-handshake',
            'price_from' => 4000,
            'features' => ['Vendor sourcing', 'Contract management', 'Communication coordination', 'Quality assurance']
        ]
    ];
}

// Gallery Functions
function getGalleryCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM gallery_categories WHERE is_active = TRUE ORDER BY sort_order ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching gallery categories: " . $e->getMessage());
        return getDefaultCategories();
    }
}

function getDefaultCategories() {
    return [
        ['id' => 1, 'name' => 'Ceremony', 'slug' => 'ceremony'],
        ['id' => 2, 'name' => 'Reception', 'slug' => 'reception'],
        ['id' => 3, 'name' => 'Decoration', 'slug' => 'decoration']
    ];
}

function getGalleryItems($category = null, $limit = null, $offset = 0) {
    global $pdo;
    
    try {
        $sql = "SELECT gi.*, gc.name as category_name, gc.slug as category_slug 
                FROM gallery_items gi 
                JOIN gallery_categories gc ON gi.category_id = gc.id 
                WHERE gi.is_active = TRUE";
        
        $params = [];
        
        if ($category && $category !== 'all') {
            $sql .= " AND gc.slug = :category";
            $params['category'] = $category;
        }
        
        $sql .= " ORDER BY gi.sort_order ASC, gi.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }
        
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':' . $key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching gallery items: " . $e->getMessage());
        return getDefaultGalleryItems();
    }
}

function getDefaultGalleryItems() {
    return [
        [
            'id' => 1,
            'title' => 'Elegant Outdoor Ceremony',
            'description' => 'Beautiful garden wedding ceremony',
            'image_url' => 'assets/images/gallery/ceremony/ceremony-1.jpg',
            'alt_text' => 'Elegant outdoor wedding ceremony',
            'category' => 'ceremony',
            'category_name' => 'Ceremony'
        ],
        [
            'id' => 2,
            'title' => 'Romantic Reception',
            'description' => 'Candlelit reception dinner',
            'image_url' => 'assets/images/gallery/reception/reception-1.jpg',
            'alt_text' => 'Romantic wedding reception',
            'category' => 'reception',
            'category_name' => 'Reception'
        ],
        [
            'id' => 3,
            'title' => 'Floral Decorations',
            'description' => 'Beautiful wedding decorations',
            'image_url' => 'assets/images/gallery/decoration/decoration-1.jpg',
            'alt_text' => 'Wedding floral decorations',
            'category' => 'decoration',
            'category_name' => 'Decoration'
        ]
    ];
}

// Testimonials Functions
function getTestimonials($limit = null) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM testimonials WHERE is_active = TRUE ORDER BY is_featured DESC, created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $pdo->prepare($sql);
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching testimonials: " . $e->getMessage());
        return getDefaultTestimonials();
    }
}

function getDefaultTestimonials() {
    return [
        [
            'id' => 1,
            'client_name' => 'Sarah',
            'partner_name' => 'Michael',
            'testimonial_text' => 'Working with Elegant Weddings was the best decision we made for our wedding. They turned our vision into reality and handled every detail perfectly. Our day was absolutely magical!',
            'rating' => 5
        ],
        [
            'id' => 2,
            'client_name' => 'Emily',
            'partner_name' => 'David',
            'testimonial_text' => 'From the initial consultation to the last dance, the team was professional, creative, and so easy to work with. They made our wedding planning stress-free and fun!',
            'rating' => 5
        ],
        [
            'id' => 3,
            'client_name' => 'Jessica',
            'partner_name' => 'Ryan',
            'testimonial_text' => 'I cannot recommend Elegant Weddings enough! They exceeded all our expectations and created the wedding of our dreams. Every guest commented on how perfect everything was.',
            'rating' => 5
        ]
    ];
}

// Consultation Functions
function processConsultationBooking($data) {
    global $pdo;
    
    // Validate required fields
    $required = ['first_name', 'last_name', 'email'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => 'Please fill in all required fields.'];
        }
    }
    
    // Validate email
    if (!validateEmail($data['email'])) {
        return ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
    
    try {
        $sql = "INSERT INTO consultations (first_name, last_name, email, phone, wedding_date, budget, guest_count, preferred_date, preferred_time, message) 
                VALUES (:first_name, :last_name, :email, :phone, :wedding_date, :budget, :guest_count, :preferred_date, :preferred_time, :message)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'first_name' => sanitizeInput($data['first_name']),
            'last_name' => sanitizeInput($data['last_name']),
            'email' => sanitizeInput($data['email']),
            'phone' => sanitizeInput($data['phone'] ?? ''),
            'wedding_date' => $data['wedding_date'] ?? null,
            'budget' => sanitizeInput($data['budget'] ?? ''),
            'guest_count' => $data['guest_count'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'preferred_time' => $data['preferred_time'] ?? null,
            'message' => sanitizeInput($data['message'] ?? '')
        ]);
        
        // Send notification email
        sendConsultationNotification($data);
        
        return ['success' => true, 'message' => 'Consultation booked successfully!'];
    } catch (PDOException $e) {
        error_log("Error booking consultation: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error. Please try again.'];
    }
}

function processServiceInquiry($data) {
    global $pdo;
    
    // Validate required fields
    $required = ['service', 'name', 'email'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => 'Please fill in all required fields.'];
        }
    }
    
    // Validate email
    if (!validateEmail($data['email'])) {
        return ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
    
    try {
        $sql = "INSERT INTO service_inquiries (service_name, name, email, phone, wedding_date, message) 
                VALUES (:service_name, :name, :email, :phone, :wedding_date, :message)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'service_name' => sanitizeInput($data['service']),
            'name' => sanitizeInput($data['name']),
            'email' => sanitizeInput($data['email']),
            'phone' => sanitizeInput($data['phone'] ?? ''),
            'wedding_date' => $data['wedding_date'] ?? null,
            'message' => sanitizeInput($data['message'] ?? '')
        ]);
        
        // Send notification email
        sendServiceInquiryNotification($data);
        
        return ['success' => true, 'message' => 'Inquiry sent successfully!'];
    } catch (PDOException $e) {
        error_log("Error processing service inquiry: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error. Please try again.'];
    }
}

// Newsletter Functions
function subscribeNewsletter($email) {
    global $pdo;
    
    if (!validateEmail($email)) {
        return ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
    
    try {
        $sql = "INSERT INTO newsletter_subscribers (email) VALUES (:email) 
                ON DUPLICATE KEY UPDATE status = 'active', subscribed_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => sanitizeInput($email)]);
        
        return ['success' => true, 'message' => 'Successfully subscribed to newsletter!'];
    } catch (PDOException $e) {
        error_log("Error subscribing to newsletter: " . $e->getMessage());
        return ['success' => false, 'message' => 'Subscription failed. Please try again.'];
    }
}

// Email Functions
function sendConsultationNotification($data) {
    $to = ADMIN_EMAIL;
    $subject = 'New Consultation Booking - ' . SITE_NAME;
    
    $message = "New consultation booking received:\n\n";
    $message .= "Name: " . $data['first_name'] . " " . $data['last_name'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Phone: " . ($data['phone'] ?? 'Not provided') . "\n";
    $message .= "Wedding Date: " . ($data['wedding_date'] ?? 'Not specified') . "\n";
    $message .= "Budget: " . ($data['budget'] ?? 'Not specified') . "\n";
    $message .= "Guest Count: " . ($data['guest_count'] ?? 'Not specified') . "\n";
    $message .= "Preferred Date: " . ($data['preferred_date'] ?? 'Not specified') . "\n";
    $message .= "Preferred Time: " . ($data['preferred_time'] ?? 'Not specified') . "\n";
    $message .= "Message: " . ($data['message'] ?? 'None') . "\n";
    
    $headers = "From: " . SITE_EMAIL . "\r\n";
    $headers .= "Reply-To: " . $data['email'] . "\r\n";
    
    mail($to, $subject, $message, $headers);
}

function sendServiceInquiryNotification($data) {
    $to = ADMIN_EMAIL;
    $subject = 'New Service Inquiry - ' . $data['service'];
    
    $message = "New service inquiry received:\n\n";
    $message .= "Service: " . $data['service'] . "\n";
    $message .= "Name: " . $data['name'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Phone: " . ($data['phone'] ?? 'Not provided') . "\n";
    $message .= "Wedding Date: " . ($data['wedding_date'] ?? 'Not specified') . "\n";
    $message .= "Message: " . ($data['message'] ?? 'None') . "\n";
    
    $headers = "From: " . SITE_EMAIL . "\r\n";
    $headers .= "Reply-To: " . $data['email'] . "\r\n";
    
    mail($to, $subject, $message, $headers);
}

// Utility Functions
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' LEI';
}

function getTimeSlots() {
    return [
        '09:00' => '9:00 AM',
        '10:00' => '10:00 AM',
        '11:00' => '11:00 AM',
        '14:00' => '2:00 PM',
        '15:00' => '3:00 PM',
        '16:00' => '4:00 PM'
    ];
}

function isTimeSlotAvailable($date, $time) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE preferred_date = :date AND preferred_time = :time AND status != 'cancelled'");
        $stmt->execute(['date' => $date, 'time' => $time]);
        
        return $stmt->fetchColumn() == 0;
    } catch (PDOException $e) {
        error_log("Error checking time slot availability: " . $e->getMessage());
        return true; // Assume available if error
    }
}
?>
