<?php

// Start the session to store user data
session_start();

// Include the database connection file
require_once 'db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If the user is logged in, return a JSON response with their details
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $response = array(
        'status' => 'logged_in',
        'user_id' => $user_id,
        'username' => $username
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if the user is trying to register or login
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Check if the user is trying to register
    if ($action == 'register') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
            // Sanitize the input fields
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            // Check if the username and email are unique
            $query = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // If the username or email is already taken, return an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'Username or email already taken'
                );
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database
            $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sss', $username, $email, $hashed_password);
            $stmt->execute();

            // Return a success response
            $response = array(
                'status' => 'success',
                'message' => 'User registered successfully'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            // If any of the required fields are missing, return an error response
            $response = array(
                'status' => 'error',
                'message' => 'Please fill in all required fields'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    // Check if the user is trying to login
    elseif ($action == 'login') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Sanitize the input fields
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            // Check if the username exists in the database
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Get the user's details from the database
                $user = $result->fetch_assoc();

                // Check if the password is correct
                if (password_verify($password, $user['password'])) {
                    // If the password is correct, log the user in and return a success response
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $response = array(
                        'status' => 'success',
                        'message' => 'User logged in successfully'
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                } else {
                    // If the password is incorrect, return an error response
                    $response = array(
                        'status' => 'error',
                        'message' => 'Incorrect password'
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                }
            } else {
                // If the username does not exist, return an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'Username does not exist'
                );
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        } else {
            // If any of the required fields are missing, return an error response
            $response = array(
                'status' => 'error',
                'message' => 'Please fill in all required fields'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    // If the action is neither 'register' nor 'login', return an error response
    else {
        $response = array(
            'status' => 'error',
            'message' => 'Invalid action'
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// If the user is trying to logout, destroy the session and return a success response
if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    session_destroy();
    $response = array(
        'status' => 'success',
        'message' => 'User logged out successfully'
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If none of the above conditions are met, return a JSON response with the session status
$response = array(
    'status' => 'logged_out'
);
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>