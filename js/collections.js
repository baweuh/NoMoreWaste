document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("collections.html")) {
    loadCollections();
    loadMerchants(); // Charge les commerçants pour le menu déroulant
    const collectionForm = document.getElementById("collectionForm");
    if (collectionForm) {
      collectionForm.addEventListener("submit", handleCollectionFormSubmit);
    } else {
      console.error("Collection form not found");
    }
  }
});


// Fonction pour charger les commerçants depuis le serveur
function loadMerchants() {
  fetch("../api/merchants.php")
    .then((response) => response.json())
    .then((data) => {
      const merchantSelect = document.getElementById("merchant_id");
      if (merchantSelect) {
        merchantSelect.innerHTML = ""; // Clear existing options
        data.forEach((merchant) => {
          const option = document.createElement("option");
          option.value = merchant.merchant_id;
          option.text = merchant.name; // Assuming the API returns an object with id and name properties
          merchantSelect.appendChild(option);
        });
      } else {
        console.error("Merchant select element not found");
      }
    })
    .catch((error) => console.error("Error loading merchants:", error));
}

// Fonction pour charger les collections depuis le serveur
function loadCollections() {
  fetch("../api/collections.php")
    .then((response) => response.json())
    .then((data) => {
      const tableBody = document.querySelector("#collectionsTable tbody");
      if (tableBody) {
        tableBody.innerHTML = "";
        data.forEach((collection) => {
          const row = document.createElement("tr");
          row.innerHTML = `
                <td>${collection.collection_id}</td>
                <td>${collection.merchant_id}</td>
                <td>${collection.collection_date}</td>
                <td>${collection.total_items}</td>
                <td>${collection.status === 0 ? "Pending" : "Delivered"}</td>
                <td>${collection.created_at}</td>
                <td>${collection.updated_at}</td>
                <td>
                  <button onclick="editCollection(${collection.collection_id})">Edit</button>
                  <button onclick="deleteCollection(${collection.collection_id})">Delete</button>
                  <button onclick="showUpdateStatusForm(${collection.collection_id})">Update Status</button>
                </td>
              `;
          tableBody.appendChild(row);
        });
      } else {
        console.error("Collections table body element not found");
      }
    })
    .catch((error) => console.error("Error loading collections:", error));
}

function showCollectionCreateForm() {
  const formTitle = document.getElementById("formTitle");
  const collectionFormContainer = document.getElementById("collectionFormContainer");
  const updateStatusContainer = document.getElementById("updateStatusContainer");

  if (formTitle && collectionFormContainer) {
    formTitle.innerText = "Create Collection";
    document.getElementById("collectionForm").reset();
    document.getElementById("collectionId").value = ""; // Assurez-vous que l'ID est vide pour la création
    document.getElementById("formSubmitButton").innerText = "Create";
    collectionFormContainer.style.display = "block";
  } else {
    console.error("Form title or collection form container element not found");
  }

  if (updateStatusContainer) {
    updateStatusContainer.style.display = "none"; // Hide status update form
  } else {
    console.error("Update status container element not found");
  }
}

function handleCollectionFormSubmit(event) {
  event.preventDefault();
  const formData = {
    collection_id: document.getElementById("collectionId").value,
    merchant_id: document.getElementById("merchant_id").value,
    collection_date: document.getElementById("collection_date").value,
    total_items: document.getElementById("total_items").value,
    status: document.getElementById("status").value,
  };

  // Déterminer si nous utilisons POST ou PUT
  const id = formData.collection_id;
  const method = id ? "PUT" : "POST";

  fetch(`../api/collections.php${id ? `?id=${id}` : ""}`, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      loadCollections();
      const collectionFormContainer = document.getElementById("collectionFormContainer");
      if (collectionFormContainer) {
        collectionFormContainer.style.display = "none";
      } else {
        console.error("Collection form container element not found");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function editCollection(id) {
  fetch(`../api/collections.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      const formTitle = document.getElementById("formTitle");
      const collectionFormContainer = document.getElementById("collectionFormContainer");
      const updateStatusContainer = document.getElementById("updateStatusContainer");

      if (formTitle && collectionFormContainer) {
        formTitle.innerText = "Edit Collection";
        document.getElementById("collectionId").value = data.collection_id;
        document.getElementById("merchant_id").value = data.merchant_id;
        document.getElementById("collection_date").value = data.collection_date;
        document.getElementById("total_items").value = data.total_items;
        document.getElementById("status").value = data.status;
        document.getElementById("formSubmitButton").innerText = "Update";
        collectionFormContainer.style.display = "block";
      } else {
        console.error("Form title or collection form container element not found");
      }

      if (updateStatusContainer) {
        updateStatusContainer.style.display = "none"; // Hide status update form
      } else {
        console.error("Update status container element not found");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function deleteCollection(id) {
  fetch(`../api/collections.php?id=${id}`, {
    method: "DELETE",
  })
    .then((response) => response.json())
    .then((data) => {
      loadCollections();
    })
    .catch((error) => console.error("Error:", error));
}

function showUpdateStatusForm(id) {
  const updateStatusContainer = document.getElementById("updateStatusContainer");
  const collectionFormContainer = document.getElementById("collectionFormContainer");

  if (updateStatusContainer) {
    document.getElementById("statusCollectionId").value = id;
    updateStatusContainer.style.display = "block";
  } else {
    console.error("Update status container element not found");
  }

  if (collectionFormContainer) {
    collectionFormContainer.style.display = "none"; // Hide create/edit form
  } else {
    console.error("Collection form container element not found");
  }
}

function handleStatusUpdateFormSubmit(event) {
  event.preventDefault();
  const statusData = {
    collection_id: document.getElementById("statusCollectionId").value,
    status: document.getElementById("statusUpdate").value,
  };

  fetch(`../api/collections.php?id=${statusData.collection_id}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(statusData),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      loadCollections();
      const updateStatusContainer = document.getElementById("updateStatusContainer");
      if (updateStatusContainer) {
        updateStatusContainer.style.display = "none";
      } else {
        console.error("Update status container element not found");
      }
    })
    .catch((error) => console.error("Error:", error));
}
