<?php
require 'dbh.inc.php'; // Include database connection

header('Content-Type: application/json');

// Determine the source of tasks (database or XML)
$async = isset($_GET['async']) ? $_GET['async'] : 'db'; // Default to 'db' for database, or use 'xml' for XML file

// Get the search term and filter from the query parameters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($async === 'xml') {
    // Fetch tasks from XML file
    $tasksXML = simplexml_load_file('tasks.xml');
    $tasks = [];

    foreach ($tasksXML->task as $task) {
        // Filter completed/uncompleted based on status
        if (($filter === 'completed' && (string)$task->status !== '1') ||
            ($filter === 'uncompleted' && (string)$task->status !== '0')
        ) {
            continue; // Skip tasks that don't match the filter
        }

        // Search filter
        if ($searchTerm && (stripos($task->title, $searchTerm) === false && stripos($task->description, $searchTerm) === false)) {
            continue; // Skip tasks that don't match the search term
        }

        $tasks[] = [
            'task_id' => (string)$task->task_id,
            'title' => (string)$task->title,
            'due_date' => (string)$task->due_date,
            'description' => (string)$task->description,
            'status' => (string)$task->status,
        ];
    }

    echo json_encode(['status' => 'success', 'tasks' => $tasks]);
} else {
    // Fetch tasks from the database
    $sql = "SELECT task_id, title, due_date, description, status FROM tasks WHERE 1=1";

    // Add filtering based on completed/uncompleted tasks
    if ($filter === 'completed') {
        $sql .= " AND status = 1";
    } elseif ($filter === 'uncompleted') {
        $sql .= " AND status = 0";
    }

    // Add search condition if provided
    if ($searchTerm) {
        $sql .= " AND (title LIKE :searchTerm OR description LIKE :searchTerm)";
    }

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare($sql);

    // Bind the search term if it exists
    if ($searchTerm) {
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
    }

    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the tasks as a JSON response
    echo json_encode(['status' => 'success', 'tasks' => $tasks]);
}
