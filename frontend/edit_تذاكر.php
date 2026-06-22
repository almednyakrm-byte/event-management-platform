**edit_تذاكر.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$url = '../backend/تذاكر.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if data exists
if (empty($data)) {
    echo 'Error: Record not found.';
    exit;
}

// Set page title and breadcrumb
$page_title = 'Edit تذاكر';
$breadcrumb = 'تذاكر / Edit تذاكر';

// Include header and breadcrumb
include 'header.php';
include 'breadcrumb.php';
?>

<div class="container mx-auto p-4 pt-6">
    <div class="bg-white rounded shadow-md p-4">
        <h2 class="text-lg font-bold mb-4"><?= $page_title ?></h2>
        <form id="edit-takrar-form" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="block w-full p-2 border border-gray-300 rounded-md" value="<?= $data['name'] ?>">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" class="block w-full p-2 border border-gray-300 rounded-md"><?= $data['description'] ?></textarea>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
        </form>
    </div>
</div>

<script>
    // Fetch existing record details via GET
    fetch('../backend/تذاكر.php?id=<?= $id ?>')
        .then(response => response.json())
        .then(data => {
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
        })
        .catch(error => console.error(error));

    // Handle form submission
    document.getElementById('edit-takrar-form').addEventListener('submit', event => {
        event.preventDefault();

        // Send AJAX PUT request
        fetch('../backend/تذاكر.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: <?= $id ?>,
                name: document.getElementById('name').value,
                description: document.getElementById('description').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'list_<?= $mod_slug ?>.php';
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error(error));
    });
</script>


**backend/تذاكر.php**

<?php
// Check if ID is set
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID not set']);
    exit;
}

// Get ID from URL
$id = $_GET['id'];

// Check if ID exists in database
// Replace with your database query
if (!isset($id)) {
    echo json_encode(['error' => 'Record not found']);
    exit;
}

// Get existing record details
// Replace with your database query
$data = [
    'id' => $id,
    'name' => 'Existing Name',
    'description' => 'Existing Description'
];

// Send response
echo json_encode($data);


**header.php**

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <ul class="flex items-center space-x-4">
                <li><a href="list_<?= $mod_slug ?>.php" class="text-lg font-bold">List <?= $mod_slug ?></a></li>
                <li><a href="create_<?= $mod_slug ?>.php" class="text-lg font-bold">Create <?= $mod_slug ?></a></li>
            </ul>
        </div>
    </nav>
    <main class="container mx-auto p-4 pt-6">
        <?= $content ?>
    </main>
</body>
</html>


**breadcrumb.php**

<nav class="bg-gray-200 text-gray-600 p-4">
    <div class="container mx-auto flex justify-between">
        <h1 class="text-lg font-bold"><?= $breadcrumb ?></h1>
        <ul class="flex items-center space-x-4">
            <li><a href="index.php" class="text-lg font-bold">Home</a></li>
            <li><a href="list_<?= $mod_slug ?>.php" class="text-lg font-bold">List <?= $mod_slug ?></a></li>
            <li><a href="create_<?= $mod_slug ?>.php" class="text-lg font-bold">Create <?= $mod_slug ?></a></li>
        </ul>
    </div>
</nav>