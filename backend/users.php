<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (empty($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Validate required fields
$requiredFields = ['name', 'email', 'password'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required field: ' . $field]);
        exit;
    }
}

// Sanitize input data
$input['name'] = trim($input['name']);
$input['email'] = filter_var($input['email'], FILTER_VALIDATE_EMAIL);
$input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

// Handle CRUD operations
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all users
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($users);
        break;
    case 'POST':
        // Create new user
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $stmt->execute($input);
        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
        break;
    case 'PUT':
        // Update existing user
        $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id');
        $stmt->execute($input);
        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully']);
        break;
    case 'DELETE':
        // Delete existing user
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $input['id']]);
        http_response_code(200);
        echo json_encode(['message' => 'User deleted successfully']);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}



// Example usage for GET, POST, PUT, DELETE requests
// GET all users
curl -X GET http://localhost/users.php

// POST new user
curl -X POST -H "Content-Type: application/json" -d '{"name": "John Doe", "email": "john@example.com", "password": "password123"}' http://localhost/users.php

// PUT update existing user
curl -X PUT -H "Content-Type: application/json" -d '{"id": 1, "name": "Jane Doe", "email": "jane@example.com", "password": "password123"}' http://localhost/users.php

// DELETE existing user
curl -X DELETE -H "Content-Type: application/json" -d '{"id": 1}' http://localhost/users.php