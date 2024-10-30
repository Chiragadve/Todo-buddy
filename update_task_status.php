<?php
require 'dbh.inc.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = $_POST['task_id'];
    $status = $_POST['status'];

    // Update status in the database
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
    if ($stmt->execute([$status, $taskId])) {
        // Load the XML file
        $xml = simplexml_load_file('tasks.xml');

        // Find the task with the matching ID and update its status
        foreach ($xml->task as $task) {
            if ((int)$task->id == $taskId) {
                $task->status = $status;
                break;
            }
        }

        // Save the updated XML back to the file
        $xml->asXML('tasks.xml');

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
