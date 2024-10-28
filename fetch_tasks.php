<?php
require 'dbh.inc.php'; // Include database connection

// Get the search term and filter from the query parameters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Start building the SQL query
$sql = "SELECT task_id, title, due_date, description, status FROM tasks WHERE 1=1"; // 1=1 is a placeholder for dynamic query building

// Add filtering based on completed/uncompleted tasks
if ($filter === 'completed') {
    $sql .= " AND status = 1"; // Only completed tasks
} elseif ($filter === 'uncompleted') {
    $sql .= " AND status = 0"; // Only uncompleted tasks
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
