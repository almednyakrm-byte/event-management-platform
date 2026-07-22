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
        body {
            background-color: #f7f7f7;
        }
        .header {
            background-color: #1a1d23;
            padding: 1rem;
            text-align: center;
        }
        .header a {
            color: #fff;
            text-decoration: none;
        }
        .header a:hover {
            color: #ccc;
        }
        .table {
            width: 80%;
            margin: 2rem auto;
        }
        .table th, .table td {
            padding: 1rem;
            border: 1px solid #ddd;
        }
        .table th {
            background-color: #f0f0f0;
        }
        .search-bar {
            width: 50%;
            margin: 2rem auto;
        }
        .search-bar input[type="search"] {
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            width: 100%;
        }
        .search-bar button[type="submit"] {
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
            background-color: #1a1d23;
            color: #fff;
            cursor: pointer;
        }
        .search-bar button[type="submit"]:hover {
            background-color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Back to Index</a>
        <span>Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container mx-auto p-4">
        <div class="search-bar">
            <input type="search" id="search" placeholder="Search events...">
            <button type="submit" id="search-btn">Search</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="events-list">
                <!-- List of events will be populated here -->
            </tbody>
        </table>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_events.php'">Add New Item</button>
    </div>

    <script>
        // Fetch API to get list of events
        fetch('../backend/events.php')
            .then(response => response.json())
            .then(data => {
                const eventsList = document.getElementById('events-list');
                data.forEach(event => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${event.id}</td>
                        <td>${event.name}</td>
                        <td>${event.description}</td>
                        <td>
                            <a href="edit_events.php?id=${event.id}" class="text-teal-500 hover:text-teal-700">Edit</a>
                            <button class="text-red-500 hover:text-red-700" onclick="deleteEvent(${event.id})">Delete</button>
                        </td>
                    `;
                    eventsList.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));

        // Function to delete an event
        function deleteEvent(id) {
            fetch(`../backend/events.php?delete=${id}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Event deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting event!');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Search functionality
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('search-btn');
        searchBtn.addEventListener('click', () => {
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetch(`../backend/events.php?search=${searchQuery}`)
                    .then(response => response.json())
                    .then(data => {
                        const eventsList = document.getElementById('events-list');
                        eventsList.innerHTML = '';
                        data.forEach(event => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${event.id}</td>
                                <td>${event.name}</td>
                                <td>${event.description}</td>
                                <td>
                                    <a href="edit_events.php?id=${event.id}" class="text-teal-500 hover:text-teal-700">Edit</a>
                                    <button class="text-red-500 hover:text-red-700" onclick="deleteEvent(${event.id})">Delete</button>
                                </td>
                            `;
                            eventsList.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                fetch('../backend/events.php')
                    .then(response => response.json())
                    .then(data => {
                        const eventsList = document.getElementById('events-list');
                        eventsList.innerHTML = '';
                        data.forEach(event => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${event.id}</td>
                                <td>${event.name}</td>
                                <td>${event.description}</td>
                                <td>
                                    <a href="edit_events.php?id=${event.id}" class="text-teal-500 hover:text-teal-700">Edit</a>
                                    <button class="text-red-500 hover:text-red-700" onclick="deleteEvent(${event.id})">Delete</button>
                                </td>
                            `;
                            eventsList.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>
</html>

Note: This code assumes that you have a `events.php` file in the `../backend` directory that handles GET and DELETE requests for events. You'll need to create this file and implement the necessary logic to retrieve and delete events.