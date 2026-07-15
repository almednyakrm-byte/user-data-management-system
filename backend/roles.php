<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Check if input is valid
if (!$input) {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid input'));
    exit;
}

// Define routes
$routes = array(
    '/roles' => array('GET', 'POST'),
    '/roles/:id' => array('GET', 'PUT', 'DELETE')
);

// Parse route
$match = false;
foreach ($routes as $route => $methods) {
    if (strpos($route, '/') !== false) {
        $parts = explode('/', $route);
        if (count($parts) == 2 && $parts[0] == 'roles' && $parts[1] == ':id') {
            $id = isset($input['id']) ? $input['id'] : null;
            if ($id !== null && is_numeric($id)) {
                $match = array($route, $methods);
                break;
            }
        }
    } else {
        if ($route == 'roles') {
            $match = array($route, $methods);
            break;
        }
    }
}

// Check if route is valid
if (!$match) {
    http_response_code(404);
    echo json_encode(array('error' => 'Route not found'));
    exit;
}

// Get route and methods
list($route, $methods) = $match;

// Check if method is valid
if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
    exit;
}

// Get database connection
$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if user is admin
    if (isset($input['admin']) && $input['admin'] == true && $_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Get roles
    $stmt = $db->prepare('SELECT * FROM roles');
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($roles);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    if (!isset($input['name']) || !isset($input['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid input'));
        exit;
    }

    // Sanitize input
    $name = htmlspecialchars($input['name']);
    $description = htmlspecialchars($input['description']);

    // Insert role
    $stmt = $db->prepare('INSERT INTO roles (name, description) VALUES (:name, :description)');
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    echo json_encode(array('message' => 'Role created successfully'));
}

// Handle PUT request
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Validate input
    if (!isset($input['id']) || !isset($input['name']) || !isset($input['description'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid input'));
        exit;
    }

    // Sanitize input
    $id = htmlspecialchars($input['id']);
    $name = htmlspecialchars($input['name']);
    $description = htmlspecialchars($input['description']);

    // Update role
    $stmt = $db->prepare('UPDATE roles SET name = :name, description = :description WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    echo json_encode(array('message' => 'Role updated successfully'));
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        exit;
    }

    // Validate input
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(array('error' => 'Invalid input'));
        exit;
    }

    // Sanitize input
    $id = htmlspecialchars($input['id']);

    // Delete role
    $stmt = $db->prepare('DELETE FROM roles WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    echo json_encode(array('message' => 'Role deleted successfully'));
}

// Close database connection
$db = null;