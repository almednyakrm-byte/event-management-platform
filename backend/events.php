<?php

require_once 'db.php';

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the user role
$userRole = $_SESSION['user_role'];

// Handle GET request
if ($method === 'GET') {
    // Validate the request
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the ID
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Prepare the SQL query
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch();

    // Check if the event exists
    if (!$result) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    // Return the event
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($result);
}

// Handle POST request
elseif ($method === 'POST') {
    // Validate the request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['title'], $data['description'], $data['start_date'], $data['end_date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the data
    $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $startDate = filter_var($data['start_date'], FILTER_SANITIZE_STRING);
    $endDate = filter_var($data['end_date'], FILTER_SANITIZE_STRING);

    // Prepare the SQL query
    $stmt = $pdo->prepare('INSERT INTO events (title, description, start_date, end_date) VALUES (:title, :description, :start_date, :end_date)');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();

    // Return the new event
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Event created successfully']);
}

// Handle PUT request
elseif ($method === 'PUT') {
    // Validate the request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['id'], $data['title'], $data['description'], $data['start_date'], $data['end_date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the data
    $id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
    $startDate = filter_var($data['start_date'], FILTER_SANITIZE_STRING);
    $endDate = filter_var($data['end_date'], FILTER_SANITIZE_STRING);

    // Check if the user is admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Prepare the SQL query
    $stmt = $pdo->prepare('UPDATE events SET title = :title, description = :description, start_date = :start_date, end_date = :end_date WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();

    // Return the updated event
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Event updated successfully']);
}

// Handle DELETE request
elseif ($method === 'DELETE') {
    // Validate the request
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Sanitize the ID
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the user is admin
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Prepare the SQL query
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return the result
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Event deleted successfully']);
}

// Return an error if the request method is not supported
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}