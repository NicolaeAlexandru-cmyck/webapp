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

    // Required fields validation
    $required = ['firstName', 'lastName', 'email'];
    $missing = validateRequired($required, $input);
    
    if (!empty($missing)) {
        sendError('Missing required fields: ' . implode(', ', $missing));
    }

    // Validate email
    if (!validateEmail($input['email'])) {
        sendError('Invalid email address');
    }

    // Sanitize inputs
    $firstName = sanitizeInput($input['firstName']);
    $lastName = sanitizeInput($input['lastName']);
    $email = sanitizeInput($input['email']);
    $phone = isset($input['phone']) ? sanitizeInput($input['phone']) : null;
    $weddingDate = isset($input['weddingDate']) ? $input['weddingDate'] : null;
    $guestCount = isset($input['guestCount']) ? (int)$input['guestCount'] : null;
    $budget = isset($input['budget']) ? sanitizeInput($input['budget']) : null;
    $services = isset($input['services']) ? json_encode($input['services']) : null;
    $message = isset($input['message']) ? sanitizeInput($input['message']) : null;

    // Validate wedding date if provided
    if ($weddingDate && !DateTime::createFromFormat('Y-m-d', $weddingDate)) {
        sendError('Invalid wedding date format');
    }

    // Validate guest count if provided
    if ($guestCount !== null && ($guestCount < 1 || $guestCount > 1000)) {
        sendError('Guest count must be between 1 and 1000');
    }

    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        sendError('Database connection failed', 500);
    }

    // Check for duplicate appointment (same email within 24 hours)
    $checkQuery = "SELECT id FROM appointments WHERE email = :email AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        sendError('You have already submitted an appointment request within the last 24 hours');
    }

    // Insert appointment
    $query = "INSERT INTO appointments (
        first_name, last_name, email, phone, wedding_date, 
        guest_count, budget_range, services_interested, message, 
        status, created_at
    ) VALUES (
        :firstName, :lastName, :email, :phone, :weddingDate,
        :guestCount, :budget, :services, :message,
        'pending', NOW()
    )";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':weddingDate', $weddingDate);
    $stmt->bindParam(':guestCount', $guestCount);
    $stmt->bindParam(':budget', $budget);
    $stmt->bindParam(':services', $services);
    $stmt->bindParam(':message', $message);

    if ($stmt->execute()) {
        $appointmentId = $db->lastInsertId();
        
        // Log the appointment for admin notification
        error_log("New appointment submitted: ID $appointmentId, Email: $email");
        
        sendSuccess('Appointment request submitted successfully', [
            'appointment_id' => $appointmentId,
            'status' => 'pending'
        ]);
    } else {
        sendError('Failed to submit appointment request', 500);
    }

} catch (Exception $e) {
    error_log("Appointment submission error: " . $e->getMessage());
    sendError('An error occurred while processing your request', 500);
}
?>
