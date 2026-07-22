<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <style>
        body {
            background-image: linear-gradient(to bottom, #2f343a, #2f343a);
            background-size: 100% 300px;
            background-position: 0% 100%;
            -webkit-transition: background-position 2s linear;
            transition: background-position 2s linear;
        }
        .glassmorphic {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1)), linear-gradient(0deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1));
            background-blend-mode: overlay;
            border-radius: 10px;
            padding: 20px;
        }
        .glassmorphic::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1)), linear-gradient(0deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.1));
            background-blend-mode: overlay;
            border-radius: 10px;
            filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-900 h-screen">
    <div class="flex justify-center items-center h-screen">
        <div class="glassmorphic w-96 p-10 bg-white rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-emerald-600 mb-4">Login</h2>
            <form id="login-form">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required>
                    <div id="username-error" class="text-red-500 hidden"></div>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <div id="password-error" class="text-red-500 hidden"></div>
                </div>
                <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Login</button>
                <p class="text-gray-700 text-sm font-bold mt-4">Don't have an account? <a href="register.php" class="text-teal-500 hover:text-teal-700">Register</a></p>
            </form>
        </div>
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

            const username = usernameInput.value.trim();
            const password = passwordInput.value.trim();

            if (username === '') {
                usernameError.classList.add('text-red-500');
                usernameError.textContent = 'Username is required';
                return;
            }

            if (password === '') {
                passwordError.classList.add('text-red-500');
                passwordError.textContent = 'Password is required';
                return;
            }

            try {
                const response = await fetch('../backend/auth.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    if (data.error.username) {
                        usernameError.classList.add('text-red-500');
                        usernameError.textContent = data.error.username;
                    }
                    if (data.error.password) {
                        passwordError.classList.add('text-red-500');
                        passwordError.textContent = data.error.password;
                    }
                }
            } catch (error) {
                console.error(error);
            }
        });
    </script>
</body>
</html>


This code uses Tailwind CSS to create a premium-looking login page with a glassmorphic layout, gradients, and a form for username and password input. It also includes validation rules for the form fields and uses the Fetch API to submit the credentials to the backend PHP script. The response from the backend is then handled dynamically, and error messages are displayed if the login is unsuccessful.