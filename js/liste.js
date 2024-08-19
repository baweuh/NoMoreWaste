document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "../../api/liste.php";
  const productsTableBody = document.getElementById("productsTableBody");

  loadProducts();
  loadCollections();

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
                          <td><button data-product-id="${product.product_id}" class="add-to-cart-btn">Ajouter au panier</button></td>
                      `;
                      productsTableBody.appendChild(row);
                  });

                  // Ajouter l'événement pour chaque bouton "Ajouter au panier"
                  document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                      button.addEventListener('click', function () {
                          const productId = this.getAttribute('data-product-id');
                          addToCart(productId);
                      });
                  });
              } else {
                  productsTableBody.innerHTML = `
                      <tr><td colspan="7">Aucun produit disponible</td></tr>
                  `;
              }
          });
  }

  function loadCollections() {
      fetch("../../api/collections.php").then((response) => response.json());
  }

  function addToCart(productId) {
      fetch(apiUrl, {
          method: "POST",
          headers: {
              "Content-Type": "application/json"
          },
          body: JSON.stringify({ product_id: productId, quantity: 1 }) // Inclure quantity si nécessaire
      })
      .then(response => response.json())
      .then(data => {
          alert(data.message)
          loadProducts();
      })
      .catch(error => {
          console.error('Error:', error);
          alert("Une erreur est survenue lors de l'ajout du produit au panier.");
      });
  }
});
