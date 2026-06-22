**list_tickets.php**

<?php
session_start();

// Check if user is authenticated
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
                <p class="mr-2">Hello, <?php echo $_SESSION['username']; ?></p>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">Logout</button>
            </div>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">Tickets</h1>
        <div class="flex justify-between mb-4">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_tickets.php'">Add New Item</button>
            <input type="search" class="w-full p-2 pl-10 text-sm text-gray-700 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600" placeholder="Search..." id="search-input">
        </div>
        <table class="w-full table-auto border-collapse border border-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="tickets-list">
                <?php
                // Fetch list records from backend
                $response = file_get_contents('../backend/tickets.php');
                $tickets = json_decode($response, true);
                foreach ($tickets as $ticket) {
                    ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo $ticket['id']; ?></td>
                        <td class="px-4 py-2"><?php echo $ticket['title']; ?></td>
                        <td class="px-4 py-2"><?php echo $ticket['description']; ?></td>
                        <td class="px-4 py-2 flex justify-between">
                            <a href="edit_tickets.php?id=<?php echo $ticket['id']; ?>" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Edit</a>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteTicket(<?php echo $ticket['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </main>

    <script>
        const searchInput = document.getElementById('search-input');
        const ticketsList = document.getElementById('tickets-list');

        searchInput.addEventListener('input', () => {
            const searchQuery = searchInput.value.toLowerCase();
            const tickets = document.querySelectorAll('#tickets-list tr');
            tickets.forEach((ticket) => {
                const title = ticket.cells[1].textContent.toLowerCase();
                const description = ticket.cells[2].textContent.toLowerCase();
                if (title.includes(searchQuery) || description.includes(searchQuery)) {
                    ticket.style.display = 'table-row';
                } else {
                    ticket.style.display = 'none';
                }
            });
        });

        async function deleteTicket(id) {
            const response = await fetch('../backend/tickets.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            if (response.ok) {
                location.reload();
            } else {
                alert('Error deleting ticket');
            }
        }
    </script>
</body>
</html>

**tickets.php (backend)**

<?php
header('Content-Type: application/json');

// Connect to database
$conn = new PDO('mysql:host=localhost;dbname=tickets', 'username', 'password');

// Fetch list records
$stmt = $conn->prepare('SELECT * FROM tickets');
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output JSON response
echo json_encode($tickets);
$conn = null;
?>

Note: Replace `'username'` and `'password'` with your actual database credentials. Also, make sure to create a `tickets` table in your database with the necessary columns (e.g., `id`, `title`, `description`).