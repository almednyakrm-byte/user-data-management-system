<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة قواعد بيانات مستخدمين</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .glassmorphism-card {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="flex justify-between items-center p-4 bg-slate-900">
        <h1 class="text-3xl text-white">نظام إدارة قواعد بيانات مستخدمين</h1>
        <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">تسجيل الخروج</button>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 p-4">
            <h2 class="text-2xl text-white">مرحباً <?= $_SESSION['username'] ?></h2>
            <div class="flex justify-between items-center p-4">
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='users.php'">إدارة المستخدمين</button>
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='roles.php'">إدارة الدور</button>
                <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='permissions.php'">إدارة الصلاحيات</button>
            </div>
            <div class="flex justify-center items-center p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="glassmorphism-card w-full p-4">
                        <h3 class="text-lg text-white">إجمالي المستخدمين</h3>
                        <p id="total-users" class="text-3xl text-white"></p>
                    </div>
                    <div class="glassmorphism-card w-full p-4">
                        <h3 class="text-lg text-white">إجمالي الدور</h3>
                        <p id="total-roles" class="text-3xl text-white"></p>
                    </div>
                    <div class="glassmorphism-card w-full p-4">
                        <h3 class="text-lg text-white">إجمالي الصلاحيات</h3>
                        <p id="total-permissions" class="text-3xl text-white"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch('api/stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').innerHTML = data.total_users;
            document.getElementById('total-roles').innerHTML = data.total_roles;
            document.getElementById('total-permissions').innerHTML = data.total_permissions;
        })
        .catch(error => console.error(error));
    </script>
</body>
</html>


This code uses Tailwind CSS for styling and includes a session check to redirect to the login page if the user is not authenticated. The dashboard layout includes a welcome message, logout button, overview stats grid, and quick links to manage modules. The stats are fetched dynamically via a JavaScript API call to `api/stats.php`. 

Please note that you need to create a PHP file named `api/stats.php` to handle the API call and return the stats data in JSON format. 

Also, make sure to replace `logout.php`, `users.php`, `roles.php`, and `permissions.php` with the actual file paths for your logout and module management pages.