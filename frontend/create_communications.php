**create_communications.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include the header and navigation
require_once 'header.php';
require_once 'nav.php';

// Define the form fields
$formFields = [
    'title' => [
        'label' => 'Title',
        'type' => 'text',
        'placeholder' => 'Enter title',
        'required' => true
    ],
    'description' => [
        'label' => 'Description',
        'type' => 'textarea',
        'placeholder' => 'Enter description',
        'required' => true
    ],
    'type' => [
        'label' => 'Type',
        'type' => 'select',
        'options' => [
            'Email' => 'Email',
            'Phone' => 'Phone',
            'Meeting' => 'Meeting'
        ],
        'required' => true
    ],
    'status' => [
        'label' => 'Status',
        'type' => 'select',
        'options' => [
            'Open' => 'Open',
            'Closed' => 'Closed'
        ],
        'required' => true
    ]
];

// Display the form
?>
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">Create New Communication</h1>
    <form id="create-communication-form">
        <?php foreach ($formFields as $field => $fieldData): ?>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="<?= $field ?>">
                    <?= $fieldData['label'] ?>
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="<?= $field ?>"
                    type="<?= $fieldData['type'] ?>"
                    placeholder="<?= $fieldData['placeholder'] ?>"
                    required
                >
                <?php if ($fieldData['type'] == 'select'): ?>
                    <select id="<?= $field ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php foreach ($fieldData['options'] as $option => $label): ?>
                            <option value="<?= $option ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($fieldData['type'] == 'textarea'): ?>
                    <textarea id="<?= $field ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Create</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#create-communication-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/communications.php',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'list_communications.php';
                    } else {
                        alert('Error creating communication');
                    }
                }
            });
        });
    });
</script>

<?php
// Include the footer
require_once 'footer.php';
?>


**communications.php (backend)**

<?php
// Include the database connection
require_once 'db.php';

// Check if the form has been submitted
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['type']) && isset($_POST['status'])) {
    // Prepare the query
    $query = "INSERT INTO communications (title, description, type, status) VALUES (:title, :description, :type, :status)";
    
    // Prepare the parameters
    $params = [
        ':title' => $_POST['title'],
        ':description' => $_POST['description'],
        ':type' => $_POST['type'],
        ':status' => $_POST['status']
    ];
    
    // Execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    // Check if the query was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>


**Note:** This code assumes that you have a `db.php` file that connects to your database and a `footer.php` file that includes the footer HTML. You will need to modify the code to fit your specific needs. Additionally, this code does not include any error handling or security measures that you should include in a production environment.