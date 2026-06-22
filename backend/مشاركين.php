<?php

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define routes
$routes = [
    'GET' => [
        '/participants' => 'getParticipants',
        '/participants/:id' => 'getParticipant',
    ],
    'POST' => [
        '/participants' => 'createParticipant',
    ],
    'PUT' => [
        '/participants/:id' => 'updateParticipant',
    ],
    'DELETE' => [
        '/participants/:id' => 'deleteParticipant',
    ],
];

// Route request
$method = $_SERVER['REQUEST_METHOD'];
$route = $_SERVER['REQUEST_URI'];
$parts = explode('/', $route);
$parts = array_filter($parts);
$parts = array_map('intval', $parts);

if (isset($parts[1])) {
    $route = '/' . implode('/', $parts);
}

if (isset($routes[$method][$route])) {
    $action = $routes[$method][$route];
    $action();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

// Functions

function getParticipants() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM participants');
    $stmt->execute();
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($participants);
}

function getParticipant($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM participants WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($participant) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($participant);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}

function createParticipant() {
    global $pdo;
    if (!isset($input['name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        return;
    }
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $stmt = $pdo->prepare('INSERT INTO participants (name, email) VALUES (:name, :email)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Participant created successfully']);
}

function updateParticipant($id) {
    global $pdo;
    if (!isset($input['name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        return;
    }
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        return;
    }
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $stmt = $pdo->prepare('UPDATE participants SET name = :name, email = :email WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Participant updated successfully']);
}

function deleteParticipant($id) {
    global $pdo;
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        return;
    }
    $stmt = $pdo->prepare('DELETE FROM participants WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Participant deleted successfully']);
}