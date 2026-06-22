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
    <title>منصة إدارة أحداث وفعاليات مع دفع وتأكيد الحضور</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .glassmorphism-card {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="flex justify-between items-center p-4 bg-emerald-600 text-white">
        <h1 class="text-3xl font-bold">منصة إدارة أحداث وفعاليات مع دفع وتأكيد الحضور</h1>
        <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">تسجيل خروج</button>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 p-4">
            <h2 class="text-2xl font-bold">مرحباً <?php echo $_SESSION['username']; ?></h2>
            <p class="text-lg">منصة إدارة أحداث وفعاليات مع دفع وتأكيد الحضور</p>
        </div>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 p-4">
            <h2 class="text-2xl font-bold">إحصائيات</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">عدد الأحداث</h3>
                    <p id="events-count" class="text-lg"></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">عدد الحضور</h3>
                    <p id="attendees-count" class="text-lg"></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">إجمالي الدفع</h3>
                    <p id="payments-total" class="text-lg"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center items-center p-4">
        <div class="glassmorphism-card w-1/2 p-4">
            <h2 class="text-2xl font-bold">روابط سريعة</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">إدارة الأحداث</h3>
                    <p><a href="events.php" class="text-lg">اضغط هنا</a></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">إدارة الحضور</h3>
                    <p><a href="attendees.php" class="text-lg">اضغط هنا</a></p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">إدارة الدفع</h3>
                    <p><a href="payments.php" class="text-lg">اضغط هنا</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch('api/stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('events-count').innerHTML = data.events_count;
                document.getElementById('attendees-count').innerHTML = data.attendees_count;
                document.getElementById('payments-total').innerHTML = data.payments_total;
            })
            .catch(error => console.error(error));
    </script>
</body>
</html>


This code uses Tailwind CSS for styling and makes API calls to fetch dynamic stats from the backend. It also includes a session check to redirect to the login page if the user is not authenticated. The dashboard layout includes a welcome message, logout button, overview stats grid, and quick links to manage modules.