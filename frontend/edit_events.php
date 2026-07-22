**edit_events.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get event ID from URL
$id = $_GET['id'];

// Fetch existing event details via GET
$url = '../backend/events.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if event exists
if (empty($data)) {
    echo 'Event not found.';
    exit;
}

// Set event details
$event_name = $data['name'];
$event_date = $data['date'];
$event_time = $data['time'];
$event_location = $data['location'];
$event_description = $data['description'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit Event</h2>
        <form id="edit-event-form">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Event Name</label>
                <input type="text" id="name" name="name" class="block w-full p-2 pl-10 text-sm text-gray-700 bg-gray-50 rounded-lg border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" value="<?= $event_name ?>">
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" id="date" name="date" class="block w-full p-2 pl-10 text-sm text-gray-700 bg-gray-50 rounded-lg border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" value="<?= $event_date ?>">
            </div>
            <div class="mb-4">
                <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                <input type="time" id="time" name="time" class="block w-full p-2 pl-10 text-sm text-gray-700 bg-gray-50 rounded-lg border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" value="<?= $event_time ?>">
            </div>
            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" id="location" name="location" class="block w-full p-2 pl-10 text-sm text-gray-700 bg-gray-50 rounded-lg border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" value="<?= $event_location ?>">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 pl-10 text-sm text-gray-700 bg-gray-50 rounded-lg border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" rows="4"><?= $event_description ?></textarea>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Update Event</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-event-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/events.php',
                    data: formData,
                    success: function(response) {
                        window.location.href = 'list_events.php';
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>


**events.php (backend)**

<?php
// Check if event ID is set
if (!isset($_GET['id'])) {
    echo json_encode(array());
    exit;
}

// Get event ID
$id = $_GET['id'];

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query event details
$sql = "SELECT * FROM events WHERE id = '$id'";
$result = $conn->query($sql);

// Check if event exists
if ($result->num_rows > 0) {
    // Fetch event details
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(array());
}

// Close connection
$conn->close();
?>


**Note:** Replace `'localhost'`, `'username'`, `'password'`, and `'database'` with your actual database credentials and name. Also, make sure to update the `list_events.php` URL in the JavaScript code to match your actual file path.