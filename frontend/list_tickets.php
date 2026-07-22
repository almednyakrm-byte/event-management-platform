**list_tickets.php**

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
    <title>Tickets</title>
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
            <h1 class="text-lg font-bold">Tickets</h1>
            <a href="create_tickets.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Add New Item</a>
        </div>
        <div class="flex justify-between items-center mb-4">
            <input type="search" id="search" class="w-full p-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" placeholder="Search...">
            <button id="search-btn" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Search</button>
        </div>
        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr>
                    <th class="border border-gray-400 px-4 py-2">ID</th>
                    <th class="border border-gray-400 px-4 py-2">Title</th>
                    <th class="border border-gray-400 px-4 py-2">Description</th>
                    <th class="border border-gray-400 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="tickets-list">
                <!-- List of records will be fetched here -->
            </tbody>
        </table>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('search-btn');
        const ticketsList = document.getElementById('tickets-list');

        searchBtn.addEventListener('click', () => {
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetch('../backend/tickets.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        ticketsList.innerHTML = '';
                        data.forEach(ticket => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="border border-gray-400 px-4 py-2">${ticket.id}</td>
                                <td class="border border-gray-400 px-4 py-2">${ticket.title}</td>
                                <td class="border border-gray-400 px-4 py-2">${ticket.description}</td>
                                <td class="border border-gray-400 px-4 py-2">
                                    <a href="edit_tickets.php?id=${ticket.id}" class="text-emerald-600 hover:text-emerald-800">Edit</a>
                                    <button class="text-red-600 hover:text-red-800" onclick="deleteTicket(${ticket.id})">Delete</button>
                                </td>
                            `;
                            ticketsList.appendChild(row);
                        });
                    });
            } else {
                fetch('../backend/tickets.php')
                    .then(response => response.json())
                    .then(data => {
                        ticketsList.innerHTML = '';
                        data.forEach(ticket => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="border border-gray-400 px-4 py-2">${ticket.id}</td>
                                <td class="border border-gray-400 px-4 py-2">${ticket.title}</td>
                                <td class="border border-gray-400 px-4 py-2">${ticket.description}</td>
                                <td class="border border-gray-400 px-4 py-2">
                                    <a href="edit_tickets.php?id=${ticket.id}" class="text-emerald-600 hover:text-emerald-800">Edit</a>
                                    <button class="text-red-600 hover:text-red-800" onclick="deleteTicket(${ticket.id})">Delete</button>
                                </td>
                            `;
                            ticketsList.appendChild(row);
                        });
                    });
            }
        });

        function deleteTicket(id) {
            if (confirm('Are you sure you want to delete this ticket?')) {
                fetch('../backend/tickets.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ticket deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error deleting ticket!');
                    }
                });
            }
        }

        fetch('../backend/tickets.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(ticket => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border border-gray-400 px-4 py-2">${ticket.id}</td>
                        <td class="border border-gray-400 px-4 py-2">${ticket.title}</td>
                        <td class="border border-gray-400 px-4 py-2">${ticket.description}</td>
                        <td class="border border-gray-400 px-4 py-2">
                            <a href="edit_tickets.php?id=${ticket.id}" class="text-emerald-600 hover:text-emerald-800">Edit</a>
                            <button class="text-red-600 hover:text-red-800" onclick="deleteTicket(${ticket.id})">Delete</button>
                        </td>
                    `;
                    ticketsList.appendChild(row);
                });
            });
    </script>
</body>
</html>

**backend/tickets.php**

<?php
// Database connection code here
// ...

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $tickets = array_filter($tickets, function($ticket) use ($searchQuery) {
        return strpos($ticket['title'], $searchQuery) !== false || strpos($ticket['description'], $searchQuery) !== false;
    });
} else {
    $tickets = // fetch all tickets from database
}

header('Content-Type: application/json');
echo json_encode($tickets);

Note: This code assumes you have a `tickets` array containing the data for each ticket, and a `deleteTicket` function that deletes a ticket from the database. You'll need to modify the code to fit your specific database schema and implementation.