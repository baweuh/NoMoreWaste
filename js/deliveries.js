document.addEventListener("DOMContentLoaded", () => {
  // Charger les livraisons et les clients au démarrage
  loadDeliveries();
  loadCustomers();

  // Attacher les gestionnaires d'événements aux éléments du DOM
  const showCreateFormButton = document.getElementById("showCreateFormButton");
  const deliveryForm = document.getElementById("deliveryForm");
  const editDeliveryForm = document.getElementById("editDeliveryForm");

  if (showCreateFormButton) {
    showCreateFormButton.addEventListener("click", showDeliveryCreateForm);
  } else {
    console.error("Element with id 'showCreateFormButton' not found.");
  }

  if (deliveryForm) {
    deliveryForm.addEventListener("submit", handleDeliveryFormSubmit);
  } else {
    console.error("Element with id 'deliveryForm' not found.");
  }

  if (editDeliveryForm) {
    editDeliveryForm.addEventListener("submit", handleEditFormSubmit);
  } else {
    console.error("Element with id 'editDeliveryForm' not found.");
  }
});

function getFormDataAsJSON(formElement) {
  const formData = new FormData(formElement);
  const formObject = {};

  formData.forEach((value, key) => {
    formObject[key] = value;
  });

  return formObject;
}

function loadCustomers() {
  fetch("../../api/customers.php")
    .then((response) => response.json())
    .then((data) => {
      if (!Array.isArray(data)) {
        console.error("Invalid data format from customers API");
        return;
      }

      // Remplir les menus déroulants pour la création et l'édition
      const recipientSelect = document.getElementById("customer_id");
      const editRecipientSelect = document.getElementById("edit_customer_id");

      const optionsHtml = data
        .map(
          (customer) =>
            `<option value="${customer.customer_id}">${customer.name}</option>`
        )
        .join("");

      if (recipientSelect) {
        recipientSelect.innerHTML = `<option value="" selected disabled>Select Recipient</option>${optionsHtml}`;
      } else {
        console.error("Element with id 'customer_id' not found.");
      }

      if (editRecipientSelect) {
        editRecipientSelect.innerHTML = `<option value="" selected disabled>Select Recipient</option>${optionsHtml}`;
      } else {
        console.error("Element with id 'edit_customer_id' not found.");
      }
    })
    .catch((error) => console.error("Error loading customers:", error));
}

function showDeliveryCreateForm() {
  document.getElementById("deliveryFormContainer").style.display = "block";
  document.getElementById("editDeliveryFormContainer").style.display = "none";
}

function handleDeliveryFormSubmit(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const submitButton = event.target.querySelector('button[type="submit"]');
  submitButton.disabled = true;

  fetch("../../api/deliveries.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Response data:", data); // Debugging
      alert(data.message);
      if (data.message === "Delivery created.") {
        loadDeliveries();
        document.getElementById("deliveryForm").reset();
        document.getElementById("deliveryFormContainer").style.display = "none";
      }
    })
    .catch((error) => console.error("Error:", error))
    .finally(() => {
      submitButton.disabled = false;
    });
}

function loadDeliveries() {
  fetch("../../api/deliveries.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Data received from API:", data); // Ligne de débogage

      if (!Array.isArray(data)) {
        throw new Error("Invalid data format: Expected an array.");
      }

      const deliveriesTableBody = document.getElementById(
        "deliveriesTableBody"
      );
      if (deliveriesTableBody) {
        deliveriesTableBody.innerHTML = "";

        data.forEach((delivery) => {
          if (
            !delivery.delivery_id ||
            !delivery.delivery_date ||
            !delivery.recipient_type ||
            delivery.customer_id === null || // Assurez-vous que customer_id n'est pas null
            delivery.customer_id === undefined || // Assurez-vous que customer_id n'est pas undefined
            delivery.status === undefined
          ) {
            console.error("Invalid delivery data:", delivery);
            return;
          }

          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${delivery.delivery_id}</td>
            <td>${delivery.delivery_date}</td>
            <td>${delivery.recipient_type}</td>
            <td>${delivery.customer_id || "N/A"}</td>
            <td>${delivery.status}</td>
            <td><a href="${
              delivery.pdf_report_path
            }" target="_blank">View PDF</a></td>
            <td>
              <button onclick="showEditDeliveryForm(${
                delivery.delivery_id
              })">Edit</button>
              <button onclick="deleteDelivery(${
                delivery.delivery_id
              })">Delete</button>
            </td>
          `;
          deliveriesTableBody.appendChild(row);
        });
      } else {
        console.error("Element with id 'deliveriesTableBody' not found.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// Fonction pour afficher le formulaire d'édition avec les données de la livraison
function showEditDeliveryForm(deliveryId) {
  fetch(`../../api/deliveries.php?id=${deliveryId}`, {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.delivery_id) {
        document.getElementById("edit_delivery_date").value =
          data.delivery_date;
        document.getElementById("edit_recipient_type").value =
          data.recipient_type;
        document.getElementById("edit_customer_id").value = data.customer_id;
        document.getElementById("edit_status").value = data.status;
        document.getElementById("editDeliveryId").value = data.delivery_id; // Assurez-vous que ce champ existe
        document.getElementById("editDeliveryFormContainer").style.display =
          "block";
      } else {
        alert("Delivery not found.");
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Fonction pour gérer la soumission du formulaire d'édition
function handleEditFormSubmit(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const submitButton = event.target.querySelector('button[type="submit"]');
  submitButton.disabled = true;

  // Vérifiez et affichez le contenu de formData pour débogage
  for (let pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }

  fetch("../../api/deliveries.php", {
    method: "PUT",
    body: new URLSearchParams(formData), // Utilisez URLSearchParams pour encoder les données
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Response data:", data); // Debugging
      alert(data.message);
      if (data.message === "Delivery updated.") {
        loadDeliveries();
        document.getElementById("editDeliveryForm").reset();
        document.getElementById("editDeliveryFormContainer").style.display =
          "none";
      }
    })
    .catch((error) => console.error("Error:", error))
    .finally(() => {
      submitButton.disabled = false;
    });
}

function deleteDelivery(id) {
  if (confirm("Are you sure you want to delete this delivery?")) {
    fetch(`../../api/deliveries.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        if (data.message === "Delivery deleted.") {
          loadDeliveries();
        }
      })
      .catch((error) => console.error("Error:", error));
  }
}
