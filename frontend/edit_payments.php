**edit_payments.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get payment ID from URL
$id = $_GET['id'];

// Fetch payment details via AJAX
$payment = json_decode(file_get_contents('../backend/payments.php?id=' . $id), true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 bg-white rounded-md shadow-md">
        <h1 class="text-lg font-bold text-emerald-600 mb-4">Edit Payment</h1>
        <form id="edit-payment-form">
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount:</label>
                <input type="number" id="amount" name="amount" class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-md" value="<?= $payment['amount'] ?>">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea id="description" name="description" class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-md" rows="4"><?= $payment['description'] ?></textarea>
            </div>
            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">Update Payment</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Populate form fields
            $.ajax({
                type: 'GET',
                url: '../backend/payments.php?id=' + <?= $id ?>,
                success: function(data) {
                    var payment = JSON.parse(data);
                    $('#amount').val(payment.amount);
                    $('#description').val(payment.description);
                }
            });

            // Update payment on form submit
            $('#edit-payment-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/payments.php',
                    data: formData,
                    success: function() {
                        window.location.href = 'list_payments.php';
                    }
                });
            });
        });
    </script>
</body>
</html>


**backend/payments.php**

<?php
// Get payment ID from URL
$id = $_GET['id'];

// Fetch payment details from database
// Replace with your actual database query
$payment = array(
    'id' => $id,
    'amount' => 100.00,
    'description' => 'Payment for service'
);

echo json_encode($payment);
?>