**edit_attendees.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get attendee ID from URL
$id = $_GET['id'];

// Fetch existing record details via AJAX
$js = "
    $(document).ready(function() {
        $.get('../backend/attendees.php?id=" . $id . "')
            .done(function(data) {
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
            })
            .fail(function() {
                console.error('Failed to fetch attendee details');
            });
    });
";

// Include JavaScript code
echo "<script>$js</script>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendee</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-md shadow-md">
        <h1 class="text-lg font-bold text-emerald-600 mb-4">Edit Attendee</h1>
        <form id="attendee-form" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="tel" id="phone" name="phone" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500">
            </div>
            <button type="submit" class="w-full px-4 py-2 text-white bg-emerald-600 rounded-md hover:bg-emerald-700">Save Changes</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#attendee-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/attendees.php',
                    data: formData,
                    success: function() {
                        window.location.href = 'list_attendees.php';
                    },
                    error: function() {
                        console.error('Failed to update attendee');
                    }
                });
            });
        });
    </script>
</body>
</html>


**Note:** This code assumes that you have a `backend/attendees.php` file that handles the GET and PUT requests for attendee data. The `list_attendees.php` file is also assumed to be present in the same directory.