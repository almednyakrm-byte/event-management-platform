<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input)) {
    $input = $_POST;
}

// Get all services (GET /services)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM services');
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($services);
}

// Create new service (POST /services)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    if (empty($input['name']) || empty($input['description'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Name and description are required']);
        exit;
    }

    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Insert new service
    $stmt = $pdo->prepare('INSERT INTO services (name, description) VALUES (:name, :description)');
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':description', $input['description']);
    $stmt->execute();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Service created successfully']);
}

// Update existing service (PUT /services/:id)
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get service ID from URL
    $id = explode('/', $_SERVER['REQUEST_URI']);
    $id = end($id);

    // Validate input data
    if (empty($input['name']) || empty($input['description'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Name and description are required']);
        exit;
    }

    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Update existing service
    $stmt = $pdo->prepare('UPDATE services SET name = :name, description = :description WHERE id = :id');
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':description', $input['description']);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Service updated successfully']);
}

// Delete existing service (DELETE /services/:id)
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get service ID from URL
    $id = explode('/', $_SERVER['REQUEST_URI']);
    $id = end($id);

    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Delete existing service
    $stmt = $pdo->prepare('DELETE FROM services WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Service deleted successfully']);
}

// Invalid request method
else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
}