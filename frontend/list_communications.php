**list_communications.php**

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
    <title>Communications</title>
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
    <div class="container mx-auto p-4">
        <header class="bg-white shadow-md p-4 mb-4">
            <nav class="flex justify-between">
                <a href="index.php" class="text-lg font-bold">Home</a>
                <div class="flex items-center">
                    <p class="mr-2">Welcome, <?php echo $_SESSION['username']; ?></p>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
                </div>
            </nav>
        </header>
        <div class="bg-white shadow-md p-4 mb-4">
            <h2 class="text-lg font-bold mb-2">Communications</h2>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="location.href='create_communications.php'">Add New Item</button>
            <div class="flex justify-between mb-4">
                <input type="search" id="search" class="w-full p-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" placeholder="Search...">
                <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="searchRecords()">Search</button>
            </div>
            <table class="w-full border-collapse border border-gray-400">
                <thead>
                    <tr>
                        <th class="border border-gray-400 px-4 py-2">ID</th>
                        <th class="border border-gray-400 px-4 py-2">Name</th>
                        <th class="border border-gray-400 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody id="records">
                    <?php
                    // Fetch records from backend
                    $records = fetchRecords();
                    foreach ($records as $record) {
                        ?>
                        <tr>
                            <td class="border border-gray-400 px-4 py-2"><?php echo $record['id']; ?></td>
                            <td class="border border-gray-400 px-4 py-2"><?php echo $record['name']; ?></td>
                            <td class="border border-gray-400 px-4 py-2">
                                <a href="edit_communications.php?id=<?php echo $record['id']; ?>" class="text-emerald-600 hover:text-emerald-800">Edit</a>
                                <button class="text-red-600 hover:text-red-800" onclick="deleteRecord(<?php echo $record['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function searchRecords() {
            const search = document.getElementById('search').value;
            fetch('../backend/communications.php?search=' + search)
                .then(response => response.json())
                .then(data => {
                    const records = document.getElementById('records');
                    records.innerHTML = '';
                    data.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="border border-gray-400 px-4 py-2">${record.id}</td>
                            <td class="border border-gray-400 px-4 py-2">${record.name}</td>
                            <td class="border border-gray-400 px-4 py-2">
                                <a href="edit_communications.php?id=${record.id}" class="text-emerald-600 hover:text-emerald-800">Edit</a>
                                <button class="text-red-600 hover:text-red-800" onclick="deleteRecord(${record.id})">Delete</button>
                            </td>
                        `;
                        records.appendChild(row);
                    });
                });
        }

        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                fetch('../backend/communications.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Record deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error deleting record!');
                    }
                });
            }
        }

        function fetchRecords() {
            return fetch('../backend/communications.php')
                .then(response => response.json())
                .then(data => data.records);
        }
    </script>
</body>
</html>

<?php
function fetchRecords() {
    $records = array();
    // Fetch records from backend
    // Replace with actual backend code
    $records = array(
        array('id' => 1, 'name' => 'Record 1'),
        array('id' => 2, 'name' => 'Record 2'),
        array('id' => 3, 'name' => 'Record 3')
    );
    return $records;
}
?>


**communications.php (backend)**

<?php
// Replace with actual backend code
$records = array(
    array('id' => 1, 'name' => 'Record 1'),
    array('id' => 2, 'name' => 'Record 2'),
    array('id' => 3, 'name' => 'Record 3')
);

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $records = array_filter($records, function($record) use ($search) {
        return strpos($record['name'], $search) !== false;
    });
}

header('Content-Type: application/json');
echo json_encode(array('records' => $records));
?>


Note: This code assumes that you have a backend PHP script (`communications.php`) that fetches records from a database and returns them in JSON format. You should replace the `fetchRecords()` function in the frontend code with actual backend code that fetches records from your database.