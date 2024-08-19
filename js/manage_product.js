document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "../../api/merchant_product.php";
  const productsTableBody = document.getElementById("productsTableBody");
  const addProductForm = document.getElementById("addProductForm");
  const editProductForm = document.getElementById("editProductForm");
  const addCollectionSelect = document.getElementById("addCollectionId");
  const editCollectionSelect = document.getElementById("editCollectionId");

  // Load products
  function loadProducts() {
    fetch(apiUrl)
      .then((response) => response.json())
      .then((data) => {
        if (Array.isArray(data)) {
          productsTableBody.innerHTML = "";
          data.forEach((product) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                            <td>${product.product_id}</td>
                            <td>${product.barcode}</td>
                            <td>${product.name}</td>
                            <td>${product.quantity}</td>
                            <td>${product.expiry_date}</td>
                            <td>${product.collection_name}</td>
                            <td>
                                <button onclick="editProduct(${product.product_id})">Modifier</button>
                                <button onclick="deleteProduct(${product.product_id})">Supprimer</button>
                            </td>
                        `;
            productsTableBody.appendChild(row);
          });
        } else {
          productsTableBody.innerHTML =
            '<tr><td colspan="7">Aucun produit trouvé.</td></tr>';
        }
      });
  }

  // Load collections
  function loadCollections() {
    fetch("../../api/merchant_collection.php")
      .then((response) => response.json())
      .then((data) => {
        if (Array.isArray(data)) {
          addCollectionSelect.innerHTML = "";
          editCollectionSelect.innerHTML = "";
          data.forEach((collection) => {
            const addOption = document.createElement("option");
            addOption.value = collection.collection_id;
            addOption.textContent = collection.name;
            addCollectionSelect.appendChild(addOption);

            const editOption = document.createElement("option");
            editOption.value = collection.collection_id;
            editOption.textContent = collection.name;
            editCollectionSelect.appendChild(editOption);
          });
        }
      });
  }

  // Add product
  addProductForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const barcode = document.getElementById("addBarcode").value;
    const name = document.getElementById("addProductName").value;
    const quantity = document.getElementById("addQuantity").value;
    const expiryDate = document.getElementById("addExpiryDate").value;
    const collectionId = document.getElementById("addCollectionId").value;

    fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        barcode,
        name,
        quantity,
        expiry_date: expiryDate,
        collection_id: collectionId,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        loadProducts();
        addProductForm.reset();
      });
  });

  // Edit product
  window.editProduct = function (productId) {
    fetch(`${apiUrl}?id=${productId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.product_id) {
          document.getElementById("editProductId").value = data.product_id;
          document.getElementById("editBarcode").value = data.barcode;
          document.getElementById("editProductName").value = data.name;
          document.getElementById("editQuantity").value = data.quantity;
          document.getElementById("editExpiryDate").value = data.expiry_date;
          document.getElementById("editCollectionId").value =
            data.collection_id;

          addProductForm.style.display = "none";
          editProductForm.style.display = "block";
        } else {
          console.error("Product data is incomplete:", data);
        }
      })
      .catch((error) => console.error("Error fetching product data:", error));
  };

  // Update product
  editProductForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const productId = document.getElementById("editProductId").value;
    const barcode = document.getElementById("editBarcode").value;
    const name = document.getElementById("editProductName").value;
    const quantity = document.getElementById("editQuantity").value;
    const expiryDate = document.getElementById("editExpiryDate").value;
    const collectionId = document.getElementById("editCollectionId").value;

    fetch(apiUrl, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        product_id: productId,
        barcode,
        name,
        quantity,
        expiry_date: expiryDate,
        collection_id: collectionId,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        loadProducts();
        editProductForm.reset();
        addProductForm.style.display = "block";
        editProductForm.style.display = "none";
      })
      .catch((error) => console.error("Error updating product:", error));
  });

  // Delete product
  window.deleteProduct = function (productId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
      fetch(apiUrl, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ product_id: productId }),
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message);
          loadProducts();
        })
        .catch((error) => console.error("Error deleting product:", error));
    }
  };

  loadProducts();
  loadCollections();
});
