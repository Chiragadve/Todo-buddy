<?php
include 'dbh.inc.php';

header('Content-Type: application/json'); // Set header to return JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $title = $_POST['title'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $important = isset($_POST['important']) ? 1 : 0;

    // Check for required fields
    if (empty($title) || empty($due_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Title and Due Date are required.']);
        exit;
    }

    try {
        $sql = "INSERT INTO tasks (title, description, due_date, status, important, created_at) 
                VALUES (:title, :description, :due_date, 'pending', :important, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':important', $important);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Task not added.']);
        }
    } catch (PDOException $e) {
        // Catch any database-related errors
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
