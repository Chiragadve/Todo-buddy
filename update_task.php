<?php
require 'dbh.inc.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = $_POST['task_id'];
    $title = $_POST['title'];
    $dueDate = $_POST['due_date'];
    $description = $_POST['description'];

    // Prepare the SQL statement to update the database
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, due_date = ?, description = ? WHERE task_id = ?");
    if ($stmt->execute([$title, $dueDate, $description, $taskId])) {
        // Update XML
        $xml = simplexml_load_file('tasks.xml');
        foreach ($xml->task as $task) {
            if ((string)$task->task_id === $taskId) {
                $task->title = $title;
                $task->due_date = $dueDate;
                $task->description = $description;
                break;
            }
        }
        // Save changes to XML
        $xml->asXML('tasks.xml');

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
