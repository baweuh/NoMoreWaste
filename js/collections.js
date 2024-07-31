document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("collections.html")) {
    loadCollections();
    const collectionForm = document.getElementById("collectionForm");
    if (collectionForm) {
      collectionForm.addEventListener("submit", handleCollectionFormSubmit);
    } else {
      console.error("Collection form not found");
    }
  }
});

// Collection-related functions
function loadCollections() {
  fetch("../api/collections.php")
    .then((response) => response.json())
    .then((data) => {
      const tableBody = document.querySelector("#collectionsTable tbody");
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
                <button onclick="editCollection(${
                  collection.collection_id
                })">Edit</button>
                <button onclick="deleteCollection(${
                  collection.collection_id
                })">Delete</button>
              </td>
            `;
        tableBody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error loading collections:", error));
}

function showCollectionCreateForm() {
  document.getElementById("formTitle").innerText = "Create Collection";
  document.getElementById("collectionForm").reset();
  document.getElementById("collectionId").value = ""; // Assurez-vous que l'ID est vide pour la création
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("collectionFormContainer").style.display = "block";
  document.getElementById("updateStatusContainer").style.display = "none"; // Hide status update form
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
      document.getElementById("collectionFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function editCollection(id) {
  fetch(`../api/collections.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Collection";
      document.getElementById("collectionId").value = data.collection_id;
      document.getElementById("merchant_id").value = data.merchant_id;
      document.getElementById("collection_date").value = data.collection_date;
      document.getElementById("total_items").value = data.total_items;
      document.getElementById("status").value = data.status;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("collectionFormContainer").style.display =
        "block";
      document.getElementById("updateStatusContainer").style.display = "none"; // Hide status update form
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
  document.getElementById("statusCollectionId").value = id;
  document.getElementById("updateStatusContainer").style.display = "block";
  document.getElementById("collectionFormContainer").style.display = "none"; // Hide create/edit form
}
