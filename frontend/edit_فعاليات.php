**edit_فعاليات.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$url = '../backend/فعاليات.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if data exists
if (!isset($data['id'])) {
    echo 'Error: Record not found.';
    exit;
}

// Set page title and mod slug
$page_title = 'تعديل فعلية';
$mod_slug = 'فعاليات';

// Include header and footer
include 'header.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 lg:p-8 xl:p-8">
        <h2 class="text-lg font-bold mb-4"><?= $page_title ?></h2>
        <form id="edit-form" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">الاسم</label>
                <input type="text" id="name" name="name" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" value="<?= $data['name'] ?>">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea id="description" name="description" class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"><?= $data['description'] ?></textarea>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">تعديل</button>
        </form>
    </div>
</div>

<script>
    // Fetch existing record details via GET
    const url = '../backend/فعاليات.php?id=<?= $id ?>';
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('edit-form');
            form.name.value = data.name;
            form.description.value = data.description;
        })
        .catch(error => console.error(error));

    // Handle form submission
    const form = document.getElementById('edit-form');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch('../backend/فعاليات.php', {
            method: 'PUT',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'list_<?= $mod_slug ?>.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**backend/فعاليات.php**

<?php
// Check if ID exists
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID not found.']);
    exit;
}

// Get ID
$id = $_GET['id'];

// Fetch existing record details from database
// Replace with your actual database query
$data = [
    'id' => $id,
    'name' => 'Existing Name',
    'description' => 'Existing Description'
];

// Check if data exists
if (!isset($data['id'])) {
    echo json_encode(['error' => 'Record not found.']);
    exit;
}

// Output data as JSON
echo json_encode($data);
?>


Note: Replace the `backend/فعاليات.php` code with your actual database query to fetch the existing record details.