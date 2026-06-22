**edit_services.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get service ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$url = '../backend/services.php?id=' . $id;
$response = file_get_contents($url);
$data = json_decode($response, true);

// Check if data exists
if ($data) {
    $service_name = $data['service_name'];
    $service_description = $data['service_description'];
    $service_price = $data['service_price'];
} else {
    echo 'Error fetching service data';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-emerald-600">Edit Service</h1>
        <form id="edit-service-form" class="bg-white p-4 rounded shadow-md">
            <div class="mb-4">
                <label for="service_name" class="block text-gray-700 text-sm font-bold mb-2">Service Name:</label>
                <input type="text" id="service_name" name="service_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $service_name ?>">
            </div>
            <div class="mb-4">
                <label for="service_description" class="block text-gray-700 text-sm font-bold mb-2">Service Description:</label>
                <textarea id="service_description" name="service_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= $service_description ?></textarea>
            </div>
            <div class="mb-4">
                <label for="service_price" class="block text-gray-700 text-sm font-bold mb-2">Service Price:</label>
                <input type="number" id="service_price" name="service_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= $service_price ?>">
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Update Service</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-service-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/services.php',
                    data: formData,
                    success: function(response) {
                        if (response == 'success') {
                            window.location.href = 'list_services.php';
                        } else {
                            alert('Error updating service');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>


**services.php (backend)**

<?php
// Check if service ID exists in URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch service data from database
    $sql = "SELECT * FROM services WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    $service = mysqli_fetch_assoc($result);
    echo json_encode($service);
} else {
    echo 'Error fetching service data';
    exit;
}
?>


**Note:** Make sure to replace `../backend/services.php` with the actual path to your backend services.php file. Also, ensure that the database connection is established in the backend services.php file.