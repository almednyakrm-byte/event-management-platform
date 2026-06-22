**list_payments.php**

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
    <title>Payments</title>
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
    <div class="max-w-7xl mx-auto p-4">
        <nav class="flex justify-between items-center py-4">
            <a href="index.php" class="text-lg font-bold">Home</a>
            <div class="flex items-center">
                <p class="mr-4 text-lg font-bold"><?= $_SESSION['username'] ?></p>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">Logout</button>
            </div>
        </nav>
        <div class="flex justify-between items-center py-4">
            <h1 class="text-lg font-bold">Payments</h1>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_payments.php'">Add New Item</button>
        </div>
        <div class="flex justify-between items-center py-4">
            <input type="search" id="search" class="w-full p-2 pl-10 text-sm text-gray-700 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Search...">
            <button class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded" onclick="searchPayments()">Search</button>
        </div>
        <table class="w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="payments-list">
                <?php
                // Fetch payments list from backend
                $payments = json_decode(file_get_contents('../backend/payments.php'), true);
                foreach ($payments as $payment) {
                    ?>
                    <tr>
                        <td class="px-4 py-2"><?= $payment['id'] ?></td>
                        <td class="px-4 py-2"><?= $payment['name'] ?></td>
                        <td class="px-4 py-2"><?= $payment['amount'] ?></td>
                        <td class="px-4 py-2">
                            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='edit_payments.php?id=<?= $payment['id'] ?>'">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deletePayment(<?= $payment['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchPayments() {
            const searchInput = document.getElementById('search');
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetch('../backend/payments.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        const paymentsList = document.getElementById('payments-list');
                        paymentsList.innerHTML = '';
                        data.forEach(payment => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-4 py-2">${payment.id}</td>
                                <td class="px-4 py-2">${payment.name}</td>
                                <td class="px-4 py-2">${payment.amount}</td>
                                <td class="px-4 py-2">
                                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='edit_payments.php?id=${payment.id}'">Edit</button>
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deletePayment(${payment.id})">Delete</button>
                                </td>
                            `;
                            paymentsList.appendChild(row);
                        });
                    });
            } else {
                fetch('../backend/payments.php')
                    .then(response => response.json())
                    .then(data => {
                        const paymentsList = document.getElementById('payments-list');
                        paymentsList.innerHTML = '';
                        data.forEach(payment => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-4 py-2">${payment.id}</td>
                                <td class="px-4 py-2">${payment.name}</td>
                                <td class="px-4 py-2">${payment.amount}</td>
                                <td class="px-4 py-2">
                                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='edit_payments.php?id=${payment.id}'">Edit</button>
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deletePayment(${payment.id})">Delete</button>
                                </td>
                            `;
                            paymentsList.appendChild(row);
                        });
                    });
            }
        }

        function deletePayment(id) {
            if (confirm('Are you sure you want to delete this payment?')) {
                fetch('../backend/payments.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error deleting payment!');
                    }
                })
                .catch(error => console.error(error));
            }
        }
    </script>
</body>
</html>

This code includes a basic layout with a navigation bar, a search bar, and a table to display the list of payments. The search bar filters the list of payments in real-time, and the delete button sends a DELETE request to the backend to delete the payment. The edit button links to the edit_payments.php page.