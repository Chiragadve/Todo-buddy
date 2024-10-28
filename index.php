<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Todolist</title>
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <div class="container">
    <!-- Left Section -->
    <div class="left-section column section">
      <div class="logo-container">
        <span class="logo">To-do Buddy</span>
      </div>
      <button id="addTaskBtn">Add New Task</button>
      <span class="todaytask">Today's Tasks</span>
      <p>All Tasks</p>
      <p>Important Tasks</p>
      <p>Completed Tasks</p>
      <p>Uncompleted Tasks</p>
    </div>

    <!-- Main Section -->
    <div class="main-section section">
      <div class="search-container">
        <input type="text" id="search" placeholder="Search on Task" />
        <button id="searchBtn">Search</button>
      </div>

      <h3>All Tasks (<span id="taskCount">0</span> Tasks)</h3>
      <p>Sort by:</p>
      <select id="sortOptions">
        <option value="due_date">Due Date</option>
        <option value="priority">Priority</option>
      </select>

      <!-- Tasks List -->
      <div class="task-list" id="taskList"></div>
    </div>

    <!-- jQuery (for AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
      $(document).ready(function() {
        // Fetch tasks on page load
        fetchTasks();

        // Function to fetch and display tasks
        function fetchTasks() {
          $.ajax({
            url: 'fetch_tasks.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                displayTasks(response.tasks);
              } else {
                displayMessage('Error fetching tasks.'); // Show error message
              }
            },
            error: function() {
              displayMessage('An error occurred while fetching tasks.'); // Show error message
            }
          });
        }

        // Display tasks on the page
        function displayTasks(tasks) {
          const taskList = $('#taskList');
          taskList.empty(); // Clear existing tasks

          const completedTasks = []; // Array to hold completed tasks
          tasks.forEach(task => {
            const importantClass = task.important ? 'important' : '';
            const completedClass = task.completed ? 'completed' : '';

            // Ensure we create a task item as a proper HTML string
            const taskItem = `
                <div class="task-item ${completedClass}" data-id="${task.task_id}">
                    <div class="task-item-left">
                        <input type="checkbox" ${task.completed ? 'checked' : ''} />
                        <p>${task.title} (${task.due_date}) - ${task.description || 'No description'}</p>
                    </div>
                    <div class="task-item-right">
                        <button class="edit ${importantClass}">Imp</button>
                        <span><button class="delete" data-id="${task.task_id}">Delete</button></span>
                    </div>
                </div>
            `;

            if (task.completed) {
              completedTasks.push(taskItem); // Add to completed tasks array
            } else {
              taskList.append(taskItem); // Append directly to the task list
            }
          });

          // Append completed tasks at the bottom
          completedTasks.forEach(taskItem => {
            taskList.append(taskItem);
          });

          // Update task count
          $('#taskCount').text(tasks.length);
        }

        // Display messages in a dedicated area
        function displayMessage(message) {
          $('#message').html(`<p>${message}</p>`).show(); // Use a separate message area
        }

        // Handle form submission via AJAX to add a new task
        $('#taskForm').on('submit', function(e) {
          e.preventDefault(); // Prevent page reload

          $.ajax({
            url: 'add_task.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                fetchTasks(); // Refresh task list
                $('#taskForm')[0].reset(); // Clear the form
                $('#taskPopup').fadeOut(); // Close the popup dialog
              } else {
                displayMessage('Failed to add task.'); // Show error message
              }
            },
            error: function() {
              displayMessage('An error occurred while adding the task.'); // Show error message
            }
          });
        });

        // Handle checkbox change to update completion status in the database
        $(document).on('change', 'input[type="checkbox"]', function() {
          const taskItem = $(this).closest('.task-item');
          const taskId = taskItem.data('id'); // Get the task ID
          const isChecked = $(this).is(':checked') ? 1 : 0; // Determine new status (1 for checked, 0 for unchecked)

          // AJAX call to update the task completion status in the database
          $.ajax({
            url: 'update_task_status.php', // Your PHP file to handle the update
            type: 'POST',
            data: {
              task_id: taskId,
              completed: isChecked
            },
            dataType: 'json',
            success: function(response) {
              if (response.status !== 'success') {
                displayMessage('Failed to update task status.'); // Show error message
              }
            },
            error: function() {
              displayMessage('An error occurred while updating task status.'); // Show error message
            }
          });
        });
      });
    </script>

    <!-- Right Section -->
    <div class="right-section column section">
      <p>Sidebar</p>
      <p>All Tasks Progress</p>
      <progress id="file" value="66" max="100">66%</progress>
      <p>66%</p>
    </div>
  </div>

  <!-- Popup Modal -->
  <div id="taskPopup" class="popup">
    <div class="popup-content">
      <span class="close-btn">&times;</span>
      <h2>Add New Task</h2>
      <form id="taskForm">
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" placeholder="e.g., Study for the test" required />
        </div>

        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="due_date" required />
        </div>

        <div class="form-group">
          <label for="description">Description (optional)</label>
          <textarea id="description" name="description" rows="4" placeholder="e.g., Study topics for the exam"></textarea>
        </div>

        <button type="submit" class="submit-btn">Add Task</button>
      </form>
      <div id="message" style="margin-top: 10px;"></div> <!-- Move message display inside the popup -->
    </div>
  </div>

  <script src="script.js"></script>
</body>

</html>