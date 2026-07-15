**edit_permissions.php**

<?php
session_start();

// Validate session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get permission ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$permissions = json_decode(file_get_contents('../backend/permissions.php?id=' . $id), true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-md shadow-md">
        <h2 class="text-slate-900 text-lg font-bold mb-4">Edit Permissions</h2>
        <form id="edit-permissions-form">
            <div class="mb-4">
                <label for="name" class="block text-slate-900 text-sm font-bold mb-2">Name:</label>
                <input type="text" id="name" name="name" class="w-full p-2 text-slate-900 border border-slate-300 rounded-md focus:outline-none focus:border-indigo-500" value="<?= $permissions['name'] ?>">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-slate-900 text-sm font-bold mb-2">Description:</label>
                <textarea id="description" name="description" class="w-full p-2 text-slate-900 border border-slate-300 rounded-md focus:outline-none focus:border-indigo-500"><?= $permissions['description'] ?></textarea>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">Save Changes</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-permissions-form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/permissions.php',
                    data: $(this).serialize() + '&id=' + <?= $id ?>,
                    success: function(response) {
                        window.location.href = 'list_permissions.php';
                    }
                });
            });
        });
    </script>
</body>
</html>


**permissions.php (backend)**

<?php
// Validate session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get permission ID from URL
$id = $_GET['id'];

// Fetch existing record details from database
// Replace this with your actual database query
$permissions = array(
    'id' => $id,
    'name' => 'Existing Permission Name',
    'description' => 'Existing Permission Description'
);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($permissions);
?>


**list_permissions.php (example)**

<?php
// Validate session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch permissions from database
// Replace this with your actual database query
$permissions = array(
    array(
        'id' => 1,
        'name' => 'Permission 1',
        'description' => 'Permission 1 Description'
    ),
    array(
        'id' => 2,
        'name' => 'Permission 2',
        'description' => 'Permission 2 Description'
    )
);

// Display permissions list
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissions List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-md shadow-md">
        <h2 class="text-slate-900 text-lg font-bold mb-4">Permissions List</h2>
        <ul>
            <?php foreach ($permissions as $permission) { ?>
                <li class="mb-4">
                    <span class="text-slate-900"><?= $permission['name'] ?></span>
                    <span class="text-gray-600"><?= $permission['description'] ?></span>
                </li>
            <?php } ?>
        </ul>
    </div>
</body>
</html>