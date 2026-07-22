<?php

// Import database connection settings
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data from JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Handle GET request to retrieve all communications
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare SQL query to retrieve all communications
        $stmt = $pdo->prepare('SELECT * FROM communications');
        $stmt->execute();
        $communications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return HTTP response with communications data
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($communications);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    if (!isset($input['title']) || !isset($input['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Sanitize input data
    $title = htmlspecialchars($input['title']);
    $content = htmlspecialchars($input['content']);

    try {
        // Prepare SQL query to insert new communication
        $stmt = $pdo->prepare('INSERT INTO communications (title, content, user_id, created_at) VALUES (:title, :content, :user_id, NOW())');
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        // Return HTTP response with success message
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Communication created successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Validate input data
    if (!isset($input['id']) || !isset($input['title']) || !isset($input['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Sanitize input data
    $id = (int) $input['id'];
    $title = htmlspecialchars($input['title']);
    $content = htmlspecialchars($input['content']);

    // Check if user is admin
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    try {
        // Prepare SQL query to update communication
        $stmt = $pdo->prepare('UPDATE communications SET title = :title, content = :content, updated_at = NOW() WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->execute();

        // Return HTTP response with success message
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Communication updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Validate input data
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Sanitize input data
    $id = (int) $input['id'];

    // Check if user is admin
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    try {
        // Prepare SQL query to delete communication
        $stmt = $pdo->prepare('DELETE FROM communications WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Return HTTP response with success message
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Communication deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}