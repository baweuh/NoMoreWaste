document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "../../api/address.php";
  const addressForm = document.getElementById("addressForm");
  const cancelButton = document.getElementById("cancelButton");
  const servicesContainer = document.getElementById("servicesContainer");

  // Fetch and display services
  fetch(apiUrl)
    .then((response) => response.json())
    .then((data) => {
      if (data.services) {
        data.services.forEach((service) => {
          const checkbox = document.createElement("input");
          checkbox.type = "checkbox";
          checkbox.className = "api";
          checkbox.name = "services[]";
          checkbox.value = service.service_id; // Assurez-vous d'utiliser le bon champ
          checkbox.id = `service_${service.service_id}`;

          const label = document.createElement("label");
          label.htmlFor = checkbox.id;
          label.innerText = service.name;

          servicesContainer.appendChild(checkbox);
          servicesContainer.appendChild(label);
          servicesContainer.appendChild(document.createElement("br"));
        });
      } else {
        servicesContainer.innerText = "Aucun service disponible.";
      }
    })
    .catch((error) => console.error("Error fetching services:", error));

  if (cancelButton) {
    cancelButton.addEventListener("click", () => {
      window.location.href = "liste_panier.php";
    });
  }

  if (addressForm) {
    addressForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const formData = new FormData(addressForm);

      const selectedServices = [];
      document.querySelectorAll(".api:checked").forEach((checkbox) => {
        selectedServices.push(checkbox.value);
      });

      const data = {
        customer_id: formData.get("customer_id"),
        recipient_name: formData.get("recipient_name"),
        address: formData.get("address"),
        city: formData.get("city"),
        zipcode: formData.get("zipcode"),
        recipient_type: formData.get("recipient_type"),
        services: selectedServices,
      };

      console.log("Data being sent:", data);

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
            window.location.href = data.redirect_url || "address.php";
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  }
});
