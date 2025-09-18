<?php
require_once 'config.php';

// Only allow GET requests for public services
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

    // Get active services
    $query = "SELECT id, title, description, icon, price_range, duration 
              FROM services 
              WHERE is_active = 1 
              ORDER BY display_order ASC, created_at ASC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendSuccess('Services loaded successfully', [
        'services' => $services,
        'count' => count($services)
    ]);

} catch (Exception $e) {
    error_log("Services loading error: " . $e->getMessage());
    sendError('An error occurred while loading services', 500);
}
?>
