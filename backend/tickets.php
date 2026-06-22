<?php
require_once 'db.php';

// Get user role and ID from session
$userRole = $_SESSION['userRole'];
$userID = $_SESSION['userID'];

// Check if user is logged in
if (!$userID) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request
if ($method === 'GET') {
    // Validate and sanitize input
    $ticketID = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    // Check if ticket ID is valid
    if ($ticketID === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ticket ID']);
        exit;
    }

    // Prepare SQL query to select ticket
    $stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = :id');
    $stmt->bindParam(':id', $ticketID);
    $stmt->execute();

    // Fetch ticket data
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if ticket exists
    if (!$ticket) {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket not found']);
        exit;
    }

    // Return ticket data
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($ticket);
}

// Handle POST request
elseif ($method === 'POST') {
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $title = filter_var($input['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($input['description'], FILTER_SANITIZE_STRING);
    $priority = filter_var($input['priority'], FILTER_VALIDATE_INT);

    // Check if input is valid
    if (!$title || !$description || !$priority) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Prepare SQL query to insert ticket
    $stmt = $pdo->prepare('INSERT INTO tickets (title, description, priority, created_by) VALUES (:title, :description, :priority, :created_by)');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':created_by', $userID);
    $stmt->execute();

    // Return ticket ID
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['id' => $pdo->lastInsertId()]);
}

// Handle PUT request
elseif ($method === 'PUT') {
    // Validate and sanitize input
    $ticketID = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $priority = filter_var($_POST['priority'], FILTER_VALIDATE_INT);

    // Check if user is admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Check if ticket ID is valid
    if ($ticketID === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ticket ID']);
        exit;
    }

    // Prepare SQL query to update ticket
    $stmt = $pdo->prepare('UPDATE tickets SET title = :title, description = :description, priority = :priority, updated_by = :updated_by WHERE id = :id');
    $stmt->bindParam(':id', $ticketID);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':updated_by', $userID);
    $stmt->execute();

    // Check if ticket was updated
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket not found']);
        exit;
    }

    // Return success message
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Ticket updated successfully']);
}

// Handle DELETE request
elseif ($method === 'DELETE') {
    // Validate and sanitize input
    $ticketID = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    // Check if user is admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Check if ticket ID is valid
    if ($ticketID === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ticket ID']);
        exit;
    }

    // Prepare SQL query to delete ticket
    $stmt = $pdo->prepare('DELETE FROM tickets WHERE id = :id');
    $stmt->bindParam(':id', $ticketID);
    $stmt->execute();

    // Check if ticket was deleted
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket not found']);
        exit;
    }

    // Return success message
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Ticket deleted successfully']);
}