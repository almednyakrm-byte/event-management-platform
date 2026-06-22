**create_attendees.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include_once 'header.php';
include_once 'nav.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Add New Attendee</h2>
        <form id="attendee-form">
            <div class="mb-4">
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Name:</label>
                <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email:</label>
                <input type="email" id="email" name="email" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Phone:</label>
                <input type="tel" id="phone" name="phone" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div class="mb-4">
                <label for="address" class="block text-sm font-bold text-gray-700 mb-2">Address:</label>
                <textarea id="address" name="address" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500"></textarea>
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Add Attendee</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#attendee-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../backend/attendees.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_attendees.php';
                    } else {
                        alert('Error adding attendee');
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


**attendees.php (backend)**

<?php
// Include database connection
include_once 'db.php';

// Check if form data is submitted
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['address'])) {
    // Prepare SQL query
    $query = "INSERT INTO attendees (name, email, phone, address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address']);
    $stmt->execute();
    $stmt->close();
    echo 'success';
} else {
    echo 'Error adding attendee';
}
?>


**Note:** Make sure to replace `db.php` with your actual database connection file and `list_attendees.php` with the actual file name where you want to redirect after adding a new attendee. Also, make sure to adjust the SQL query in `attendees.php` according to your database schema.