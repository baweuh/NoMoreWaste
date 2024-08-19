document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "../../api/panier.php";
  const paniersTableBody = document.getElementById("paniersTableBody");
  const validateCartButton = document.getElementById("validateCartButton");

  LoadPaniers();
  ShowCart();

  function LoadPaniers() {
    fetch(apiUrl)
      .then((response) => response.json())
      .then((data) => {
        if (Array.isArray(data)) {
          paniersTableBody.innerHTML = "";
          data.forEach((panier) => {
            const row = document.createElement("tr");
            row.innerHTML = `
              <td>${panier.panier_id}</td>
              <td>${panier.name}</td>
              <td>${panier.quantity}</td>
              <td>${panier.expiry_date}</td>
              <input type="hidden" id="productId-${panier.panier_id}" value="${panier.product_id}">
              <input type="hidden" id="customerId-${panier.panier_id}" value="${panier.customer_id}">
              <td><button onclick="RemoveProduct(${panier.panier_id})">Supprimer le produit de votre panier</button></td>
            `;
            paniersTableBody.appendChild(row);
          });
        } else {
          paniersTableBody.innerHTML += `
            <tr><td colspan="7">Votre panier est vide</td></tr>
          `;
        }
      });
  }

  function ShowCart() {
    if (paniersTableBody && validateCartButton) {
      validateCartButton.innerHTML = "";
      const button = document.createElement("button");
      button.innerHTML = `Valider le panier`;
      button.onclick = StartDelivery; // Appeler StartDelivery lorsque le bouton est cliqué
      validateCartButton.appendChild(button);
    }
  }
});

function RemoveProduct(id) {
  fetch(`../../api/panier.php?id=${id}`, {
    method: "DELETE",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Delete response:", data);
      // Recharger les paniers après suppression
      LoadPaniers();
    })
    .catch((error) => console.error("Error:", error));
}

function StartDelivery() {
  const customerIdInputs = document.querySelectorAll('input[id^="customerId"]');
  if (customerIdInputs.length > 0) {
    // Récupère l'ID du client à partir du premier élément trouvé
    const customerId = customerIdInputs[0].value;

    // Construire l'URL de redirection vers address.php avec l'ID du client comme paramètre
    const redirectUrl = `address.php?customer_id=${encodeURIComponent(customerId)}`;

    // Redirection vers la page après récupération de l'ID du client
    window.location.href = redirectUrl;
  } else {
    alert("Votre panier est vide ou une erreur est survenue.");
  }
}

