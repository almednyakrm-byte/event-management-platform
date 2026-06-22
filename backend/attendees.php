<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Initialize database connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle GET requests
if ($method == 'GET') {
    // Validate and sanitize input
    $attendee_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    
    // Check if attendee ID is provided
    if ($attendee_id) {
        // SQL query to retrieve attendee by ID
        $stmt = $pdo->prepare('SELECT * FROM attendees WHERE id = :id');
        $stmt->bindParam(':id', $attendee_id);
        $stmt->execute();
        
        // Fetch attendee data
        $attendee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if attendee exists
        if ($attendee) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($attendee);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Attendee not found']);
        }
    } else {
        // SQL query to retrieve all attendees
        $stmt = $pdo->prepare('SELECT * FROM attendees');
        $stmt->execute();
        
        // Fetch all attendees
        $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($attendees);
    }
}

// Handle POST requests
if ($method == 'POST') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Forbidden']);
        exit;
    }
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate and sanitize input
    $name = filter_var($input['name'] ?? null, FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'] ?? null, FILTER_VALIDATE_EMAIL);
    
    // Check if input is valid
    if ($name && $email) {
        // SQL query to insert new attendee
        $stmt = $pdo->prepare('INSERT INTO attendees (name, email) VALUES (:name, :email)');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Attendee created successfully']);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid input']);
    }
}

// Handle PUT requests
if ($method == 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Forbidden']);
        exit;
    }
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate and sanitize input
    $attendee_id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
    $name = filter_var($input['name'] ?? null, FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'] ?? null, FILTER_VALIDATE_EMAIL);
    
    // Check if input is valid
    if ($attendee_id && $name && $email) {
        // SQL query to update attendee
        $stmt = $pdo->prepare('UPDATE attendees SET name = :name, email = :email WHERE id = :id');
        $stmt->bindParam(':id', $attendee_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Attendee updated successfully']);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid input']);
    }
}

// Handle DELETE requests
if ($method == 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Forbidden']);
        exit;
    }
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate and sanitize input
    $attendee_id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
    
    // Check if input is valid
    if ($attendee_id) {
        // SQL query to delete attendee
        $stmt = $pdo->prepare('DELETE FROM attendees WHERE id = :id');
        $stmt->bindParam(':id', $attendee_id);
        $stmt->execute();
        
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Attendee deleted successfully']);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid input']);
    }
}