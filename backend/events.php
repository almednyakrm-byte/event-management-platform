<?php

require_once 'db.php';

// Get user role from session
$userRole = $_SESSION['userRole'];

// Get event ID from URL parameter
$eventId = isset($_GET['id']) ? $_GET['id'] : null;

// Get event data from JSON input or POST data
$inputData = json_decode(file_get_contents('php://input'), true);
if (empty($inputData)) {
    $inputData = $_POST;
}

// Validate and sanitize input data
if (empty($inputData)) {
    http_response_code(400);
    echo json_encode(['error' => 'No data provided']);
    exit;
}

// Validate event data
$requiredFields = ['title', 'description', 'date', 'time'];
foreach ($requiredFields as $field) {
    if (!isset($inputData[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required field: ' . $field]);
        exit;
    }
}

// Sanitize event data
$inputData['title'] = htmlspecialchars($inputData['title']);
$inputData['description'] = htmlspecialchars($inputData['description']);

// Connect to database
$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// GET all events
if (empty($eventId)) {
    try {
        $stmt = $db->prepare('SELECT * FROM events');
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($events);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($userRole === 'admin') {
    // GET event by ID
    try {
        $stmt = $db->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->bindParam(':id', $eventId);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) {
            http_response_code(404);
            echo json_encode(['error' => 'Event not found']);
        } else {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($event);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($userRole === 'user') {
    // GET user's events
    try {
        $stmt = $db->prepare('SELECT * FROM events WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $_SESSION['userId']);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($events);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// POST new event
if (empty($eventId)) {
    try {
        $stmt = $db->prepare('INSERT INTO events (title, description, date, time, user_id) VALUES (:title, :description, :date, :time, :user_id)');
        $stmt->bindParam(':title', $inputData['title']);
        $stmt->bindParam(':description', $inputData['description']);
        $stmt->bindParam(':date', $inputData['date']);
        $stmt->bindParam(':time', $inputData['time']);
        $stmt->bindParam(':user_id', $_SESSION['userId']);
        $stmt->execute();
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Event created successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// PUT update event
if ($userRole === 'admin' || $userRole === 'user') {
    try {
        $stmt = $db->prepare('UPDATE events SET title = :title, description = :description, date = :date, time = :time WHERE id = :id');
        $stmt->bindParam(':title', $inputData['title']);
        $stmt->bindParam(':description', $inputData['description']);
        $stmt->bindParam(':date', $inputData['date']);
        $stmt->bindParam(':time', $inputData['time']);
        $stmt->bindParam(':id', $eventId);
        $stmt->execute();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Event updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// DELETE event
if ($userRole === 'admin') {
    try {
        $stmt = $db->prepare('DELETE FROM events WHERE id = :id');
        $stmt->bindParam(':id', $eventId);
        $stmt->execute();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Event deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

$db = null;