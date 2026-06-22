**edit_tickets.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get ticket ID from URL
$id = $_GET['id'];

// Fetch existing ticket details via AJAX
$js = '
    $(document).ready(function() {
        $.get("../backend/tickets.php?id=' . $id . '")
            .done(function(data) {
                $("#title").val(data.title);
                $("#description").val(data.description);
                $("#priority").val(data.priority);
                $("#status").val(data.status);
            })
            .fail(function() {
                alert("Error fetching ticket details");
            });
    });
';

// Include JavaScript code
echo '<script>' . $js . '</script>';

// Form HTML
?>

<div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit Ticket</h2>
    <form id="edit-ticket-form" class="space-y-4">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="block w-full p-2 pl-10 text-sm text-gray-700 border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" class="block w-full p-2 pl-10 text-sm text-gray-700 border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500"></textarea>
        </div>
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
            <select id="priority" name="priority" class="block w-full p-2 pl-10 text-sm text-gray-700 border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="block w-full p-2 pl-10 text-sm text-gray-700 border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
            </select>
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">Update Ticket</button>
    </form>
</div>

<?php
// AJAX PUT request to update ticket
$js .= '
    $(document).ready(function() {
        $("#edit-ticket-form").submit(function(event) {
            event.preventDefault();
            $.ajax({
                type: "PUT",
                url: "../backend/tickets.php",
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success) {
                        window.location.href = "list_' . $_SESSION['mod_slug'] . '.php";
                    } else {
                        alert("Error updating ticket");
                    }
                },
                error: function() {
                    alert("Error updating ticket");
                }
            });
        });
    });
';

// Include JavaScript code
echo '<script>' . $js . '</script>';
?>


**tickets.php (backend)**

<?php
// Check if ticket ID is set
if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

// Get ticket ID
$id = $_GET['id'];

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get ticket details
$query = "SELECT * FROM tickets WHERE id = '$id'";
$result = $conn->query($query);

// Check if ticket exists
if ($result->num_rows > 0) {
    // Fetch ticket details
    $ticket = $result->fetch_assoc();
    echo json_encode($ticket);
} else {
    echo json_encode(array('error' => 'Ticket not found'));
}

// Close database connection
$conn->close();
?>


**PUT request (backend)**

<?php
// Check if ticket ID is set
if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

// Get ticket ID
$id = $_GET['id'];

// Get updated ticket details
$title = $_POST['title'];
$description = $_POST['description'];
$priority = $_POST['priority'];
$status = $_POST['status'];

// Connect to database
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to update ticket
$query = "UPDATE tickets SET title = '$title', description = '$description', priority = '$priority', status = '$status' WHERE id = '$id'";
$conn->query($query);

// Check if update was successful
if ($conn->affected_rows > 0) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));
}

// Close database connection
$conn->close();
?>