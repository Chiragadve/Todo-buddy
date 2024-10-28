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
                displayMessage('Error fetching tasks.');
              }
            },
            error: function() {
              displayMessage('An error occurred while fetching tasks.');
            },
          });
        }

        // Display tasks on the page
        function displayTasks(tasks) {
          const taskList = $('#taskList');
          taskList.empty();

          tasks.forEach((task) => {
            const isCompleted = task.status == 1; // Check if task is completed
            const checked = isCompleted ? 'checked' : '';
            const lineThrough = isCompleted ? 'text-decoration: line-through; color: #888;' : '';
            const opacityStyle = isCompleted ? 'opacity: 0.5;' : '';

            const taskItem = `
              <div class="task-item" data-id="${task.task_id}">
                <div class="task-item-left">
                  <input type="checkbox" ${checked} />
                  <p style="${lineThrough} ${opacityStyle}">
                    ${task.title} (${task.due_date}) - ${task.description || 'No description'}
                  </p>
                </div>
                <div class="task-item-right">
                  <button class="delete red" data-id="${task.task_id}">Delete</button>
                </div>
              </div>
            `;
            taskList.append(taskItem);
          });

          $('#taskCount').text(tasks.length);
        }

        // Handle task deletion
        $(document).on('click', '.delete', function() {
          const taskId = $(this).data('id');

          $.ajax({
            url: 'delete_task.php',
            type: 'POST',
            data: {
              task_id: taskId
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                fetchTasks();
              } else {
                displayMessage('Failed to delete task.');
              }
            },
            error: function() {
              displayMessage('An error occurred while deleting the task.');
            },
          });
        });

        // Handle checkbox change to update task completion status
        $(document).on('change', 'input[type="checkbox"]', function() {
          const taskItem = $(this).closest('.task-item');
          const taskId = taskItem.data('id');
          const isChecked = $(this).is(':checked') ? 1 : 0;

          $.ajax({
            url: 'update_task_status.php',
            type: 'POST',
            data: {
              task_id: taskId,
              status: isChecked // Use status for updating completion
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                // Update the UI based on the checked status
                updateTaskUI(taskItem, isChecked);
              } else {
                displayMessage('Failed to update task status.');
              }
            },
            error: function() {
              displayMessage('An error occurred while updating task status.');
            },
          });
        });

        // Function to update task item UI
        function updateTaskUI(taskItem, isChecked) {
          if (isChecked) {
            taskItem.find('p').css({
              'text-decoration': 'line-through',
              color: '#888',
              opacity: '0.5',
            });
          } else {
            taskItem.find('p').css({
              'text-decoration': 'none',
              color: '',
              opacity: '1',
            });
          }
        }

        // Open the "Add Task" popup
        $('#addTaskBtn').on('click', function() {
          $('#taskPopup').fadeIn();
        });

        // Close the popup when clicking the close button
        $(document).on('click', '.close-btn', function() {
          $('#taskPopup').fadeOut();
        });

        // Handle form submission to add a new task
        $('#taskForm').on('submit', function(e) {
          e.preventDefault();

          $.ajax({
            url: 'add_task.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                fetchTasks();
                $('#taskForm')[0].reset();
                $('#taskPopup').fadeOut();
              } else {
                displayMessage('Failed to add task.');
              }
            },
            error: function() {
              displayMessage('An error occurred while adding the task.');
            },
          });
        });

        // Display messages in a dedicated area
        function displayMessage(message) {
          $('#message').html(`<p>${message}</p>`).show();
        }
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
          <input type="text" id="title" name="title" required />
        </div>

        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="due_date" required />
        </div>

        <div class="form-group">
          <label for="description">Description (optional)</label>
          <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <button type="submit" class="submit-btn">Add Task</button>
      </form>
      <div id="message" style="margin-top: 10px;"></div>
    </div>
  </div>

</body>

</html>