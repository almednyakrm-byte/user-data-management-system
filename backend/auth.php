<?php
// Start the session to handle user authentication
session_start();

// Import the database connection script
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
    echo json_encode($response);
    exit;
}

// Check if the user is trying to register or login
if (isset($_POST['action'])) {
    // Check if the action is register
    if ($_POST['action'] == 'register') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
            // Sanitize and validate user input
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            // Check if the username and email are unique
            $query = "SELECT * FROM users WHERE username = :username OR email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                // If the username or email is already taken, return an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'Username or email already taken'
                );
                echo json_encode($response);
                exit;
            }

            // Hash the password using password_hash()
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();

            // Return a success response with the new user's details
            $user_id = $pdo->lastInsertId();
            $response = array(
                'status' => 'success',
                'user_id' => $user_id,
                'username' => $username
            );
            echo json_encode($response);
            exit;
        } else {
            // If any required field is missing, return an error response
            $response = array(
                'status' => 'error',
                'message' => 'Please fill in all required fields'
            );
            echo json_encode($response);
            exit;
        }
    }

    // Check if the action is login
    elseif ($_POST['action'] == 'login') {
        // Check if all required fields are present
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Sanitize and validate user input
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            // Check if the username exists in the database
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch();
            if (!$result) {
                // If the username does not exist, return an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'Invalid username or password'
                );
                echo json_encode($response);
                exit;
            }

            // Verify the password using password_verify()
            if (password_verify($password, $result['password'])) {
                // If the password is correct, log the user in and return a success response
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $username;
                $response = array(
                    'status' => 'success',
                    'user_id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username']
                );
                echo json_encode($response);
                exit;
            } else {
                // If the password is incorrect, return an error response
                $response = array(
                    'status' => 'error',
                    'message' => 'Invalid username or password'
                );
                echo json_encode($response);
                exit;
            }
        } else {
            // If any required field is missing, return an error response
            $response = array(
                'status' => 'error',
                'message' => 'Please fill in all required fields'
            );
            echo json_encode($response);
            exit;
        }
    }

    // If the action is neither register nor login, return an error response
    else {
        $response = array(
            'status' => 'error',
            'message' => 'Invalid action'
        );
        echo json_encode($response);
        exit;
    }
}

// If the user is not logged in and is not trying to register or login, return a JSON response indicating that they are not logged in
$response = array(
    'status' => 'not_logged_in'
);
echo json_encode($response);

// Log the user out if the logout button is clicked
if (isset($_GET['logout'])) {
    session_destroy();
    session_unset();
    $response = array(
        'status' => 'logged_out'
    );
    echo json_encode($response);
    exit;
}