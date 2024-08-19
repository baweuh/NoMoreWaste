document.addEventListener("DOMContentLoaded", function () {
  loadVolunteers();
  loadServices();
  loadAssignments();

  const assignmentForm = document.getElementById("assignmentForm");
  if (assignmentForm) {
    assignmentForm.addEventListener("submit", handleAssignmentFormSubmit);
  } else {
    console.error("Assignment form not found");
  }
});

function loadVolunteers() {
  fetch("../../api/volunteers.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Volunteers data:", data); // Déboguer les données reçues
      const volunteerSelect = document.getElementById("volunteer_id");
      volunteerSelect.innerHTML = ""; // Effacer les options existantes
      data.forEach((volunteer) => {
        const option = document.createElement("option");
        option.value = volunteer.volunteer_id; // Assigner la valeur correcte
        option.textContent = `${volunteer.volunteer_id}`; // Afficher ID et nom
        volunteerSelect.appendChild(option);
      });
    })
    .catch((error) => console.error("Error loading volunteers:", error));
}

function loadServices() {
  fetch("../../api/services.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Services data:", data); // Déboguer les données reçues
      const serviceSelect = document.getElementById("service_id");
      serviceSelect.innerHTML = ""; // Effacer les options existantes
      data.forEach((service) => {
        const option = document.createElement("option");
        option.value = service.service_id; // Assigner la valeur correcte
        option.textContent = `${service.service_id}`; // Afficher ID et nom
        serviceSelect.appendChild(option);
      });
    })
    .catch((error) => console.error("Error loading services:", error));
}

function showAssignmentCreateForm() {
  document.getElementById("formTitle").innerText = "Create New Assignment";
  document.getElementById("assignmentId").value = ""; // Reset assignment ID
  document.getElementById("volunteer_id").value = ""; // Reset selected volunteer
  document.getElementById("service_id").value = ""; // Reset selected service
  document.getElementById("task").value = ""; // Reset task
  document.getElementById("date").value = ""; // Reset date
  document.getElementById("status").value = ""; // Reset status
  document.getElementById("formSubmitButton").innerText = "Create"; // Set button text
  document.getElementById("assignmentFormContainer").style.display = "block"; // Show form
}

function handleAssignmentFormSubmit(event) {
  event.preventDefault();

  const assignmentId = document.getElementById("assignmentId").value;
  const volunteerId = document.getElementById("volunteer_id").value;
  const serviceId = document.getElementById("service_id").value;
  const task = document.getElementById("task").value;
  const date = document.getElementById("date").value;
  const status = document.getElementById("status").value;

  const method = assignmentId ? "PUT" : "POST"; // Use PUT for update, POST for create
  const url = assignmentId
    ? `../../api/volunteer_assignements.php?id=${assignmentId}`
    : "../../api/volunteer_assignements.php";

  fetch(url, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      assignment_id: assignmentId,
      volunteer_id: volunteerId,
      service_id: serviceId,
      task: task,
      date: date,
      status: status,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      alert(data.message);
      document.getElementById("assignmentFormContainer").style.display = "none";
      loadAssignments(); // Reload assignments list
    })
    .catch((error) => console.error("Error submitting assignment:", error));
}

function loadAssignments() {
  fetch("../../api/volunteer_assignements.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json(); // Lire la réponse en JSON
    })
    .then((data) => {
      const assignmentsTableBody = document.getElementById(
        "assignmentsTableBody"
      );
      assignmentsTableBody.innerHTML = ""; // Clear existing rows
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
        assignmentsTableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading assignments:", error));
}

function editAssignment(assignmentId) {
  fetch(`../../api/volunteer_assignements.php?id=${assignmentId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Assignment";
      document.getElementById("assignmentId").value = data.assignment_id;
      document.getElementById("volunteer_id").value = data.volunteer_id; // Assigner la valeur correctement
      document.getElementById("service_id").value = data.service_id; // Assigner la valeur correctement
      document.getElementById("task").value = data.task;
      document.getElementById("date").value = data.date;
      document.getElementById("status").value = data.status;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("assignmentFormContainer").style.display =
        "block";
    })
    .catch((error) => console.error("Error loading assignment:", error));
}

function deleteAssignment(assignmentId) {
  if (confirm("Are you sure you want to delete this assignment?")) {
    fetch(`../../api/volunteer_assignements.php?id=${assignmentId}`, {
      method: "DELETE",
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        alert(data.message);
        loadAssignments(); // Reload assignments list
      })
      .catch((error) => console.error("Error deleting assignment:", error));
  }
}
