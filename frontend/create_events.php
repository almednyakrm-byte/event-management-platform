**create_events.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include_once 'header.php';
include_once 'nav.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:px-12 xl:px-24">
    <div class="flex flex-wrap -mx-4">
        <div class="w-full xl:w-8/12 p-4">
            <div class="bg-white rounded shadow-md">
                <h2 class="text-lg font-bold text-emerald-600 p-4">Create Event</h2>
                <form id="create-event-form">
                    <div class="p-4">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full xl:w-6/12 p-4">
                                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                                <input type="text" id="title" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Event Title">
                            </div>
                            <div class="w-full xl:w-6/12 p-4">
                                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                                <input type="date" id="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full xl:w-6/12 p-4">
                                <label for="time" class="block text-gray-700 text-sm font-bold mb-2">Time</label>
                                <input type="time" id="time" name="time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="w-full xl:w-6/12 p-4">
                                <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                                <input type="text" id="location" name="location" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Event Location">
                            </div>
                        </div>
                        <div class="p-4">
                            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Create Event</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-event-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/events.php',
                data: formData,
                success: function(response) {
                    if (response == 'success') {
                        window.location.href = 'list_events.php';
                    } else {
                        alert('Error creating event');
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
include_once 'footer.php';
?>


**events.php (backend)**

<?php
// Include database connection
include_once 'db.php';

// Check if form data is submitted
if (isset($_POST['title']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['location'])) {
    // Prepare SQL query
    $query = "INSERT INTO events (title, date, time, location) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssss", $_POST['title'], $_POST['date'], $_POST['time'], $_POST['location']);
    // Execute query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error creating event';
    }
    $stmt->close();
}
?>


**Note:** This code assumes you have a `db.php` file that includes your database connection settings and a `footer.php` file that includes the closing HTML tags. You'll need to modify the code to fit your specific database schema and backend setup. Additionally, this code does not include any error handling or validation, which you should add in a production environment.