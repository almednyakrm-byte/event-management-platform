**create_schedules.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include header
include 'header.php';

// Include navigation
include 'navigation.php';

// Include form
include 'create_schedules_form.php';

// Include footer
include 'footer.php';


**create_schedules_form.php**

<!-- Create Schedules Form -->
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-lg leading-6 font-medium text-gray-900">Create Schedules</h2>
            <form id="create-schedules-form" class="mt-6 space-y-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="schedule_name" class="block text-sm font-medium text-gray-700">Schedule Name</label>
                        <input type="text" id="schedule_name" name="schedule_name" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-600 focus:border-emerald-600" placeholder="Schedule Name">
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-600 focus:border-emerald-600">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-600 focus:border-emerald-600" rows="4"></textarea>
                    </div>
                </div>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">Create Schedule</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-schedules-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/schedules.php',
                data: formData,
                success: function(response) {
                    if (response == 'success') {
                        window.location.href = 'list_schedules.php';
                    } else {
                        alert('Error creating schedule');
                    }
                }
            });
        });
    });
</script>


**header.php**

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .emerald-600 {
            color: #34C759;
        }
        .teal-500 {
            color: #0097A7;
        }
    </style>
</head>
<body>
    <header>
        <!-- Navigation bar -->
        <?php include 'navigation.php'; ?>
    </header>
    <main>
        <!-- Page content -->
        <?php include 'create_schedules.php'; ?>
    </main>
</body>
</html>


**footer.php**

<footer>
    <!-- Footer content -->
    &copy; 2023
</footer>


**navigation.php**

<nav class="bg-gray-800">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between">
            <div class="flex items-center">
                <a href="#" class="text-white hover:text-gray-300">Home</a>
                <a href="#" class="text-white hover:text-gray-300">About</a>
            </div>
            <div class="flex items-center">
                <a href="#" class="text-white hover:text-gray-300">Login</a>
                <a href="#" class="text-white hover:text-gray-300">Register</a>
            </div>
        </div>
    </div>
</nav>