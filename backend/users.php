<?php
require_once 'db.php';

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get user role
$userRole = $_SESSION['user_role'];

// Handle GET request
if ($method === 'GET') {
    // Validate user role for admin-only access
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get all users
    $stmt = $pdo->prepare('SELECT * FROM users');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($users);
    exit;
}

// Handle POST request
if ($method === 'POST') {
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Sanitize input
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Get new user ID
    $userId = $pdo->lastInsertId();

    // Return new user data
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

// Handle PUT request
if ($method === 'PUT') {
    // Validate user role for admin-only access
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['id']) || !isset($input['name']) || !isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Sanitize input
    $id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($input['password'], PASSWORD_DEFAULT);

    // Update existing user
    $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Return updated user data
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

// Handle DELETE request
if ($method === 'DELETE') {
    // Validate user role for admin-only access
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Sanitize input
    $id = filter_var($input['id'], FILTER_SANITIZE_NUMBER_INT);

    // Delete existing user
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return success message
    http_response_code(204);
    exit;
}