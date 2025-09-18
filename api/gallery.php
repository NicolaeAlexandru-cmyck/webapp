<?php
require_once 'config.php';

// Only allow GET requests for public gallery
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
    $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
    $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 50) : 20;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    // Build query
    $whereConditions = [];
    $params = [];

    if ($category) {
        $whereConditions[] = "category = :category";
        $params[':category'] = $category;
    }

    if ($featured !== null) {
        $whereConditions[] = "is_featured = :featured";
        $params[':featured'] = $featured ? 1 : 0;
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    $query = "SELECT id, title, description, image_url, alt_text, category, is_featured 
              FROM gallery 
              $whereClause 
              ORDER BY display_order ASC, created_at DESC 
              LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($query);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM gallery $whereClause";
    $countStmt = $db->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    sendSuccess('Gallery loaded successfully', [
        'gallery' => $gallery,
        'pagination' => [
            'total' => (int)$total,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total
        ]
    ]);

} catch (Exception $e) {
    error_log("Gallery loading error: " . $e->getMessage());
    sendError('An error occurred while loading the gallery', 500);
}
?>
