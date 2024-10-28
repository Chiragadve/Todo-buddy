<?php
// Include database connection
include 'dbh.inc.php'; // Adjust this line as needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the task ID and the completed status from the request
    $task_id = intval($_POST['task_id']);
    $completed = intval($_POST['completed']); // 1 for checked, 0 for unchecked

    // Prepare the SQL update statement
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
    $stmt->bind_param("ii", $completed, $task_id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
