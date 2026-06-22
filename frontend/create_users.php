**create_users.php**

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
?>

<div class="container mx-auto p-4 pt-6">
    <div class="bg-white rounded shadow-md p-4">
        <h2 class="text-emerald-600 text-lg font-bold mb-4">Create New User</h2>
        <form id="create-user-form">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                <select id="role" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderator</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create User</button>
        </form>
    </div>
</div>

<?php
// Include footer
require_once 'footer.php';
?>

<script>
    $(document).ready(function() {
        $('#create-user-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/users.php',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'list_users.php';
                    } else {
                        alert('Error creating user');
                    }
                }
            });
        });
    });
</script>


**users.php (backend)**

<?php
// Include database connection
require_once 'db.php';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        echo json_encode(array('success' => false));
        exit;
    }

    // Hash password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
}
?>


**Note:** This code assumes you have a `db.php` file that connects to your database and a `header.php` and `footer.php` file that includes the HTML header and footer respectively. You will need to modify the code to fit your specific database schema and requirements. Additionally, this code does not include any validation or sanitization of user input, which is a security risk. You should always validate and sanitize user input to prevent SQL injection and other security vulnerabilities.