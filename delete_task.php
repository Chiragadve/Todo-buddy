<?php
include 'dbh.inc.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];

    try {
        // Delete from database
        $sql = "DELETE FROM tasks WHERE task_id = :task_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Remove from XML
            $xml = simplexml_load_file('tasks.xml');
            foreach ($xml->task as $index => $task) {
                if ((string)$task->task_id === $task_id) {
                    unset($xml->task[$index]);
                    break;
                }
            }

            // Save changes to XML
            $xml->asXML('tasks.xml');

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete task.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
