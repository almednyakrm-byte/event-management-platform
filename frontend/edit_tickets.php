**edit_tickets.php**

<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get ticket ID from URL
$id = $_GET['id'];

// Fetch existing ticket details via AJAX
$js = "
<script>
    $(document).ready(function() {
        $.get('../backend/tickets.php?id=" . $id . "', function(data) {
            $('#title').val(data.title);
            $('#description').val(data.description);
            $('#priority').val(data.priority);
            $('#status').val(data.status);
        });
    });
</script>
";

// Include header
include 'header.php';

// Display form
?>

<div class="container mx-auto p-4 mt-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit Ticket</h2>
        <form id="edit-ticket-form">
            <div class="mb-4">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                <input type="text" id="title" name="title" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="priority" class="block text-sm font-bold text-gray-700 mb-2">Priority</label>
                <select id="priority" name="priority" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="block w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">Save Changes</button>
        </form>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>

<script>
    $(document).ready(function() {
        $('#edit-ticket-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'PUT',
                url: '../backend/tickets.php',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success) {
                        window.location.href = 'list_tickets.php';
                    } else {
                        alert('Error updating ticket');
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
    <title>Edit Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php echo $js; ?>
    <div class="container mx-auto p-4 mt-6">
        <nav class="bg-white border-b border-gray-200 p-4">
            <ul class="flex justify-between items-center">
                <li><a href="list_tickets.php" class="text-lg font-bold text-emerald-600">Tickets</a></li>
                <li><a href="logout.php" class="text-lg font-bold text-teal-500">Logout</a></li>
            </ul>
        </nav>
    </div>


**footer.php**

</body>
</html>


**tickets.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get ticket ID from URL
$id = $_GET['id'];

// Fetch existing ticket details
$ticket = get_ticket($id);

// Update ticket details via AJAX
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    update_ticket($id, $title, $description, $priority, $status);

    echo json_encode(['success' => true]);
    exit;
}

// Function to get ticket details
function get_ticket($id) {
    // Database connection code here
    // ...
    return $ticket;
}

// Function to update ticket details
function update_ticket($id, $title, $description, $priority, $status) {
    // Database connection code here
    // ...
}
?>