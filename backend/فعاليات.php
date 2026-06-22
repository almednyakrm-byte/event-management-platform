<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get user role
$user_role = $_SESSION['user_role'];

// Check if user is admin
$is_admin = $user_role == 'admin';

// Get input data
$input_data = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (empty($input_data)) {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

// Define CRUD operations
function get_activities() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM activities');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function create_activity() {
    global $pdo;
    $activity_name = filter_var($input_data['activity_name'], FILTER_SANITIZE_STRING);
    $activity_description = filter_var($input_data['activity_description'], FILTER_SANITIZE_STRING);
    $stmt = $pdo->prepare('INSERT INTO activities (name, description) VALUES (:name, :description)');
    $stmt->bindParam(':name', $activity_name);
    $stmt->bindParam(':description', $activity_description);
    $stmt->execute();
    return $pdo->lastInsertId();
}

function update_activity($id) {
    global $pdo;
    $activity_name = filter_var($input_data['activity_name'], FILTER_SANITIZE_STRING);
    $activity_description = filter_var($input_data['activity_description'], FILTER_SANITIZE_STRING);
    $stmt = $pdo->prepare('UPDATE activities SET name = :name, description = :description WHERE id = :id');
    $stmt->bindParam(':name', $activity_name);
    $stmt->bindParam(':description', $activity_description);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->rowCount();
}

function delete_activity($id) {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM activities WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->rowCount();
}

// Handle HTTP requests
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($is_admin) {
        $activities = get_activities();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($activities);
    } else {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($is_admin) {
        $activity_id = create_activity();
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(array('id' => $activity_id));
    } else {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if ($is_admin) {
        $id = $input_data['id'];
        $updated = update_activity($id);
        if ($updated) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('message' => 'Activity updated successfully'));
        } else {
            http_response_code(404);
            echo json_encode(array('error' => 'Activity not found'));
        }
    } else {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if ($is_admin) {
        $id = $input_data['id'];
        $deleted = delete_activity($id);
        if ($deleted) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('message' => 'Activity deleted successfully'));
        } else {
            http_response_code(404);
            echo json_encode(array('error' => 'Activity not found'));
        }
    } else {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
}