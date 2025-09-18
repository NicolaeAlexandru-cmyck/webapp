<?php
require_once 'config.php';

// Only allow GET requests for public blog
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
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 20) : 6;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    // Build query
    $whereConditions = ['is_published = 1'];
    $params = [];

    if ($category) {
        $whereConditions[] = "category = :category";
        $params[':category'] = $category;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

    $query = "SELECT id, title, slug, excerpt, featured_image, author, category, published_at
              FROM blog_posts 
              $whereClause 
              ORDER BY published_at DESC 
              LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($query);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format posts for frontend
    $formattedPosts = array_map(function($post) {
        return [
            'id' => $post['id'],
            'title' => $post['title'],
            'slug' => $post['slug'],
            'excerpt' => $post['excerpt'],
            'image' => $post['featured_image'],
            'author' => $post['author'],
            'category' => $post['category'],
            'date' => date('F j, Y', strtotime($post['published_at']))
        ];
    }, $posts);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM blog_posts $whereClause";
    $countStmt = $db->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    sendSuccess('Blog posts loaded successfully', [
        'posts' => $formattedPosts,
        'pagination' => [
            'total' => (int)$total,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total
        ]
    ]);

} catch (Exception $e) {
    error_log("Blog loading error: " . $e->getMessage());
    sendError('An error occurred while loading blog posts', 500);
}
?>
