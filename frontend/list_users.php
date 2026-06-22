**list_users.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .emerald-600 {
            color: #008E73;
        }
        .teal-500 {
            color: #0097A7;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md p-4">
        <nav class="flex justify-between items-center">
            <a href="index.php" class="text-lg font-bold">Dashboard</a>
            <div class="flex items-center">
                <p class="mr-2">Welcome, <?= $_SESSION['username'] ?></p>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="document.location='logout.php'">Logout</button>
            </div>
        </nav>
    </header>
    <main class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Users</h1>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="document.location='create_users.php'">Add New User</button>
        <div class="flex justify-between items-center mb-4">
            <input type="search" id="search" class="w-full p-2 pl-10 text-sm text-gray-700 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Search...">
            <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="searchUsers()">Search</button>
        </div>
        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr>
                    <th class="border border-gray-400 p-2">ID</th>
                    <th class="border border-gray-400 p-2">Name</th>
                    <th class="border border-gray-400 p-2">Email</th>
                    <th class="border border-gray-400 p-2">Actions</th>
                </tr>
            </thead>
            <tbody id="users-table">
                <?php
                // Fetch users from backend
                $users = fetchUsers();
                foreach ($users as $user) {
                    ?>
                    <tr>
                        <td class="border border-gray-400 p-2"><?= $user['id'] ?></td>
                        <td class="border border-gray-400 p-2"><?= $user['name'] ?></td>
                        <td class="border border-gray-400 p-2"><?= $user['email'] ?></td>
                        <td class="border border-gray-400 p-2">
                            <a href="edit_users.php?id=<?= $user['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteUser(<?= $user['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </main>

    <script>
        function searchUsers() {
            const searchInput = document.getElementById('search');
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetchUsers(searchQuery);
            } else {
                fetchUsers();
            }
        }

        function fetchUsers(searchQuery = '') {
            const url = '../backend/users.php';
            const params = new URLSearchParams({
                search: searchQuery
            });
            const fetchUrl = `${url}?${params.toString()}`;
            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    const usersTable = document.getElementById('users-table');
                    usersTable.innerHTML = '';
                    data.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="border border-gray-400 p-2">${user.id}</td>
                            <td class="border border-gray-400 p-2">${user.name}</td>
                            <td class="border border-gray-400 p-2">${user.email}</td>
                            <td class="border border-gray-400 p-2">
                                <a href="edit_users.php?id=${user.id}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteUser(${user.id})">Delete</button>
                            </td>
                        `;
                        usersTable.appendChild(row);
                    });
                })
                .catch(error => console.error(error));
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                fetch(`../backend/users.php?delete=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchUsers();
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => console.error(error));
            }
        }
    </script>
</body>
</html>


**users.php (backend)**

<?php
// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search query
$searchQuery = $_GET['search'] ?? '';

// SQL query
$sql = "SELECT * FROM users";
if ($searchQuery) {
    $sql .= " WHERE name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%'";
}

$result = $conn->query($sql);

$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();

// Output users
header('Content-Type: application/json');
echo json_encode($users);
?>


**Note:** This is a basic example and you should adjust the code to fit your specific needs. Also, make sure to replace the placeholders (`username`, `password`, `database`) with your actual database credentials.