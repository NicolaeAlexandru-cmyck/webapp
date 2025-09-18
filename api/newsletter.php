<?php
require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendError('Invalid JSON data');
    }

    // Validate email
    if (!isset($input['email']) || !validateEmail($input['email'])) {
        sendError('Valid email address is required');
    }

    $email = sanitizeInput($input['email']);

    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        sendError('Database connection failed', 500);
    }

    // Check if email already exists
    $checkQuery = "SELECT id, is_active FROM newsletter_subscribers WHERE email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing['is_active']) {
            sendError('This email is already subscribed to our newsletter');
        } else {
            // Reactivate subscription
            $updateQuery = "UPDATE newsletter_subscribers SET is_active = 1, unsubscribed_at = NULL WHERE email = :email";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':email', $email);
            
            if ($updateStmt->execute()) {
                sendSuccess('Welcome back! Your newsletter subscription has been reactivated');
            } else {
                sendError('Failed to reactivate subscription', 500);
            }
        }
    } else {
        // Insert new subscription
        $query = "INSERT INTO newsletter_subscribers (email, subscription_source, subscribed_at) VALUES (:email, 'website', NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            sendSuccess('Thank you for subscribing to our newsletter!');
        } else {
            sendError('Failed to subscribe to newsletter', 500);
        }
    }

} catch (Exception $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    sendError('An error occurred while processing your subscription', 500);
}
?>
