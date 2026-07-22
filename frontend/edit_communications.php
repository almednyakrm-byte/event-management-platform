**edit_communications.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get id from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$existingRecord = json_decode(file_get_contents('../backend/communications.php?id=' . $id), true);

// Check if record exists
if (empty($existingRecord)) {
    echo 'Record not found';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Communications</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded shadow-md">
        <h1 class="text-lg font-bold text-emerald-600 mb-4">Edit Communications</h1>
        <form id="edit-communications-form">
            <div class="mb-4">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Title:</label>
                <input type="text" id="title" name="title" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-emerald-600 focus:border-emerald-600" value="<?= $existingRecord['title'] ?>">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description:</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-emerald-600 focus:border-emerald-600"><?= $existingRecord['description'] ?></textarea>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status:</label>
                <select id="status" name="status" class="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="active" <?= $existingRecord['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $existingRecord['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Update</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-communications-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/communications.php',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'list_mod_slug.php';
                        } else {
                            alert('Error updating record');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

**communications.php (backend)**

<?php
// Check if id is set
if (!isset($_GET['id'])) {
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

// Get id from URL
$id = $_GET['id'];

// Fetch existing record details from database
// Replace with your actual database query
$result = mysqli_query($conn, "SELECT * FROM communications WHERE id = '$id'");

// Check if record exists
if (mysqli_num_rows($result) > 0) {
    $record = mysqli_fetch_assoc($result);
    echo json_encode($record);
} else {
    echo json_encode(array('error' => 'Record not found'));
}
?>

Note: Replace `list_mod_slug.php` with the actual URL of the page you want to redirect to after updating the record. Also, replace `../backend/communications.php` with the actual URL of the backend script.