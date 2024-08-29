document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("services.php")) {
    loadServices();
    const serviceForm = document.getElementById("serviceForm");
    if (serviceForm) {
      serviceForm.addEventListener("submit", handleServiceFormSubmit);
    } else {
      console.error("Service form not found");
    }
  }
});

function loadServices() {
  fetch("../../api/services.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Loaded services:", data);
      const tableBody = document.querySelector("#servicesTableBody");
      tableBody.innerHTML = "";
      data.forEach((service) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                <td>${service.service_id}</td>
                <td>${service.name}</td>
                <td>${service.description}</td>
                <td>
                  <button onclick="editService(${service.service_id})">Edit</button>
                  <button onclick="deleteService(${service.service_id})">Delete</button>
                </td>
              `;
        tableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading services:", error));
}

function showServiceCreateForm() {
  document.getElementById("formTitle").innerText = "Create Service";
  document.getElementById("serviceForm").reset();
  document.getElementById("serviceId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("serviceFormContainer").style.display = "block";
}

function handleServiceFormSubmit(event) {
  event.preventDefault();

  const formData = {
    service_id: document.getElementById("serviceId").value,
    name: document.getElementById("name").value,
    description: document.getElementById("description").value,
  };

  const method = formData.service_id ? "PUT" : "POST";
  const url =
    "../../api/services.php" +
    (formData.service_id ? `?id=${formData.service_id}` : "");

  fetch(url, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((data) => {
      loadServices();
      document.getElementById("serviceFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function editService(id) {
  fetch(`../../api/services.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Service";
      document.getElementById("serviceId").value = data.service_id;
      document.getElementById("name").value = data.name;
      document.getElementById("description").value = data.description;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("serviceFormContainer").style.display = "block";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteService(id) {
  if (confirm("Are you sure you want to delete this service?")) {
    fetch(`../../api/services.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        loadServices();
      })
      .catch((error) => console.error("Error:", error));
  }
}
