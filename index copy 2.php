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
      <p id="allTasksBtn" class="task-filter" data-filter="all">All Tasks</p>
      <p id="completedTasksBtn" class="task-filter" data-filter="completed">Completed Tasks</p>
      <p id="uncompletedTasksBtn" class="task-filter" data-filter="uncompleted">Uncompleted Tasks</p>
    </div>

    <!-- Main Section -->
    <div class="main-section section">
      <div class="search-container">
        <input type="text" id="search" placeholder="Search on Task" />
        <button id="searchBtn">Search</button>
      </div>

      <h3>All Tasks (<span id="taskCount">0</span> Tasks)</h3>

      <!-- Tasks List -->
      <div class="task-list" id="taskList"></div>
    </div>

    <!-- Right Section -->
    <div class="right-section column section">
      <p>Sidebar</p>
      <p>All Tasks Progress</p>
      <progress id="file" value="0" max="100">0%</progress>
      <p>0%</p>
    </div>
  </div>

  <!-- jQuery (for AJAX) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
    $(document).ready(function() {
      // Fetch tasks on page load
      fetchTasks();

      // Function to fetch and display tasks
      function fetchTasks(searchTerm = '', filter = 'all') {
        $.ajax({
          url: 'fetch_tasks.php',
          type: 'GET',
          dataType: 'json',
          data: {
            search: searchTerm,
            filter: filter // Pass the filter
          },
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

        let completedCount = 0;

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
                <button class="edit" data-id="${task.task_id}" data-title="${task.title}" data-due_date="${task.due_date}" data-description="${task.description || ''}">Edit</button>
                <button class="delete red" data-id="${task.task_id}">Delete</button>
              </div>
            </div>
          `;
          taskList.append(taskItem);

          if (isCompleted) completedCount++;
        });

        $('#taskCount').text(tasks.length);
        updateProgress(completedCount, tasks.length); // Update progress bar
      }

      // Update progress bar based on completed tasks
      function updateProgress(completed, total) {
        const progress = (completed / total) * 100 || 0; // Avoid division by zero
        $('#file').val(progress);
        $('#file').next('p').text(`${Math.round(progress)}%`); // Update percentage display
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
              fetchTasks(); // Refresh task list
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
              fetchTasks(); // Refresh task list to update progress
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
        $('#taskForm')[0].reset(); // Reset form for new task
        $('#taskPopupTitle').text('Add New Task'); // Set title
        $('#taskId').val(''); // Clear hidden task ID
      });

      // Close the popup when clicking the close button
      $(document).on('click', '.close-btn', function() {
        $('#taskPopup').fadeOut();
      });

      // Handle form submission to add a new task
      $('#taskForm').on('submit', function(e) {
        e.preventDefault();

        const taskId = $('#taskId').val();
        const url = taskId ? 'update_task.php' : 'add_task.php'; // Determine the URL based on task ID

        $.ajax({
          url: url,
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              fetchTasks(); // Refresh task list
              $('#taskForm')[0].reset();
              $('#taskPopup').fadeOut();
            } else {
              displayMessage('Failed to add/update task.');
            }
          },
          error: function() {
            displayMessage('An error occurred while adding/updating the task.');
          },
        });
      });

      // Edit task
      $(document).on('click', '.edit', function() {
        const taskId = $(this).data('id');
        const title = $(this).data('title');
        const dueDate = $(this).data('due_date');
        const description = $(this).data('description');

        $('#taskId').val(taskId); // Set hidden task ID
        $('#title').val(title); // Set title
        $('#date').val(dueDate); // Set due date
        $('#description').val(description); // Set description

        $('#taskPopup').fadeIn(); // Open the popup for editing
        $('#taskPopupTitle').text('Edit Task'); // Set title for popup
      });

      // Search functionality
      $('#searchBtn').on('click', function() {
        const searchTerm = $('#search').val().trim();
        fetchTasks(searchTerm); // Fetch tasks with search term
      });

      // Handle filter clicks
      $('.task-filter').on('click', function() {
        const filter = $(this).data('filter');
        fetchTasks('', filter); // Fetch tasks with the selected filter
      });

      // Display a message to the user
      function displayMessage(message) {
        alert(message); // You can replace this with a more sophisticated UI message display
      }
    });
  </script>

  <!-- Add Task Popup -->
  <div id="taskPopup" class="popup">
    <div class="popup-content">
      <span class="close-btn">&times;</span>
      <h2 id="taskPopupTitle">Add/Edit Task</h2>
      <form id="taskForm">
        <input type="hidden" id="taskId" name="task_id" />
        <label for="title">Task Title:</label>
        <input type="text" id="title" name="title" required />
        <label for="date">Due Date:</label>
        <input type="date" id="date" name="due_date" required />
        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea>
        <button id="" type="submit">Save Task</button>
      </form>
    </div>
  </div>
</body>

</html>