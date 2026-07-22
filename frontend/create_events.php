**create_events.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
require_once 'header.php';
require_once 'nav.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12 2xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 lg:p-8 xl:p-8 2xl:p-8">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Create New Event</h2>
        <form id="create-event-form">
            <div class="mb-4">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                <input type="text" id="title" name="title" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Date</label>
                <input type="date" id="date" name="date" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="time" class="block text-sm font-bold text-gray-700 mb-2">Time</label>
                <input type="time" id="time" name="time" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="location" class="block text-sm font-bold text-gray-700 mb-2">Location</label>
                <input type="text" id="location" name="location" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" required>
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">Create Event</button>
        </form>
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
                    if (response === 'success') {
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
require_once 'footer.php';
?>


**events.php (backend)**

<?php
// Include database connection
require_once 'db.php';

// Check if form data is submitted
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['location'])) {
    // Prepare SQL query
    $sql = "INSERT INTO events (title, description, date, time, location) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $_POST['title'], $_POST['description'], $_POST['date'], $_POST['time'], $_POST['location']);
    // Execute query
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error creating event';
    }
    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>


**Note:** This code assumes you have a `db.php` file that establishes a connection to your database and a `footer.php` file that includes the closing HTML tags. You'll need to modify the code to fit your specific database schema and file structure.