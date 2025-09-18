<?php
require_once 'config.php';

// Only allow GET requests for public testimonials
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        sendError('Database connection failed', 500);
    }

    // Get query parameters
    $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 20) : 10;

    // Build query
    $whereConditions = ['is_approved = 1'];
    $params = [];

    if ($featured !== null) {
        $whereConditions[] = "is_featured = :featured";
        $params[':featured'] = $featured ? 1 : 0;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

    $query = "SELECT id, client_name, partner_name, testimonial_text, rating, wedding_date, is_featured
              FROM testimonials 
              $whereClause 
              ORDER BY display_order ASC, created_at DESC 
              LIMIT :limit";

    $stmt = $db->prepare($query);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format testimonials for frontend
    $formattedTestimonials = array_map(function($testimonial) {
        $author = $testimonial['client_name'];
        if ($testimonial['partner_name']) {
            $author .= ' & ' . $testimonial['partner_name'];
        }
        
        return [
            'id' => $testimonial['id'],
            'text' => $testimonial['testimonial_text'],
            'author' => $author,
            'rating' => (int)$testimonial['rating'],
            'wedding_date' => $testimonial['wedding_date'],
            'is_featured' => (bool)$testimonial['is_featured']
        ];
    }, $testimonials);

    sendSuccess('Testimonials loaded successfully', [
        'testimonials' => $formattedTestimonials,
        'count' => count($formattedTestimonials)
    ]);

} catch (Exception $e) {
    error_log("Testimonials loading error: " . $e->getMessage());
    sendError('An error occurred while loading testimonials', 500);
}
?>
