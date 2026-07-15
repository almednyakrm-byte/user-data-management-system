**list_permissions.php**

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
    <title>Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .bg-slate-900 {
            background-color: #1a1d23 !important;
        }
        .text-indigo-500 {
            color: #6b7280 !important;
        }
        .text-slate-900 {
            color: #1a1d23 !important;
        }
    </style>
</head>
<body class="bg-slate-900">
    <div class="container mx-auto p-4">
        <header class="bg-indigo-500 p-4 mb-4">
            <nav class="flex justify-between">
                <a href="index.php" class="text-indigo-500 hover:text-white">Back to Index</a>
                <div class="flex items-center">
                    <span class="text-indigo-500 mr-2">Welcome, <?php echo $_SESSION['username']; ?></span>
                    <a href="logout.php" class="text-indigo-500 hover:text-white">Logout</a>
                </div>
            </nav>
        </header>
        <main class="bg-slate-900 p-4">
            <h1 class="text-indigo-500 text-2xl mb-4">Permissions</h1>
            <div class="flex justify-between mb-4">
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_permissions.php'">Add New Item</button>
                <input type="search" id="search" class="bg-slate-900 text-indigo-500 p-2 pl-10 rounded" placeholder="Search...">
            </div>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="permissions-list">
                    <?php
                    // Fetch data from backend
                    $response = file_get_contents('../backend/permissions.php');
                    $permissions = json_decode($response, true);
                    foreach ($permissions as $permission) {
                        ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo $permission['id']; ?></td>
                            <td class="px-4 py-2"><?php echo $permission['name']; ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_permissions.php?id=<?php echo $permission['id']; ?>" class="text-indigo-500 hover:text-white">Edit</a>
                                <button class="text-red-500 hover:text-white" onclick="deletePermission(<?php echo $permission['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const permissionsList = document.getElementById('permissions-list');

        searchInput.addEventListener('input', () => {
            const searchValue = searchInput.value.toLowerCase();
            const permissions = Array.from(permissionsList.children);
            permissions.forEach((row) => {
                const name = row.children[1].textContent.toLowerCase();
                if (name.includes(searchValue)) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        async function deletePermission(id) {
            const response = await fetch('../backend/permissions.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            });
            if (response.ok) {
                const permissions = await response.json();
                permissionsList.innerHTML = '';
                permissions.forEach((permission) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${permission.id}</td>
                        <td class="px-4 py-2">${permission.name}</td>
                        <td class="px-4 py-2">
                            <a href="edit_permissions.php?id=${permission.id}" class="text-indigo-500 hover:text-white">Edit</a>
                            <button class="text-red-500 hover:text-white" onclick="deletePermission(${permission.id})">Delete</button>
                        </td>
                    `;
                    permissionsList.appendChild(row);
                });
            } else {
                alert('Error deleting permission');
            }
        }
    </script>
</body>
</html>

Note: This code assumes that you have a `permissions.php` file in the `../backend` directory that returns a JSON array of permissions. You'll need to modify the `deletePermission` function to match your backend API.