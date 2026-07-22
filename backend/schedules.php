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
    // Check if the user is an admin to allow edit and delete operations
    if ($userRole === 'admin') {
        $stmt = $pdo->prepare('SELECT * FROM schedules');
    } else {
        $stmt = $pdo->prepare('SELECT * FROM schedules WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
    }
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($schedules);
} elseif ($method === 'POST') {
    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the request body
    if (!isset($data['title']) || !isset($data['description']) || !isset($data['start_date']) || !isset($data['end_date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    // Sanitize the request body
    $title = htmlspecialchars($data['title']);
    $description = htmlspecialchars($data['description']);
    $startDate = htmlspecialchars($data['start_date']);
    $endDate = htmlspecialchars($data['end_date']);

    // Insert the new schedule
    $stmt = $pdo->prepare('INSERT INTO schedules (title, description, start_date, end_date, user_id) VALUES (:title, :description, :start_date, :end_date, :user_id)');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Schedule created successfully']);
} elseif ($method === 'PUT') {
    // Check if the user is an admin to allow edit operations
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the request body
    if (!isset($data['id']) || !isset($data['title']) || !isset($data['description']) || !isset($data['start_date']) || !isset($data['end_date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    // Sanitize the request body
    $id = htmlspecialchars($data['id']);
    $title = htmlspecialchars($data['title']);
    $description = htmlspecialchars($data['description']);
    $startDate = htmlspecialchars($data['start_date']);
    $endDate = htmlspecialchars($data['end_date']);

    // Update the schedule
    $stmt = $pdo->prepare('UPDATE schedules SET title = :title, description = :description, start_date = :start_date, end_date = :end_date WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Schedule updated successfully']);
} elseif ($method === 'DELETE') {
    // Check if the user is an admin to allow delete operations
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the request body
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    // Sanitize the request body
    $id = htmlspecialchars($data['id']);

    // Delete the schedule
    $stmt = $pdo->prepare('DELETE FROM schedules WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Schedule deleted successfully']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}