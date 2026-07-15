**list_roles.php**

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
    <title>Roles Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .header {
            background-color: #1a1d23;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .header a {
            color: #fff;
            text-decoration: none;
        }
        .header a:hover {
            color: #ccc;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 1rem;
            text-align: left;
        }
        .table th {
            background-color: #1a1d23;
            color: #fff;
        }
        .search-bar {
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            width: 50%;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Back to Index</a>
        <span class="text-indigo-500">Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php" class="text-indigo-500">Logout</a>
    </div>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl text-slate-900">Roles Management</h1>
        <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_roles.php'">Add New Item</button>
        <div class="flex justify-center mb-4">
            <input type="search" class="search-bar" placeholder="Search..." id="search-input">
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="roles-table">
                <!-- Table rows will be populated dynamically -->
            </tbody>
        </table>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableBody = document.getElementById('roles-table');

        searchInput.addEventListener('input', () => {
            const searchQuery = searchInput.value.toLowerCase();
            const tableRows = tableBody.children;
            for (let i = 0; i < tableRows.length; i++) {
                const row = tableRows[i];
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchQuery)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        async function fetchRoles() {
            try {
                const response = await fetch('../backend/roles.php');
                const data = await response.json();
                const tableRows = tableBody.children;
                tableBody.innerHTML = '';
                data.forEach((role) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${role.id}</td>
                        <td>${role.name}</td>
                        <td>${role.description}</td>
                        <td>
                            <a href="edit_roles.php?id=${role.id}" class="text-indigo-500">Edit</a>
                            <button class="text-red-500" onclick="deleteRole(${role.id})">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                console.error(error);
            }
        }

        async function deleteRole(id) {
            try {
                const response = await fetch('../backend/roles.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id }),
                });
                if (response.ok) {
                    fetchRoles();
                } else {
                    console.error('Error deleting role');
                }
            } catch (error) {
                console.error(error);
            }
        }

        fetchRoles();
    </script>
</body>
</html>


**roles.php (backend)**

<?php
// Database connection code...
// ...

// Retrieve roles from database
$roles = array();
$query = "SELECT * FROM roles";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $roles[] = $row;
}

// Output roles in JSON format
header('Content-Type: application/json');
echo json_encode($roles);
?>


**Note:** This code assumes you have a `roles` table in your database with columns `id`, `name`, and `description`. You'll need to modify the database connection code and the `roles.php` file to match your actual database schema. Additionally, this code uses the `mysqli` extension for database interactions; you may need to adjust the code to use a different extension (e.g., PDO) depending on your PHP version and configuration.