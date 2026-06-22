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
$user_role = $_SESSION['user_role'];

// Handle GET request
if ($method === 'GET') {
    // Get the payment ID from the URL query string
    $payment_id = $_GET['id'] ?? null;

    // Check if the user is an admin to allow edit and delete operations
    if ($payment_id && ($user_role !== 'admin')) {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Select all payments or a single payment by ID
    if ($payment_id) {
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE id = :id');
        $stmt->execute(['id' => $payment_id]);
        $payment = $stmt->fetch();
        if (!$payment) {
            http_response_code(404);
            echo json_encode(array('error' => 'Not Found'));
            exit;
        }
        echo json_encode($payment);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM payments');
        $stmt->execute();
        $payments = $stmt->fetchAll();
        echo json_encode($payments);
    }
}

// Handle POST request
if ($method === 'POST') {
    // Get the payment data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the payment data
    if (!isset($data['amount']) || !isset($data['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Bad Request'));
        exit;
    }

    // Sanitize the payment data
    $amount = filter_var($data['amount'], FILTER_SANITIZE_NUMBER_INT);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);

    // Insert the payment into the database
    $stmt = $pdo->prepare('INSERT INTO payments (amount, description) VALUES (:amount, :description)');
    $stmt->execute(['amount' => $amount, 'description' => $description]);
    http_response_code(201);
    echo json_encode(array('message' => 'Payment created successfully'));
}

// Handle PUT request
if ($method === 'PUT') {
    // Get the payment ID and data from the URL query string and request body
    $payment_id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if the user is an admin to allow edit operations
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Validate the payment data
    if (!isset($data['amount']) || !isset($data['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Bad Request'));
        exit;
    }

    // Sanitize the payment data
    $amount = filter_var($data['amount'], FILTER_SANITIZE_NUMBER_INT);
    $description = filter_var($data['description'], FILTER_SANITIZE_STRING);

    // Update the payment in the database
    $stmt = $pdo->prepare('UPDATE payments SET amount = :amount, description = :description WHERE id = :id');
    $stmt->execute(['amount' => $amount, 'description' => $description, 'id' => $payment_id]);
    http_response_code(200);
    echo json_encode(array('message' => 'Payment updated successfully'));
}

// Handle DELETE request
if ($method === 'DELETE') {
    // Get the payment ID from the URL query string
    $payment_id = $_GET['id'];

    // Check if the user is an admin to allow delete operations
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Delete the payment from the database
    $stmt = $pdo->prepare('DELETE FROM payments WHERE id = :id');
    $stmt->execute(['id' => $payment_id]);
    http_response_code(204);
    echo json_encode(array('message' => 'Payment deleted successfully'));
}