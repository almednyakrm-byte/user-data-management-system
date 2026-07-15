**edit_roles.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get role ID from URL
$id = $_GET['id'];

// Fetch existing role details via AJAX
$js = "
<script>
    $(document).ready(function() {
        $.get('../backend/roles.php?id=" . $id . "')
            .done(function(data) {
                $('#role_name').val(data.role_name);
                $('#role_description').val(data.role_description);
            })
            .fail(function() {
                alert('Error fetching role details');
            });
    });
</script>
";

// Display form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-slate-900 {
            background-color: #1A1D23;
        }
        .text-indigo-500 {
            color: #6b5f7e;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4 bg-slate-900 rounded-lg">
        <h1 class="text-3xl text-indigo-500 mb-4">Edit Role</h1>
        <form id="edit-role-form" class="w-full max-w-md">
            <div class="mb-4">
                <label for="role_name" class="block text-sm font-medium text-gray-700">Role Name</label>
                <input type="text" id="role_name" name="role_name" class="block w-full p-2 mt-1 text-sm text-gray-700 bg-gray-100 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="mb-4">
                <label for="role_description" class="block text-sm font-medium text-gray-700">Role Description</label>
                <textarea id="role_description" name="role_description" class="block w-full p-2 mt-1 text-sm text-gray-700 bg-gray-100 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Update Role</button>
        </form>
    </div>

    <?php echo $js; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-role-form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/roles.php',
                    data: $(this).serialize() + '&id=' + <?php echo $id; ?>,
                    success: function(data) {
                        window.location.href = 'list_roles.php';
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating role');
                    }
                });
            });
        });
    </script>
</body>
</html>


**roles.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get role ID from URL
$id = $_GET['id'];

// Fetch existing role details
$role = get_role($id);

// Update role details via AJAX
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents('php://input'), $data);
    update_role($id, $data['role_name'], $data['role_description']);
    echo json_encode(['success' => true]);
} else {
    // Display role details
    echo json_encode($role);
}

// Helper functions
function get_role($id) {
    // Fetch role details from database
    // ...
    return ['role_name' => 'Role Name', 'role_description' => 'Role Description'];
}

function update_role($id, $name, $description) {
    // Update role details in database
    // ...
}