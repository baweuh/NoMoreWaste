document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("products.html")) {
    loadProducts();
    loadCollections(); // Load collections when the page is loaded
    const productForm = document.getElementById("productForm");
    if (productForm) {
      productForm.addEventListener("submit", handleProductFormSubmit);
    } else {
      console.error("Product form not found");
    }
  }
});

function loadProducts() {
  fetch("../../api/products.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Products data:", data); // Log the data for debugging
      if (Array.isArray(data)) {
        const tbody = document.getElementById("productsTableBody");
        tbody.innerHTML = "";
        data.forEach((product) => {
          const row = document.createElement("tr");
          row.innerHTML = `
                      <td>${product.product_id}</td>
                      <td>${product.barcode}</td>
                      <td>${product.name}</td>
                      <td>${product.quantity}</td>
                      <td>${product.expiry_date}</td>
                      <td>${product.collection_id}</td>
                      <td>
                          <button onclick="editProduct(${product.product_id})">Edit</button>
                          <button onclick="deleteProduct(${product.product_id})">Delete</button>
                      </td>
                  `;
          tbody.appendChild(row);
        });
      } else {
        console.error("Expected an array but got:", data);
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Load collections from the API and populate the select element
function loadCollections() {
  fetch("../../api/collections.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Collections loaded:", data); // Log the collections data
      const collectionSelect = document.getElementById("collection_id");
      collectionSelect.innerHTML = ""; // Clear existing options

      // Check if the data is an array
      if (Array.isArray(data)) {
        data.forEach((collection) => {
          const option = document.createElement("option");
          option.value = collection.collection_id; // Set the value to collection_id
          option.textContent = `${collection.collection_id}`; // Display collection_id
          collectionSelect.appendChild(option);
        });
      } else {
        console.error("Unexpected data format for collections:", data);
      }
    })
    .catch((error) => console.error("Error loading collections:", error));
}

function showProductCreateForm() {
  document.getElementById("formTitle").innerText = "Create Product";
  document.getElementById("productForm").reset();
  document.getElementById("productId").value = "";
  document.getElementById("formSubmitButton").innerText = "Create";
  document.getElementById("productFormContainer").style.display = "block";
  loadCollections(); // Load collections when showing the form
}

function editProduct(id) {
  fetch(`../../api/products.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      console.log("Product data for editing:", data); // Log the data for debugging
      document.getElementById("formTitle").innerText = "Edit Product";
      document.getElementById("productId").value = data.product_id;
      document.getElementById("barcode").value = data.barcode;
      document.getElementById("name").value = data.name;
      document.getElementById("quantity").value = data.quantity;
      document.getElementById("expiry_date").value = data.expiry_date;
      document.getElementById("collection_id").value = data.collection_id;
      document.getElementById("formSubmitButton").innerText = "Update";
      document.getElementById("productFormContainer").style.display = "block";
      loadCollections(); // Load collections when showing the form
    })
    .catch((error) => console.error("Error:", error));
}

function handleProductFormSubmit(event) {
  event.preventDefault();
  const formData = {
    product_id: document.getElementById("productId").value,
    barcode: document.getElementById("barcode").value,
    name: document.getElementById("name").value,
    quantity: document.getElementById("quantity").value,
    expiry_date: document.getElementById("expiry_date").value,
    collection_id: document.getElementById("collection_id").value,
  };

  const id = formData.product_id;
  const method = id ? "PUT" : "POST";

  fetch(`../../api/products.php${id ? `?id=${id}` : ""}`, {
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
      console.log("Form submit response:", data); // Log the response for debugging
      loadProducts();
      document.getElementById("productFormContainer").style.display = "none";
    })
    .catch((error) => console.error("Error:", error));
}

function deleteProduct(id) {
  fetch(`../../api/products.php?id=${id}`, {
    method: "DELETE",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Delete response:", data); // Log the response for debugging
      loadProducts();
    })
    .catch((error) => console.error("Error:", error));
}
