document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("stocks.html")) {
    loadStocks();
    const stockForm = document.getElementById("stockForm");
    if (stockForm) {
      stockForm.addEventListener("submit", handleStockFormSubmit);
    } else {
      console.error("Stock form not found");
    }
  }
});

// Stock-related functions
function loadStocks() {
  fetch("../api/stocks.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Data loaded for stocks:", data); // Afficher les données chargées pour débogage
      if (Array.isArray(data)) {
        const tbody = document.getElementById("stocksTableBody");
        tbody.innerHTML = ""; // Vider le tableau actuel
        data.forEach((stock) => {
          const row = document.createElement("tr");
          row.id = `stock-${stock.stock_id}`; // Ajout de l'identifiant unique
          row.innerHTML = `
                  <td>${stock.stock_id}</td>
                  <td>${stock.product_id}</td>
                  <td>${stock.quantity}</td>
                  <td>${stock.location}</td>
                  <td>
                    <button onclick="editStock(${stock.stock_id})">Edit</button>
                    <button onclick="deleteStock(${stock.stock_id})">Delete</button>
                  </td>
                `;
          tbody.appendChild(row);
        });
      } else {
        console.error("Data is not an array:", data);
      }
    })
    .catch((error) => console.error("Error loading stocks:", error));
}

// Show the stock creation form
function showStockCreateForm() {
  document.getElementById("formTitle").innerText = "Create Stock";
  document.getElementById("stockForm").reset();
  document.getElementById("stockId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("stockFormContainer").style.display = "block";
}

// Edit a stock
function editStock(id) {
  fetch(`../api/stocks.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("formTitle").innerText = "Edit Stock";
      document.getElementById("stockId").value = data.stock_id;
      document.getElementById("product_id").value = data.product_id;
      document.getElementById("quantity").value = data.quantity;
      document.getElementById("location").value = data.location;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("stockFormContainer").style.display = "block";
    })
    .catch((error) => console.error("Error:", error));
}

// Handle stock form submission
function handleStockFormSubmit(event) {
  console.log("Form submitted");
  event.preventDefault();
  const formData = {
    stock_id: document.getElementById("stockId").value,
    product_id: document.getElementById("product_id").value,
    quantity: document.getElementById("quantity").value,
    location: document.getElementById("location").value,
  };

  console.log("Form Data:", formData); // Ajoutez ce log pour vérifier les données

  const id = formData.stock_id;
  const method = id ? "PUT" : "POST";

  fetch(`../api/stocks.php${id ? `?id=${id}` : ""}`, {
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
      console.log("Response Data:", data); // Ajoutez ce log pour vérifier les données de réponse
      loadStocks();
      document.getElementById("stockFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteStock(id) {
  fetch(`../api/stocks.php?id=${id}`, {
    method: "DELETE",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Delete response:", data); // Afficher la réponse après suppression pour débogage

      // Trouver et supprimer l'élément du DOM
      const row = document.getElementById(`stock-${id}`);
      if (row) {
        row.remove(); // Retirer la ligne du tableau
      }
    })
    .catch((error) => console.error("Error during delete:", error));
}
