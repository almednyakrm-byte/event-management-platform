<?php
require_once 'db.php';

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get the user role
$userRole = $_SESSION['user_role'];

// Handle GET requests
if ($method === 'GET') {
    // Get the ID parameter
    $id = $_GET['id'] ?? null;

    // Check if the user is an admin to allow edits/deletions
    if ($id && $userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Select all or a single record
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM تذاكر WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch();
        if ($data) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(array('error' => 'Not Found'));
        }
    } else {
        $stmt = $pdo->prepare('SELECT * FROM تذاكر');
        $stmt->execute();
        $data = $stmt->fetchAll();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

// Handle POST requests
elseif ($method === 'POST') {
    // Get the JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the data
    if (!isset($data['name']) || !isset($data['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Bad Request'));
        exit;
    }

    // Sanitize the data
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);

    // Insert the data
    $stmt = $pdo->prepare('INSERT INTO تذاكر (name, description) VALUES (:name, :description)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    // Get the ID of the newly inserted record
    $id = $pdo->lastInsertId();

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(array('id' => $id));
}

// Handle PUT requests
elseif ($method === 'PUT') {
    // Get the ID parameter
    $id = $_GET['id'] ?? null;

    // Check if the user is an admin to allow edits
    if (!$id || $userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Get the JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the data
    if (!isset($data['name']) || !isset($data['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Bad Request'));
        exit;
    }

    // Sanitize the data
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);

    // Update the data
    $stmt = $pdo->prepare('UPDATE تذاكر SET name = :name, description = :description WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'Updated successfully'));
}

// Handle DELETE requests
elseif ($method === 'DELETE') {
    // Get the ID parameter
    $id = $_GET['id'] ?? null;

    // Check if the user is an admin to allow deletions
    if (!$id || $userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Delete the record
    $stmt = $pdo->prepare('DELETE FROM تذاكر WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'Deleted successfully'));
}