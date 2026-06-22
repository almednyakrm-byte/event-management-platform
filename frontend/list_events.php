**list_events.php**

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
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .emerald-600 {
            color: #008E77;
        }
        .teal-500 {
            color: #0097A7;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <header class="flex justify-between items-center mb-4">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <div class="flex items-center">
                <p class="mr-2">Welcome, <?php echo $_SESSION['username']; ?></p>
                <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
            </div>
        </header>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-lg font-bold">Events</h1>
            <a href="create_events.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
        </div>
        <div class="flex justify-between items-center mb-4">
            <input type="search" id="search" placeholder="Search events" class="w-full p-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600">
            <button id="search-btn" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Search</button>
        </div>
        <table class="w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Event Name</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="events-list">
                <!-- List of events will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
        // Get search input and button
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('search-btn');

        // Add event listener to search button
        searchBtn.addEventListener('click', () => {
            // Get search query
            const query = searchInput.value.trim();

            // Make GET request to backend to fetch events
            fetch('../backend/events.php?search=' + query)
                .then(response => response.json())
                .then(data => {
                    // Populate events list
                    const eventsList = document.getElementById('events-list');
                    eventsList.innerHTML = '';
                    data.forEach(event => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-4 py-2">${event.name}</td>
                            <td class="px-4 py-2">${event.date}</td>
                            <td class="px-4 py-2">
                                <a href="edit_events.php?id=${event.id}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                <button class="text-red-600 hover:text-red-800" onclick="deleteEvent(${event.id})">Delete</button>
                            </td>
                        `;
                        eventsList.appendChild(row);
                    });
                })
                .catch(error => console.error(error));
        });

        // Function to delete event
        function deleteEvent(id) {
            // Make DELETE request to backend to delete event
            fetch('../backend/events.php?id=' + id, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove event from list
                        const eventsList = document.getElementById('events-list');
                        const row = eventsList.querySelector(`tr[data-id="${id}"]`);
                        row.remove();
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => console.error(error));
        }
    </script>
</body>
</html>


**events.php (backend)**

<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query
$search = $_GET['search'] ?? '';

// Query to fetch events
$query = "SELECT * FROM events";
if ($search) {
    $query .= " WHERE name LIKE '%$search%'";
}

// Execute query
$result = $conn->query($query);

// Fetch events
$events = array();
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Close connection
$conn->close();

// Output events in JSON format
header('Content-Type: application/json');
echo json_encode($events);
?>


**Note:** This code assumes you have a database table named `events` with columns `id`, `name`, and `date`. You'll need to modify the database connection and query to match your actual database schema. Additionally, this code uses a simple search query that searches for events with a name containing the search query. You may want to modify this to use a more advanced search query or indexing.