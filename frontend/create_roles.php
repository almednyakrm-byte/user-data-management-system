**create_roles.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
include 'nav.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:px-12 xl:px-24">
    <h1 class="text-3xl font-bold text-slate-900 mb-4">Create Role</h1>

    <form id="create-role-form" class="bg-white rounded-lg shadow-md p-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="role_name" class="block text-sm font-medium text-slate-900">Role Name</label>
                <input type="text" id="role_name" name="role_name" class="block w-full p-2 mt-1 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="role_description" class="block text-sm font-medium text-slate-900">Role Description</label>
                <textarea id="role_description" name="role_description" class="block w-full p-2 mt-1 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
            </div>
        </div>

        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Create Role</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#create-role-form').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '../backend/roles.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_roles.php';
                    } else {
                        alert('Error creating role');
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**roles.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['role_name']) && isset($_POST['role_description'])) {
    // Prepare SQL query
    $sql = "INSERT INTO roles (name, description) VALUES (?, ?)";

    // Prepare and execute query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $_POST['role_name'], $_POST['role_description']);
    $stmt->execute();

    // Check if query was successful
    if ($stmt->affected_rows === 1) {
        echo 'success';
    } else {
        echo 'Error creating role';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>


**Note:** This code assumes you have a `db.php` file that establishes a connection to your database, and a `header.php` and `footer.php` file that includes the HTML header and footer, respectively. You will need to modify the code to fit your specific database schema and application structure.