<?php
require 'dbh.inc.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = $_POST['task_id'];
    $title = $_POST['title'];
    $dueDate = $_POST['due_date'];
    $description = $_POST['description'];

    // Prepare the SQL statement
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, due_date = ?, description = ? WHERE task_id = ?");
    if ($stmt->execute([$title, $dueDate, $description, $taskId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
