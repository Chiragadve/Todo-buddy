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
$(document).on("change", 'input[type="checkbox"]', function () {
  const taskItem = $(this).closest(".task-item");
  const taskId = taskItem.data("id"); // Get the task ID

  if ($(this).is(":checked")) {
    // Mark the task as completed
    taskItem.addClass("completed"); // Add completed class
    taskItem
      .find("p")
      .css({ "text-decoration": "line-through", color: "#888" }); // Apply strikethrough and fade color
    $("#taskList").append(taskItem); // Move task to bottom
  } else {
    // Mark the task as not completed
    taskItem.removeClass("completed"); // Remove completed class
    taskItem.find("p").css({ "text-decoration": "none", color: "" }); // Remove strikethrough and reset color
    $("#taskList").prepend(taskItem); // Move back to the top
  }

  // Prevent any unwanted output
  return false; // Prevent any default behavior that may cause the object to be outputted
});
