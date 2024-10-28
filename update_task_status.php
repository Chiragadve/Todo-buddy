<?php
require 'dbh.inc.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];
    $status = $_POST['status'];

    // Prepare and execute the SQL statement to update the status
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
    if ($stmt->execute([$status, $taskId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
