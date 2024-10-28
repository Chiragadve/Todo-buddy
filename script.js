const addTaskBtn = document.getElementById("addTaskBtn");
const taskPopup = document.getElementById("taskPopup");
const closeBtn = document.querySelector(".close-btn");

// Open the popup
addTaskBtn.addEventListener("click", () => {
  taskPopup.style.display = "block";
});

// Close the popup
closeBtn.addEventListener("click", () => {
  taskPopup.style.display = "none";
});

// Close the popup when clicking outside the content
window.addEventListener("click", (event) => {
  if (event.target === taskPopup) {
    taskPopup.style.display = "none";
  }
});

// Display tasks on the page
function displayTasks(tasks) {
  const taskList = $("#taskList");
  taskList.empty(); // Clear existing tasks
  tasks.forEach((task) => {
    const importantClass = task.important ? "important" : "";
    const completedClass = task.status === "completed" ? "completed" : ""; // Check if task is completed
    taskList.append(`
      <div class="task-item ${completedClass}">
        <div class="task-item-left">
          <input type="checkbox" class="task-checkbox" data-id="${
            task.task_id
          }" ${task.status === "completed" ? "checked" : ""}/>
          <p>${task.title} (${task.due_date}) - ${task.description}</p>
        </div>
        <div class="task-item-right">
          <button class="edit ${importantClass}">Imp</button>
          <span><button class="delete" data-id="${
            task.task_id
          }">Delete</button></span>
        </div>
      </div>
    `);
  });
  $("#taskCount").text(tasks.length);
}

// Handle checkbox change
// Handle checkbox change
$(document).on("change", 'input[type="checkbox"]', function () {
  const taskItem = $(this).closest(".task-item");
  const taskId = $(this).data("id"); // Get the task ID
  const isCompleted = $(this).is(":checked") ? 1 : 0;

  // Send AJAX request to update status
  $.ajax({
    url: "update_task_status.php",
    type: "POST",
    data: {
      task_id: taskId,
      completed: isCompleted,
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        if (response.completed) {
          // Task is marked as completed
          taskItem.addClass("completed");
          taskItem
            .find("p")
            .css({ "text-decoration": "line-through", color: "#888" });
        } else {
          // Task is marked as not completed
          taskItem.removeClass("completed");
          taskItem.find("p").css({ "text-decoration": "none", color: "" });
        }
        // Optionally, you can append/prepend to the task list here if needed
        // For example, if you want to keep the order of tasks unchanged, do not modify task list here
      }
    },
    error: function () {
      console.error("Error updating task status");
    },
  });
});
