document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("volunteers_assignements.html")) {
    loadAssignments();
    const assignmentForm = document.getElementById("assignmentForm");
    if (assignmentForm) {
      assignmentForm.addEventListener("submit", handleAssignmentFormSubmit);
    } else {
      console.error("Assignment form not found");
    }
  }
});

function loadAssignments() {
  fetch("../api/volunteer_assignements.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Loaded assignments:", data); // Check what is returned
      const tableBody = document.querySelector("#assignmentsTableBody");
      tableBody.innerHTML = "";
      data.forEach((assignment) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                  <td>${assignment.assignment_id}</td>
                  <td>${assignment.volunteer_id}</td>
                  <td>${assignment.service_id}</td>
                  <td>${assignment.task}</td>
                  <td>${assignment.date}</td>
                  <td>${assignment.status}</td>
                  <td>
                    <button onclick="editAssignment(${assignment.assignment_id})">Edit</button>
                    <button onclick="deleteAssignment(${assignment.assignment_id})">Delete</button>
                  </td>
                `;
        tableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading assignments:", error));
}

function showAssignmentCreateForm() {
  document.getElementById("formTitle").innerText = "Create Assignment";
  document.getElementById("assignmentForm").reset();
  document.getElementById("assignmentId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("assignmentFormContainer").style.display = "block";
}

function handleAssignmentFormSubmit(event) {
  event.preventDefault();

  const formData = {
    assignment_id: document.getElementById("assignmentId").value,
    volunteer_id: document.getElementById("volunteer_id").value,
    service_id: document.getElementById("service_id").value,
    task: document.getElementById("task").value,
    date: document.getElementById("date").value,
    status: document.getElementById("status").value,
  };

  const method = formData.assignment_id ? "PUT" : "POST";
  const url =
    "../api/volunteer_assignements.php" +
    (formData.assignment_id ? `?id=${formData.assignment_id}` : "");

  fetch(url, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((data) => {
      loadAssignments();
      document.getElementById("assignmentFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function editAssignment(id) {
  fetch(`../api/volunteer_assignements.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Assignment";
      document.getElementById("assignmentId").value = data.assignment_id;
      document.getElementById("volunteer_id").value = data.volunteer_id;
      document.getElementById("service_id").value = data.service_id;
      document.getElementById("task").value = data.task;
      document.getElementById("date").value = data.date;
      document.getElementById("status").value = data.status;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("assignmentFormContainer").style.display =
        "block";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteAssignment(id) {
  if (confirm("Are you sure you want to delete this assignment?")) {
    fetch(`../api/volunteer_assignements.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        loadAssignments();
      })
      .catch((error) => console.error("Error:", error));
  }
}
