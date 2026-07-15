<!-- login.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <style>
        body {
            background-image: linear-gradient(to bottom, #1a1d23, #2c2f36);
            background-size: 100% 300px;
            background-position: 0% 100%;
            transition: background-position 1s linear;
        }
        
        .glassmorphic {
            background: linear-gradient(90deg, #1a1d23, #2c2f36);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        
        .glassmorphic::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #1a1d23, #2c2f36);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-900 h-screen flex justify-center items-center">
    <div class="glassmorphic w-96 p-10 bg-indigo-500 text-white rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold mb-5">Login</h2>
        <form id="login-form">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" class="block w-full p-2 mt-1 text-gray-700 bg-gray-100 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required>
                <div id="username-error" class="text-red-500 hidden"></div>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" class="block w-full p-2 mt-1 text-gray-700 bg-gray-100 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                <div id="password-error" class="text-red-500 hidden"></div>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Login</button>
            <p class="text-gray-300 text-sm mt-2">Don't have an account? <a href="register.php" class="text-indigo-500 hover:text-indigo-700">Register</a></p>
        </form>
    </div>
    
    <script>
        const form = document.getElementById('login-form');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const usernameError = document.getElementById('username-error');
        const passwordError = document.getElementById('password-error');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            usernameError.classList.remove('text-red-500');
            passwordError.classList.remove('text-red-500');
            usernameError.textContent = '';
            passwordError.textContent = '';
            
            const response = await fetch('../backend/auth.php?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: usernameInput.value,
                    password: passwordInput.value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                if (data.usernameError) {
                    usernameError.textContent = data.usernameError;
                    usernameError.classList.add('text-red-500');
                }
                if (data.passwordError) {
                    passwordError.textContent = data.passwordError;
                    passwordError.classList.add('text-red-500');
                }
            }
        });
    </script>
</body>
</html>


This code creates a premium-looking login page with a glassmorphic design, using Tailwind CSS. It includes a form for username and password input, with validation rules and error messages. The form is submitted using AJAX with the Fetch API, and the response is handled dynamically. The page also includes a link to the registration page.