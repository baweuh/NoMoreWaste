document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("volunteers.php")) {
    loadVolunteers();
    const volunteerForm = document.getElementById("volunteerForm");
    if (volunteerForm) {
      volunteerForm.addEventListener("submit", handleVolunteerFormSubmit);
    } else {
      console.error("Volunteer form not found");
    }
  }
});

// Volunteers-related functions
function loadVolunteers() {
  fetch("../../api/volunteers.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Loaded volunteers:", data); // Check what is returned
      const tableBody = document.querySelector("#volunteersTableBody");
      tableBody.innerHTML = "";
      data.forEach((volunteer) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                  <td>${volunteer.volunteer_id}</td>
                  <td>${volunteer.name}</td>
                  <td>${volunteer.email}</td>
                  <td>${volunteer.phone}</td>
                  <td>${volunteer.skills}</td>
                  <td>${volunteer.status}</td>
                  <td>
                    <button onclick="editVolunteer(${volunteer.volunteer_id})">Edit</button>
                    <button onclick="deleteVolunteer(${volunteer.volunteer_id})">Delete</button>
                  </td>
                `;
        tableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading volunteers:", error));
}

function showVolunteerCreateForm() {
  document.getElementById("formTitle").innerText = "Create Volunteer";
  document.getElementById("volunteerForm").reset();
  document.getElementById("volunteerId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("volunteerFormContainer").style.display = "block";
}

function handleVolunteerFormSubmit(event) {
  event.preventDefault();

  const formData = {
    volunteer_id: document.getElementById("volunteerId").value,
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    phone: document.getElementById("phone").value,
    skills: document.getElementById("skills").value,
    status: document.getElementById("status").value,
  };

  const method = formData.volunteer_id ? "PUT" : "POST";
  const url =
    "../../api/volunteers.php" +
    (formData.volunteer_id ? `?id=${formData.volunteer_id}` : "");

  fetch(url, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((data) => {
      loadVolunteers();
      document.getElementById("volunteerFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function editVolunteer(id) {
  fetch(`../../api/volunteers.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Volunteer";
      document.getElementById("volunteerId").value = data.volunteer_id;
      document.getElementById("name").value = data.name;
      document.getElementById("email").value = data.email;
      document.getElementById("phone").value = data.phone;
      document.getElementById("skills").value = data.skills;
      document.getElementById("status").value = data.status;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("volunteerFormContainer").style.display = "block";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteVolunteer(id) {
  if (confirm("Are you sure you want to delete this volunteer?")) {
    fetch(`../../api/volunteers.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        loadVolunteers();
      })
      .catch((error) => console.error("Error:", error));
  }
}
