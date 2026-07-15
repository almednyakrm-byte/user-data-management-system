<?php

// Import database connection settings
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get user role
$userRole = $_SESSION['user_role'];

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate and sanitize input parameters
    $params = array();
    if (isset($_GET['id'])) {
        $params['id'] = (int) $_GET['id'];
    }
    if (isset($_GET['name'])) {
        $params['name'] = $_GET['name'];
    }
    if (isset($_GET['description'])) {
        $params['description'] = $_GET['description'];
    }

    try {
        // Establish database connection
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL query
        $query = 'SELECT * FROM permissions';
        if (!empty($params)) {
            $query .= ' WHERE ';
            $conditions = array();
            foreach ($params as $key => $value) {
                $conditions[] = "$key = :$key";
            }
            $query .= implode(' AND ', $conditions);
        }

        // Execute query
        $stmt = $pdo->prepare($query);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        $stmt->execute();

        // Fetch and return results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($results);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data)) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request data'));
        exit;
    }

    // Check if user is admin for permission creation
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    try {
        // Establish database connection
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL query
        $query = 'INSERT INTO permissions (name, description) VALUES (:name, :description)';
        $stmt = $pdo->prepare($query);

        // Bind input data
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);

        // Execute query
        $stmt->execute();

        // Return result
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'Permission created successfully'));

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Validate and sanitize input data
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data)) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request data'));
        exit;
    }

    // Check if user is admin for permission updates
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    try {
        // Establish database connection
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL query
        $query = 'UPDATE permissions SET name = :name, description = :description WHERE id = :id';
        $stmt = $pdo->prepare($query);

        // Bind input data
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);

        // Execute query
        $stmt->execute();

        // Return result
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'Permission updated successfully'));

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Validate and sanitize input data
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data)) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid request data'));
        exit;
    }

    // Check if user is admin for permission deletions
    if ($userRole !== 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    try {
        // Establish database connection
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL query
        $query = 'DELETE FROM permissions WHERE id = :id';
        $stmt = $pdo->prepare($query);

        // Bind input data
        $stmt->bindParam(':id', $data['id']);

        // Execute query
        $stmt->execute();

        // Return result
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'Permission deleted successfully'));

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
    }
}