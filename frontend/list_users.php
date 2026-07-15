**list_users.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .bg-slate-900 {
            background-color: #1a1d23;
        }
        .text-indigo-500 {
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-slate-900">
    <div class="max-w-7xl mx-auto p-4">
        <nav class="bg-slate-900 rounded-md p-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-indigo-500 hover:text-white">Back to Dashboard</a>
                <div class="flex items-center">
                    <span class="text-indigo-500">Welcome, <?= $_SESSION['username'] ?></span>
                    <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded ml-4" onclick="document.location='logout.php'">Logout</button>
                </div>
            </div>
        </nav>
        <div class="mt-4">
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="document.location='create_users.php'">Add New User</button>
            <div class="mt-4">
                <input type="search" id="search" class="bg-gray-200 rounded-md p-2 w-full" placeholder="Search users...">
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded ml-2" onclick="searchUsers()">Search</button>
            </div>
            <table class="w-full mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-list">
                    <!-- Users list will be populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Search functionality
        function searchUsers() {
            const searchInput = document.getElementById('search');
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetch('../backend/users.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        const usersList = document.getElementById('users-list');
                        usersList.innerHTML = '';
                        data.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-4 py-2">${user.id}</td>
                                <td class="px-4 py-2">${user.name}</td>
                                <td class="px-4 py-2">${user.email}</td>
                                <td class="px-4 py-2">
                                    <a href="edit_users.php?id=${user.id}" class="text-indigo-500 hover:text-white">Edit</a>
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2" onclick="deleteUser(${user.id})">Delete</button>
                                </td>
                            `;
                            usersList.appendChild(row);
                        });
                    });
            } else {
                fetch('../backend/users.php')
                    .then(response => response.json())
                    .then(data => {
                        const usersList = document.getElementById('users-list');
                        usersList.innerHTML = '';
                        data.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-4 py-2">${user.id}</td>
                                <td class="px-4 py-2">${user.name}</td>
                                <td class="px-4 py-2">${user.email}</td>
                                <td class="px-4 py-2">
                                    <a href="edit_users.php?id=${user.id}" class="text-indigo-500 hover:text-white">Edit</a>
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2" onclick="deleteUser(${user.id})">Delete</button>
                                </td>
                            `;
                            usersList.appendChild(row);
                        });
                    });
            }
        }

        // Delete user functionality
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                fetch('../backend/users.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully!');
                        searchUsers();
                    } else {
                        alert('Error deleting user!');
                    }
                });
            }
        }

        // Initialize search functionality
        searchUsers();
    </script>
</body>
</html>

**users.php (backend)**

<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Search functionality
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $query = "SELECT * FROM users WHERE name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%'";
} else {
    $query = "SELECT * FROM users";
}

// Fetch users
$result = $conn->query($query);
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output users in JSON format
header('Content-Type: application/json');
echo json_encode($data);
?>

Note: This is a basic implementation and you should adjust the code to fit your specific needs. Additionally, you should ensure that your backend is secure and follows best practices for database interactions.