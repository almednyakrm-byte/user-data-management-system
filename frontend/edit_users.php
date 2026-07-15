**edit_users.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get user ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$url = '../backend/users.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if data is available
if ($data) {
    // Populate form fields
    $username = $data['username'];
    $email = $data['email'];
    $role = $data['role'];
} else {
    // Handle error
    echo 'Error fetching user data';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Edit User</h2>
        <form id="edit-user-form">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-slate-900">Username</label>
                <input type="text" id="username" name="username" class="block w-full p-2 pl-10 text-sm text-slate-900 placeholder-slate-400 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" value="<?= $username ?>">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-900">Email</label>
                <input type="email" id="email" name="email" class="block w-full p-2 pl-10 text-sm text-slate-900 placeholder-slate-400 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" value="<?= $email ?>">
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-slate-900">Role</label>
                <select id="role" name="role" class="block w-full p-2 pl-10 text-sm text-slate-900 placeholder-slate-400 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="admin" <?= $role == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="moderator" <?= $role == 'moderator' ? 'selected' : '' ?>>Moderator</option>
                    <option value="user" <?= $role == 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-indigo-500 border border-indigo-500 rounded-lg hover:bg-indigo-600">Update User</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-user-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/users.php',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = 'list_users.php';
                        } else {
                            alert('Error updating user');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating user: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>

**users.php (backend)**

<?php
// Check if user ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

// Get user ID from URL
$id = $_GET['id'];

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user data is available
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}
$conn->close();
?>

**users.php (backend) - Update User**

<?php
// Check if user ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

// Get user ID from URL
$id = $_GET['id'];

// Get user data from request
$username = $_REQUEST['username'];
$email = $_REQUEST['email'];
$role = $_REQUEST['role'];

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Update user data
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
$stmt->bind_param("sssi", $username, $email, $role, $id);
$stmt->execute();

// Check if update was successful
if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['error' => 'Update failed']);
}
$conn->close();
?>