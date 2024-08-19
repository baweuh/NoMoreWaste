document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("merchants.html")) {
    loadMerchants();
    const merchantForm = document.getElementById("merchantForm");
    if (merchantForm) {
      merchantForm.addEventListener("submit", handleMerchantFormSubmit);
    } else {
      console.error("Merchant form not found");
    }
  }
});

// Merchant-related functions
function loadMerchants() {
  fetch("../../api/merchants.php")
    .then((response) => response.json())
    .then((data) => {
      const tableBody = document.querySelector("#merchantsTable tbody");
      tableBody.innerHTML = "";
      data.forEach((merchant) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${merchant.merchant_id}</td>
          <td>${merchant.name}</td>
          <td>${merchant.address}</td>
          <td>${merchant.phone}</td>
          <td>${merchant.email}</td>
          <td>${merchant.membership_start_date}</td>
          <td>${merchant.membership_end_date}</td>
          <td>
            <button onclick="editMerchant(${merchant.merchant_id})">Edit</button>
            <button onclick="deleteMerchant(${merchant.merchant_id})">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading merchants:", error));
}

function showMerchantCreateForm() {
  document.getElementById("formTitle").innerText = "Create Merchant";
  document.getElementById("merchantForm").reset();
  document.getElementById("merchantId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("merchantFormContainer").style.display = "block";
}

function handleMerchantFormSubmit(event) {
  event.preventDefault();

  const formData = {
    merchant_id: document.getElementById("merchantId").value,
    name: document.getElementById("name").value,
    address: document.getElementById("address").value,
    phone: document.getElementById("phone").value,
    email: document.getElementById("email").value,
    membership_start_date: document.getElementById("membership_start_date")
      .value,
    membership_end_date: document.getElementById("membership_end_date").value,
    renewal_reminder_sent: false,
  };

  const method = formData.merchant_id ? "PUT" : "POST";
  const url = `../../api/merchants.php${
    formData.merchant_id ? `?id=${formData.merchant_id}` : ""
  }`;

  fetch(url, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((data) => {
      loadMerchants();
      document.getElementById("merchantFormContainer").style.display = "none";
      document.getElementById("merchantForm").reset(); // Réinitialiser le formulaire
      document.getElementById("merchantId").value = ""; // Réinitialiser le champ caché
      document.getElementById("formSubmitButton").innerText = "Create"; // Réinitialiser le texte du bouton
    })
    .catch((error) => console.error("Error:", error));
}

function editMerchant(id) {
  fetch(`../../api/merchants.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Merchant";
      document.getElementById("merchantId").value = data.merchant_id;
      document.getElementById("name").value = data.name;
      document.getElementById("address").value = data.address;
      document.getElementById("phone").value = data.phone;
      document.getElementById("email").value = data.email;
      document.getElementById("membership_start_date").value =
        data.membership_start_date;
      document.getElementById("membership_end_date").value =
        data.membership_end_date;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("merchantFormContainer").style.display = "block";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteMerchant(id) {
  fetch(`../../api/merchants.php?id=${id}`, {
    method: "DELETE",
  })
    .then((response) => response.json())
    .then((data) => {
      loadMerchants();
    })
    .catch((error) => console.error("Error:", error));
}
