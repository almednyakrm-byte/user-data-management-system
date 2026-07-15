**create_users.php**

<?php
// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
include 'nav.php';

// Include Tailwind CSS
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .bg-slate-900 {
            background-color: #1A1D23;
        }
        .text-indigo-500 {
            color: #6B7280;
        }
    </style>
</head>
<body class="bg-slate-900">
    <div class="container mx-auto p-4 md:p-6 lg:p-8">
        <h1 class="text-3xl text-indigo-500 font-bold mb-4">Create User</h1>
        <form id="create-user-form">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-300">Role</label>
                <select id="role" name="role" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderator</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Create User</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#create-user-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '../backend/users.php',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            window.location.href = 'list_users.php';
                        } else {
                            alert('Error creating user');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
// Include footer
include 'footer.php';
?>


**users.php (backend)**

<?php
// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form data
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

    // Insert data into database
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'Error creating user';
    }

    $conn->close();
}
?>