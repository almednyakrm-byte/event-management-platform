**edit_schedules.php**

<?php
// Session validation
if (!isset($_SESSION['mod_slug']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get schedule ID from URL
$schedule_id = $_GET['id'];

// Fetch existing schedule details via AJAX
$js = '
<script>
    $(document).ready(function() {
        $.get("../backend/schedules.php?id=' . $schedule_id . '", function(data) {
            $("#schedule_name").val(data.schedule_name);
            $("#schedule_description").val(data.schedule_description);
            $("#schedule_start_date").val(data.schedule_start_date);
            $("#schedule_end_date").val(data.schedule_schedule_end_date);
        });
    });
</script>
';

// Form submission handler
$js .= '
<script>
    $(document).ready(function() {
        $("#edit-schedule-form").submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: "PUT",
                url: "../backend/schedules.php",
                data: formData,
                success: function(data) {
                    window.location.href = "list_' . $_SESSION['mod_slug'] . '.php";
                },
                error: function(xhr, status, error) {
                    alert("Error updating schedule: " + error);
                }
            });
        });
    });
</script>
';

// Display form
?>

<div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-bold text-emerald-600 mb-4">Edit Schedule</h2>
    <form id="edit-schedule-form" method="post">
        <div class="mb-4">
            <label for="schedule_name" class="block text-sm font-bold text-gray-700">Schedule Name:</label>
            <input type="text" id="schedule_name" name="schedule_name" class="w-full p-2 text-sm text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500">
        </div>
        <div class="mb-4">
            <label for="schedule_description" class="block text-sm font-bold text-gray-700">Schedule Description:</label>
            <textarea id="schedule_description" name="schedule_description" class="w-full p-2 text-sm text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500"></textarea>
        </div>
        <div class="mb-4">
            <label for="schedule_start_date" class="block text-sm font-bold text-gray-700">Schedule Start Date:</label>
            <input type="date" id="schedule_start_date" name="schedule_start_date" class="w-full p-2 text-sm text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500">
        </div>
        <div class="mb-4">
            <label for="schedule_end_date" class="block text-sm font-bold text-gray-700">Schedule End Date:</label>
            <input type="date" id="schedule_end_date" name="schedule_end_date" class="w-full p-2 text-sm text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">Update Schedule</button>
    </form>
</div>

<?php echo $js; ?>


**Note:** This code assumes that you have jQuery and a backend PHP script (`schedules.php`) that handles the AJAX requests. The `schedules.php` script should be responsible for updating the schedule record in the database and returning the updated schedule details in JSON format.