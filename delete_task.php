<?php
// Include database connection
include 'dbh.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];

    try {
        // Query to delete the task
        $sql = "DELETE FROM tasks WHERE task_id = :task_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete task.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
