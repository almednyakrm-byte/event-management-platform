<?php

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Define routes
$routes = array(
    '/tickets' => array('GET', 'POST'),
    '/tickets/:id' => array('GET', 'PUT', 'DELETE')
);

// Route the request
$match = false;
foreach ($routes as $route => $methods) {
    if (strpos($route, '/') === 0) {
        $route = ltrim($route, '/');
    }
    $routeParts = explode('/', $route);
    $uriParts = explode('/', $_SERVER['REQUEST_URI']);
    if (count($routeParts) === count($uriParts)) {
        $match = true;
        $id = isset($input['id']) ? $input['id'] : $uriParts[count($uriParts) - 1];
        if (in_array($_SERVER['REQUEST_METHOD'], $methods)) {
            break;
        }
    }
}

if (!$match) {
    http_response_code(404);
    echo json_encode(array('error' => 'Not Found'));
    exit;
}

// Validate input
if (in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {
    if (!isset($input['title']) || !isset($input['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Validation failed'));
        exit;
    }
    $input['title'] = filter_var($input['title'], FILTER_SANITIZE_STRING);
    $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
}

// Connect to database
$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle request
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (strpos($route, 'tickets') === 0) {
            $stmt = $db->prepare('SELECT * FROM tickets');
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($tickets);
        } else {
            $stmt = $db->prepare('SELECT * FROM tickets WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($ticket) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode($ticket);
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Not Found'));
            }
        }
        break;
    case 'POST':
        if (strpos($route, 'tickets') === 0) {
            $stmt = $db->prepare('INSERT INTO tickets (title, description, user_id, created_at) VALUES (:title, :description, :user_id, NOW())');
            $stmt->bindParam(':title', $input['title']);
            $stmt->bindParam(':description', $input['description']);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(array('message' => 'Ticket created successfully'));
        }
        break;
    case 'PUT':
        if (strpos($route, 'tickets') === 0) {
            $stmt = $db->prepare('UPDATE tickets SET title = :title, description = :description, updated_at = NOW() WHERE id = :id AND user_id = :user_id');
            $stmt->bindParam(':title', $input['title']);
            $stmt->bindParam(':description', $input['description']);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array('message' => 'Ticket updated successfully'));
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Not Found'));
            }
        }
        break;
    case 'DELETE':
        if (strpos($route, 'tickets') === 0) {
            $stmt = $db->prepare('DELETE FROM tickets WHERE id = :id AND user_id = :user_id');
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array('message' => 'Ticket deleted successfully'));
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Not Found'));
            }
        }
        break;
}

// Close database connection
$db = null;

?>