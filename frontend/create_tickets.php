**create_tickets.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
include 'navigation.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:px-12">
    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6 lg:p-8">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Create New Ticket</h2>
        <form id="create-ticket-form">
            <div class="mb-4">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                <input type="text" id="title" name="title" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-emerald-600" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-emerald-600" required></textarea>
            </div>
            <div class="mb-4">
                <label for="priority" class="block text-sm font-bold text-gray-700 mb-2">Priority</label>
                <select id="priority" name="priority" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-emerald-600" required>
                    <option value="">Select Priority</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-emerald-600" required>
                    <option value="">Select Status</option>
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                </select>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-lg">Create Ticket</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create-ticket-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/tickets.php',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'list_tickets.php';
                    } else {
                        alert('Error creating ticket: ' + response);
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**tickets.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['priority']) && isset($_POST['status'])) {
    // Insert data into database
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $query = "INSERT INTO tickets (title, description, priority, status) VALUES ('$title', '$description', '$priority', '$status')";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo 'success';
    } else {
        echo 'Error creating ticket: ' . mysqli_error($conn);
    }
} else {
    echo 'Error creating ticket: Invalid form data';
}
?>