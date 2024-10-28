<?php
// Include database connection
include 'dbh.inc.php';

try {
    // Query to fetch all tasks
    $sql = "SELECT task_id, title, description, due_date, important FROM tasks ORDER BY due_date ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send tasks as JSON
    echo json_encode(['status' => 'success', 'tasks' => $tasks]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
