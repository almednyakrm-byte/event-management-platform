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
    <title>منصة إدارة أحداث ومؤتمرات</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }
        .glassmorphism {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
        <h1 class="text-2xl font-bold">منصة إدارة أحداث ومؤتمرات</h1>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="logout()">تسجيل خروج</button>
    </div>
    <div class="flex justify-center items-center p-4 bg-white border-b border-gray-200">
        <h2 class="text-2xl font-bold">مرحباً <?= $_SESSION['username'] ?></h2>
    </div>
    <div class="flex justify-center items-center p-4 bg-white border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            // Fetch stats dynamically via Javascript API calls from the backend files
            ?>
            <div class="glassmorphism bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                <h3 class="text-lg font-bold">إحصائيات</h3>
                <p id="stats-events" class="text-lg"></p>
                <p id="stats-schedules" class="text-lg"></p>
                <p id="stats-tickets" class="text-lg"></p>
                <p id="stats-communications" class="text-lg"></p>
            </div>
            <div class="glassmorphism bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                <h3 class="text-lg font-bold">إدارة الحوادث</h3>
                <a href="events.php" class="text-lg">إدارة الحوادث</a>
            </div>
            <div class="glassmorphism bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                <h3 class="text-lg font-bold">إدارة الجدول</h3>
                <a href="schedules.php" class="text-lg">إدارة الجدول</a>
            </div>
            <div class="glassmorphism bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                <h3 class="text-lg font-bold">إدارة التذاكر</h3>
                <a href="tickets.php" class="text-lg">إدارة التذاكر</a>
            </div>
            <div class="glassmorphism bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                <h3 class="text-lg font-bold">إدارة المراسلات</h3>
                <a href="communications.php" class="text-lg">إدارة المراسلات</a>
            </div>
        </div>
    </div>
    <script>
        function logout() {
            window.location.href = 'logout.php';
        }

        // Fetch stats dynamically via Javascript API calls from the backend files
        fetch('api/stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('stats-events').innerHTML = `حوادث: ${data.events}`;
                document.getElementById('stats-schedules').innerHTML = `جدول: ${data.schedules}`;
                document.getElementById('stats-tickets').innerHTML = `تذاكر: ${data.tickets}`;
                document.getElementById('stats-communications').innerHTML = `مراسلات: ${data.communications}`;
            })
            .catch(error => console.error('Error:', error));
    </script>
</body>
</html>


This code uses Tailwind CSS for styling and includes a logout button that redirects to `logout.php`. The dashboard layout includes a welcome message, an overview stats grid, and quick links to manage modules. The stats are fetched dynamically via a Javascript API call to `api/stats.php`.

Please note that you need to create the `api/stats.php` file to handle the API call and return the stats data. You also need to create the `logout.php` file to handle the logout functionality.

Also, make sure to replace the `events.php`, `schedules.php`, `tickets.php`, and `communications.php` files with your actual module management pages.