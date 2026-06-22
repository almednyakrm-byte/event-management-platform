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
            -webkit-transition: background-position 0.5s ease-in-out;
            transition: background-position 0.5s ease-in-out;
        }
        
        .glassmorphic {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .glassmorphic::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #fff, #fff);
            mix-blend-mode: screen;
            z-index: -1;
        }
        
        .gradient {
            background: linear-gradient(to bottom, #2f343a, #2f343a);
            background-size: 100% 300px;
            background-position: 0% 100%;
            -webkit-transition: background-position 0.5s ease-in-out;
            transition: background-position 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-900 h-screen flex justify-center items-center">
    <div class="glassmorphic bg-white rounded-lg shadow-lg p-10 w-96">
        <h2 class="text-3xl font-bold text-emerald-600 mb-4">Login</h2>
        <form id="login-form">
            <div class="mb-4">
                <label for="username" class="block text-gray-600 text-sm font-bold mb-2">Username</label>
                <input type="text" id="username" name="username" class="block w-full p-2 border border-gray-300 rounded-lg" placeholder="Enter your username" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required>
                <div id="username-error" class="text-red-500 hidden"></div>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-600 text-sm font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" class="block w-full p-2 border border-gray-300 rounded-lg" placeholder="Enter your password" required>
                <div id="password-error" class="text-red-500 hidden"></div>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Login</button>
            <p class="text-gray-600 text-sm mt-2">Don't have an account? <a href="register.php" class="text-emerald-600 hover:text-emerald-800">Register</a></p>
        </form>
    </div>

    <script>
        const form = document.getElementById('login-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
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
                    document.getElementById('username-error').textContent = data.usernameError ? data.usernameError : '';
                    document.getElementById('password-error').textContent = data.passwordError ? data.passwordError : '';
                    document.getElementById('username-error').classList.remove('hidden');
                    document.getElementById('password-error').classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('username-error').classList.add('hidden');
                        document.getElementById('password-error').classList.add('hidden');
                    }, 3000);
                }
            } catch (error) {
                console.error(error);
                alert('Error logging in. Please try again later.');
            }
        });
    </script>
</body>
</html>


This code creates a premium-looking login page with a glassmorphic layout, gradients, and a form for username and password input. It uses the Tailwind CSS CDN for styling and includes a beautiful glassmorphic layout with a gradient background. The form includes standard HTML input pattern validators to support Arabic and Latin characters. The AJAX JavaScript code uses the Fetch API to submit the credentials to the backend `auth.php` file and handles the response or error alerts dynamically. The code also includes a direct link to the `register.php` page.