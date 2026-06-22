<?php

// Start the session to store user data
session_start();

// Import the database connection script
require_once 'db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If the user is logged in, send a JSON response with their details
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

// Check if the user is attempting to register or login
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Check if the user is attempting to register
    if ($action == 'register') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
            // Sanitize and validate user input
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            // Check if the password is strong enough
            if (strlen($password) < 8) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Password must be at least 8 characters long'
                );
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            // Hash the password using password_hash()
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL query to insert the user into the database
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            // Execute the query
            if ($stmt->execute()) {
                // If the user is created successfully, send a JSON response
                $response = array(
                    'status' => 'success',
                    'message' => 'User created successfully'
                );
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                // If the query fails, send an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'Failed to create user'
                );
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        } else {
            // If any required field is missing, send an error response
            $response = array(
                'status' => 'error',
                'message' => 'Please fill in all required fields'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    // Check if the user is attempting to login
    elseif ($action == 'login') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Sanitize and validate user input
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            // Prepare the SQL query to select the user from the database
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);

            // Execute the query
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the user exists
            if ($result->num_rows > 0) {
                // Fetch the user's details
                $row = $result->fetch_assoc();

                // Verify the password using password_verify()
                if (password_verify($password, $row['password'])) {
                    // If the password is correct, log the user in and send a JSON response
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $response = array(
                        'status' => 'logged_in',
                        'user_id' => $_SESSION['user_id'],
                        'username' => $_SESSION['username']
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                } else {
                    // If the password is incorrect, send an error response
                    $response = array(
                        'status' => 'error',
                        'message' => 'Incorrect password'
                    );
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                }
            } else {
                // If the user does not exist, send an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'User not found'
                );
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        } else {
            // If any required field is missing, send an error response
            $response = array(
                'status' => 'error',
                'message' => 'Please fill in all required fields'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    // If the action is neither 'register' nor 'login', send an error response
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

// If the user is not logged in, send a JSON response indicating that they are not logged in
$response = array(
    'status' => 'not_logged_in'
);
header('Content-Type: application/json');
echo json_encode($response);

// Function to handle logout
function logout() {
    // Destroy the session to log the user out
    session_destroy();
    $response = array(
        'status' => 'logged_out'
    );
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Check if the user is attempting to logout
if (isset($_POST['action']) && $_POST['action'] == 'logout') {
    logout();
}