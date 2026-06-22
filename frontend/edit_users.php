**edit_users.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$existingRecord = json_decode(file_get_contents('../backend/users.php?id=' . $id), true);

// Check if record exists
if (empty($existingRecord)) {
    echo 'Record not found!';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-md shadow-md">
        <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit User</h2>
        <form id="edit-user-form">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-md border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" value="<?= $existingRecord['name'] ?>">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-md border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600" value="<?= $existingRecord['email'] ?>">
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-md border border-gray-300 focus:ring-emerald-600 focus:border-emerald-600">
                    <option value="admin" <?= $existingRecord['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="moderator" <?= $existingRecord['role'] == 'moderator' ? 'selected' : '' ?>>Moderator</option>
                    <option value="user" <?= $existingRecord['role'] == 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Update User</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-user-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/users.php',
                    data: formData,
                    success: function(response) {
                        window.location.href = 'list_users.php';
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>


**users.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user ID from URL
$id = $_GET['id'];

// Fetch existing record details from database
$existingRecord = get_user_by_id($id);

// Return JSON response
echo json_encode($existingRecord);

// Function to get user by ID
function get_user_by_id($id) {
    // Connect to database
    $db = new PDO('mysql:host=localhost;dbname=database_name', 'username', 'password');
    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    $db = null;
    return $result;
}
?>


**users.php (backend) - Update User**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user ID from URL
$id = $_GET['id'];

// Get updated user data
$name = $_POST['name'];
$email = $_POST['email'];
$role = $_POST['role'];

// Update user in database
update_user($id, $name, $email, $role);

// Redirect to list_users.php
header('Location: list_users.php');
exit;

// Function to update user
function update_user($id, $name, $email, $role) {
    // Connect to database
    $db = new PDO('mysql:host=localhost;dbname=database_name', 'username', 'password');
    $stmt = $db->prepare('UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $db = null;
}
?>