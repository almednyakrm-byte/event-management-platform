**list_schedules.php**

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
    <title>Schedules</title>
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
            <a href="index.php" class="text-lg font-bold">Home</a>
            <div class="flex items-center">
                <p class="mr-4">Welcome, <?= $_SESSION['username'] ?></p>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">Logout</button>
            </div>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Schedules</h1>
        <div class="flex justify-between items-center mb-4">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_schedules.php'">Add New Item</button>
            <input type="search" class="w-full p-2 mb-2 border border-gray-400 rounded" placeholder="Search..." id="search-input">
        </div>
        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr>
                    <th class="p-2 border border-gray-400">ID</th>
                    <th class="p-2 border border-gray-400">Name</th>
                    <th class="p-2 border border-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody id="schedule-list">
                <!-- List of schedules will be rendered here -->
            </tbody>
        </table>
    </main>

    <script>
        // Search bar filtering
        const searchInput = document.getElementById('search-input');
        const scheduleList = document.getElementById('schedule-list');

        searchInput.addEventListener('input', () => {
            const searchQuery = searchInput.value.toLowerCase();
            const schedules = Array.from(scheduleList.children);
            schedules.forEach((schedule, index) => {
                const scheduleName = schedule.children[1].textContent.toLowerCase();
                if (scheduleName.includes(searchQuery)) {
                    schedule.style.display = 'table-row';
                } else {
                    schedule.style.display = 'none';
                }
            });
        });

        // Fetch list of schedules
        fetch('../backend/schedules.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(schedule => {
                    const scheduleRow = document.createElement('tr');
                    scheduleRow.innerHTML = `
                        <td class="p-2 border border-gray-400">${schedule.id}</td>
                        <td class="p-2 border border-gray-400">${schedule.name}</td>
                        <td class="p-2 border border-gray-400">
                            <a href="edit_schedules.php?id=${schedule.id}" class="text-teal-500 hover:text-teal-700">Edit</a>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteSchedule(${schedule.id})">Delete</button>
                        </td>
                    `;
                    scheduleList.appendChild(scheduleRow);
                });
            })
            .catch(error => console.error(error));

        // Delete schedule
        function deleteSchedule(id) {
            fetch(`../backend/delete_schedule.php?id=${id}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Schedule deleted successfully');
                        location.reload();
                    } else {
                        console.error('Error deleting schedule');
                    }
                })
                .catch(error => console.error(error));
        }
    </script>
</body>
</html>


**schedules.php (backend)**

<?php
// Database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'database');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch list of schedules
$query = "SELECT * FROM schedules";
$result = mysqli_query($conn, $query);

$schedules = array();
while ($row = mysqli_fetch_assoc($result)) {
    $schedules[] = $row;
}

// Output JSON data
header('Content-Type: application/json');
echo json_encode($schedules);

// Close connection
mysqli_close($conn);
?>


**delete_schedule.php (backend)**

<?php
// Database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'database');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Delete schedule
$id = $_GET['id'];
$query = "DELETE FROM schedules WHERE id = '$id'";
mysqli_query($conn, $query);

// Output JSON data
header('Content-Type: application/json');
echo json_encode(array('success' => true));

// Close connection
mysqli_close($conn);
?>