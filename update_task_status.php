<?php
// Include your database connection file
include 'dbh.inc.php'; // Assumes this file contains your $conn connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $completed = intval($_POST['completed']); // 1 for completed, 0 for not completed

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
    $stmt->bind_param("ii", $completed, $task_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
