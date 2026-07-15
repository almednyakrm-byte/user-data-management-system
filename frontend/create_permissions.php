**create_permissions.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:px-12 xl:px-24">
    <h1 class="text-3xl font-bold text-slate-900 mb-4">Create Permission</h1>

    <form id="create-permission-form" class="bg-white rounded-lg shadow-md p-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-900">Name</label>
                <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-sm text-gray-700 placeholder-slate-300 border border-slate-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500" placeholder="Permission Name">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-slate-900">Description</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-sm text-gray-700 placeholder-slate-300 border border-slate-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 focus:border-indigo-500" placeholder="Permission Description"></textarea>
            </div>
        </div>

        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Create Permission</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#create-permission-form').submit(function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '../backend/permissions.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_permissions.php';
                    } else {
                        alert('Error creating permission: ' + response);
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


**permissions.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['name']) && isset($_POST['description'])) {
    // Prepare query
    $query = "INSERT INTO permissions (name, description) VALUES (?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $_POST['name'], $_POST['description']);

    // Execute query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error creating permission: ' . $mysqli->error;
    }

    // Close statement and connection
    $stmt->close();
    $mysqli->close();
}
?>