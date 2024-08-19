// Dans fetch.js
document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "../../api/address.php";
  const addressForm = document.getElementById("addressForm");
  const cancelButton = document.getElementById("cancelButton");

  if (cancelButton) {
    cancelButton.addEventListener("click", () => {
      window.location.href = "liste_panier.php";
    });
  }

  if (addressForm) {
    addressForm.addEventListener("submit", (event) => {
      event.preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire

      const formData = new FormData(addressForm);
      const data = {
        customer_id: formData.get("customer_id"),
        recipient_name: formData.get("recipient_name"),
        address: formData.get("address"),
        city: formData.get("city"),
        zipcode: formData.get("zipcode"),
        recipient_type: formData.get("recipient_type"),
      };

      console.log("Data being sent:", data); // Ajoutez cette ligne pour déboguer

      fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then((response) => {
          if (response.ok) {
            return response.json();
          }
          throw new Error(`HTTP error! Status: ${response.status}`);
        })
        .then((data) => {
          alert(data.message);
          if (data.message === "Delivery created.") {
            window.location.href = data.redirect_url || "address.php"; // Redirection si nécessaire
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  }
});
