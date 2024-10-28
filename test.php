<?php
include 'dbh.inc.php'; // Include the database connection

// Handle checkbox status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];
    $completed = 1; // Set status to 1

    // Prepare and execute SQL query to update the task status
    $sql = "UPDATE tasks SET status = ? WHERE task_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $completed, $taskId);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'SQL error: ' . $conn->error]);
    }

    $conn->close();
    exit; // Exit after processing the request
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Update</title>
</head>

<body>

    <h2>Task List</h2>
    <div id="taskList">
        <!-- Example task; in a real application, you'd fetch this from your database -->
        <div class="task-item" data-id="1">
            <input type="checkbox" class="task-checkbox" data-id="1"> Task 1
        </div>
        <div class="task-item" data-id="2">
            <input type="checkbox" class="task-checkbox" data-id="2"> Task 2
        </div>
    </div>

    <!-- jQuery (for AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle checkbox change event
            $(document).on('change', '.task-checkbox', function() {
                const taskId = $(this).data('id'); // Get the task ID

                // AJAX request to update the task status
                $.ajax({
                    url: '', // Use the current URL for POST request
                    type: 'POST',
                    data: {
                        task_id: taskId,
                        completed: 1 // Set status to 1 when checkbox is checked
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Task status updated successfully.');
                        } else {
                            alert('Failed to update task status: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while updating task status.');
                    }
                });
            });
        });
    </script>

</body>

</html>