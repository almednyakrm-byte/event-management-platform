**create_تذاكر.php**

<?php
// Session validation
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Include header
require_once 'header.php';

// Include Tailwind CSS
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">إضافة تذكرة جديدة</h1>

    <form id="create_ticket_form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">عنوان التذكرة</label>
            <input type="text" id="title" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="عنوان التذكرة">
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">وصف التذكرة</label>
            <textarea id="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="وصف التذكرة"></textarea>
        </div>

        <div class="mb-4">
            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">حالة التذكرة</label>
            <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="مفتوح">مفتوح</option>
                <option value="مغلق">مغلق</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">إضافة تذكرة</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#create_ticket_form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: '../backend/تذاكر.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data == 'success') {
                        window.location.href = 'list_تذاكر.php';
                    } else {
                        alert('Error: ' + data);
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
require_once 'footer.php';
?>

**تذاكر.php (backend file)**

<?php
// Include database connection
require_once 'db.php';

// Check if form data is submitted
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['status'])) {
    // Insert data into database
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $sql = "INSERT INTO تذاكر (title, description, status) VALUES ('$title', '$description', '$status')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo 'success';
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}
?>