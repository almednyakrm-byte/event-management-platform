**list_فعاليات.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فعاليات</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md p-4">
        <nav class="flex justify-between items-center">
            <a href="index.php" class="text-lg font-bold">الرئيسية</a>
            <div class="flex items-center">
                <span class="text-lg font-bold"><?= $_SESSION['username']; ?></span>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-4" onclick="document.location='logout.php'">تسجيل الخروج</button>
            </div>
        </nav>
    </header>
    <main class="max-w-7xl mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4">فعاليات</h1>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="document.location='create_فعاليات.php'">إضافة جديد</button>
        <div class="flex justify-between items-center mb-4">
            <input type="search" class="w-full p-2 pl-10 text-sm text-gray-700 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="بحث" id="search">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="searchRecords()">بحث</button>
        </div>
        <table class="w-full border-collapse border border-gray-400">
            <thead>
                <tr>
                    <th class="border border-gray-400 p-2">اسم الفعالية</th>
                    <th class="border border-gray-400 p-2">تاريخ الفعالية</th>
                    <th class="border border-gray-400 p-2">حالة الفعالية</th>
                    <th class="border border-gray-400 p-2">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="records">
                <!-- Records will be fetched from AJAX call -->
            </tbody>
        </table>
    </main>
    <script>
        // Search records
        function searchRecords() {
            const search = document.getElementById('search').value;
            fetch('../backend/فعاليات.php', {
                method: 'GET',
                params: { search }
            })
            .then(response => response.json())
            .then(data => {
                const records = document.getElementById('records');
                records.innerHTML = '';
                data.forEach(record => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border border-gray-400 p-2">${record.name}</td>
                        <td class="border border-gray-400 p-2">${record.date}</td>
                        <td class="border border-gray-400 p-2">${record.status}</td>
                        <td class="border border-gray-400 p-2">
                            <a href="edit_فعاليات.php?id=${record.id}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">تعديل</a>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteRecord(${record.id})">حذف</button>
                        </td>
                    `;
                    records.appendChild(row);
                });
            });
        }

        // Delete record
        function deleteRecord(id) {
            if (confirm('هل تريد حذف الفعالية؟')) {
                fetch('../backend/فعاليات.php', {
                    method: 'DELETE',
                    params: { id }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        searchRecords();
                    } else {
                        alert('حدث خطأ أثناء الحذف');
                    }
                });
            }
        }

        // Fetch records on page load
        fetch('../backend/فعاليات.php')
        .then(response => response.json())
        .then(data => {
            const records = document.getElementById('records');
            data.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border border-gray-400 p-2">${record.name}</td>
                    <td class="border border-gray-400 p-2">${record.date}</td>
                    <td class="border border-gray-400 p-2">${record.status}</td>
                    <td class="border border-gray-400 p-2">
                        <a href="edit_فعاليات.php?id=${record.id}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">تعديل</a>
                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteRecord(${record.id})">حذف</button>
                    </td>
                `;
                records.appendChild(row);
            });
        });
    </script>
</body>
</html>

**backend/فعاليات.php**

<?php
// Database connection
$conn = mysqli_connect('localhost', 'username', 'password', 'database');

// Search records
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM activities WHERE name LIKE '%$search%'";
} else {
    $query = "SELECT * FROM activities";
}

// Fetch records
$result = mysqli_query($conn, $query);
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Delete record
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM activities WHERE id = '$id'";
    mysqli_query($conn, $query);
    echo json_encode(array('success' => true));
}

// Output records
echo json_encode($data);
?>