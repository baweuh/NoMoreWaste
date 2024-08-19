document.addEventListener("DOMContentLoaded", function () {
  // Charger les stocks et les produits lorsque le DOM est prêt
  if (window.location.pathname.includes("stocks.html")) {
    loadProducts();
    loadStocks();
    
    const stockForm = document.getElementById("stockForm");
    if (stockForm) {
      stockForm.addEventListener("submit", handleStockFormSubmit);
    } else {
      console.error("Stock form not found");
    }
  }
});

// Définir la fonction pour afficher le formulaire de création/modification de stock
function showStockCreateForm() {
  document.getElementById("formTitle").innerText = "Create New Stock";
  document.getElementById("stockId").value = ""; // Réinitialiser l'ID du stock
  document.getElementById("product_id").value = ""; // Réinitialiser le produit sélectionné
  document.getElementById("quantity").value = ""; // Réinitialiser la quantité
  document.getElementById("location").value = ""; // Réinitialiser la localisation
  document.getElementById("formSubmitButton").innerText = "Create"; // Bouton "Create"
  document.getElementById("stockFormContainer").style.display = "block"; // Afficher le formulaire
}


// Load products from the API and populate the select element
function loadProducts() {
  fetch("../../api/products.php")
    .then(response => response.json())
    .then(data => {
      const productSelect = document.getElementById("product_id");
      productSelect.innerHTML = ""; // Clear existing options

      // Check if the data is an array
      if (Array.isArray(data)) {
        data.forEach(product => {
          const option = document.createElement("option");
          option.value = product.product_id; // Set the value to product_id
          option.textContent = `${product.product_id}`; // Display product_id
          productSelect.appendChild(option);
        });
      } else {
        console.error("Unexpected data format for products:", data);
      }
    })
    .catch(error => console.error("Error loading products:", error));
}

// Handle stock form submission
function handleStockFormSubmit(event) {
  event.preventDefault();
  const formData = {
    stock_id: document.getElementById("stockId").value,
    product_id: document.getElementById("product_id").value,
    quantity: document.getElementById("quantity").value,
    location: document.getElementById("location").value,
  };

  const id = formData.stock_id;
  const method = id ? "PUT" : "POST";

  fetch(`../../api/stocks.php${id ? `?id=${id}` : ""}`, {
    method: method,
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      loadStocks(); // Reload stocks after submission
      document.getElementById("stockFormContainer").style.display = "none";
    })
    .catch(error => console.error("Error:", error));
}

// Load existing stocks and populate the table
function loadStocks() {
  fetch("../../api/stocks.php")
    .then(response => response.json())
    .then(data => {
      const tbody = document.getElementById("stocksTableBody");
      tbody.innerHTML = "";

      if (Array.isArray(data)) {
        data.forEach(stock => {
          const row = document.createElement("tr");
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
        console.error("Unexpected data format for stocks:", data);
      }
    })
    .catch(error => console.error("Error loading stocks:", error));
}

// Edit a stock
function editStock(id) {
  fetch(`../../api/stocks.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
      document.getElementById("formTitle").innerText = "Edit Stock";
      document.getElementById("stockId").value = data.stock_id;
      document.getElementById("product_id").value = data.product_id;
      document.getElementById("quantity").value = data.quantity;
      document.getElementById("location").value = data.location;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("stockFormContainer").style.display = "block";
    })
    .catch(error => console.error("Error:", error));
}

// Delete a stock
function deleteStock(id) {
  fetch(`../../api/stocks.php?id=${id}`, {
    method: "DELETE",
  })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      loadStocks(); // Reload stocks after deletion
    })
    .catch(error => console.error("Error:", error));
}
