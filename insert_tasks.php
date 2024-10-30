<?php
// Include database connection
include 'dbh.inc.php';

// Retrieve task data from the AJAX request
$title = $_POST['title'];
$description = $_POST['description'] ?? 'No description';
$due_date = $_POST['due_date'];
$status = 0; // Default status for new tasks

try {
    // Insert task into the database
    $stmt = $dbh->prepare("INSERT INTO tasks (title, description, due_date, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $due_date, $status]);

    // Call the function to update tasks.xml
    updateTasksXML();

    echo "Task inserted and XML updated!";
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500); // Indicate server error
}

// Function to regenerate the XML
function updateTasksXML()
{
    include 'dbh.inc.php'; // Database connection

    try {
        // Fetch all tasks from the database
        $stmt = $dbh->prepare("SELECT task_id, title, description, due_date, status FROM tasks");
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create a new XML document
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        // Root element <tasks>
        $root = $xml->createElement('tasks');
        $xml->appendChild($root);

        // Loop through tasks and add to XML
        foreach ($tasks as $task) {
            $taskElement = $xml->createElement('task');

            $taskElement->appendChild($xml->createElement('task_id', $task['task_id']));
            $taskElement->appendChild($xml->createElement('title', htmlspecialchars($task['title'])));
            $taskElement->appendChild($xml->createElement('description', htmlspecialchars($task['description'])));
            $taskElement->appendChild($xml->createElement('due_date', $task['due_date']));
            $taskElement->appendChild($xml->createElement('status', $task['status']));

            $root->appendChild($taskElement);
        }

        // Save XML to file
        $xml->save('tasks.xml');
    } catch (PDOException $e) {
        error_log('Failed to update tasks.xml: ' . $e->getMessage());
    }
}
