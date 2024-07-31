document.addEventListener("DOMContentLoaded", function () {
  if (
    window.location.pathname.includes("users.html")
  ) {
    loadUsers();
    const userForm = document.getElementById("userForm");
    if (userForm) {
      userForm.addEventListener("submit", handleUserFormSubmit);
    } else {
      console.error("User form not found");
    }
  }
});

function loadUsers() {
  fetch("../api/users.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Loaded users:", data);
      const tableBody = document.querySelector("#usersTableBody");
      tableBody.innerHTML = "";
      data.forEach((user) => {
        const row = document.createElement("tr");
        row.innerHTML = `
              <td>${user.user_id}</td>
              <td>${user.username}</td>
              <td>${user.role}</td>
              <td>
                <button onclick="editUser(${user.user_id})">Edit</button>
                <button onclick="deleteUser(${user.user_id})">Delete</button>
              </td>
            `;
        tableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading users:", error));
}

function showUserCreateForm() {
  document.getElementById("formTitle").innerText = "Create User";
  document.getElementById("userForm").reset();
  document.getElementById("userId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("userFormContainer").style.display = "block";
}

function handleUserFormSubmit(event) {
  event.preventDefault();

  const formData = {
    user_id: document.getElementById("userId").value,
    username: document.getElementById("username").value,
    password: document.getElementById("password").value,
    role: document.getElementById("role").value,
  };

  const method = formData.user_id ? "PUT" : "POST";
  const url =
    "../api/users.php" +
    (formData.user_id ? `?id=${formData.user_id}` : "");

  fetch(url, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((data) => {
      loadUsers();
      document.getElementById("userFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function editUser(id) {
  fetch(`../api/users.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit User";
      document.getElementById("userId").value = data.user_id;
      document.getElementById("username").value = data.username;
      document.getElementById("role").value = data.role;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("userFormContainer").style.display = "block";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    fetch(`../api/users.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        loadUsers();
      })
      .catch((error) => console.error("Error:", error));
  }
}