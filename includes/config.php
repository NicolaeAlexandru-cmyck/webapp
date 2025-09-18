<?php
// includes/config.php
// Database configuration and site settings

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'wedding_planner');
define('DB_USER', 'myuser');
define('DB_PASS', 'mypassword');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'Elegant Weddings');
define('SITE_URL', 'https://yourdomain.com');
define('SITE_EMAIL', 'info@elegantweddings.ro');
define('ADMIN_EMAIL', 'admin@elegantweddings.ro');

// Email Configuration (for contact forms)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');

// File Upload Configuration
define('UPLOAD_PATH', 'assets/images/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('GALLERY_ITEMS_PER_PAGE', 20);

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

// Database Tables Creation (run once)
function createTables($pdo) {
    $tables = [
        "CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            icon VARCHAR(100) NOT NULL,
            price_from DECIMAL(10,2) NULL,
            features JSON NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS gallery_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT NULL,
            sort_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE
        )",
        
        "CREATE TABLE IF NOT EXISTS gallery_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            image_url VARCHAR(500) NOT NULL,
            alt_text VARCHAR(255) NOT NULL,
            category_id INT NOT NULL,
            sort_order INT DEFAULT 0,
            is_featured BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES gallery_categories(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_name VARCHAR(255) NOT NULL,
            partner_name VARCHAR(255) NULL,
            testimonial_text TEXT NOT NULL,
            rating INT DEFAULT 5,
            wedding_date DATE NULL,
            is_featured BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS consultations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NULL,
            wedding_date DATE NULL,
            budget VARCHAR(50) NULL,
            guest_count INT NULL,
            preferred_date DATE NULL,
            preferred_time TIME NULL,
            message TEXT NULL,
            status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS service_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service_name VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NULL,
            wedding_date DATE NULL,
            message TEXT NULL,
            status ENUM('new', 'contacted', 'quoted', 'closed') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            status ENUM('active', 'unsubscribed') DEFAULT 'active',
            subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            unsubscribed_at TIMESTAMP NULL
        )"
    ];
    
    foreach ($tables as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creating table: " . $e->getMessage());
        }
    }
}

// Uncomment the line below to create tables (run once)
// createTables($pdo);
?>
