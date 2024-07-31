document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("deliveries.html")) {
    loadDeliveries();
    const deliveryForm = document.getElementById("deliveryForm");
    if (deliveryForm) {
      deliveryForm.addEventListener("submit", handleDeliveryFormSubmit);
    } else {
      console.error("Delivery form not found");
    }
  }
});

// Fonction pour afficher le formulaire de création
function showDeliveryCreateForm() {
  document.getElementById("deliveryFormContainer").style.display = "block";
  document.getElementById("editDeliveryFormContainer").style.display = "none";
}

// Fonction pour afficher le formulaire d'édition
function showEditDeliveryForm() {
  document.getElementById("deliveryFormContainer").style.display = "none";
  document.getElementById("editDeliveryFormContainer").style.display = "block";
}

// Fonction pour charger les livraisons
function loadDeliveries() {
  fetch("../api/deliveries.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Data:", data);
      if (Array.isArray(data)) {
        const tbody = document.getElementById("deliveriesTableBody");
        tbody.innerHTML = "";
        data.forEach((delivery) => {
          const row = document.createElement("tr");
          row.id = `delivery-${delivery.delivery_id}`;
          row.innerHTML = `
                <td>${delivery.delivery_id}</td>
                <td>${delivery.delivery_date}</td>
                <td>${delivery.recipient_type}</td>
                <td>${delivery.recipient_id}</td>
                <td>${delivery.status}</td>
                <td><a href="../uploads/${delivery.pdf_report_path}" target="_blank">View PDF</a></td>
                <td>
                  <button onclick="editDelivery(${delivery.delivery_id})">Edit</button>
                  <button onclick="deleteDelivery(${delivery.delivery_id})">Delete</button>
                </td>
              `;
          tbody.appendChild(row);
        });
      } else {
        console.error("Data is not an array:", data);
      }
    })
    .catch((error) => console.error("Error loading deliveries:", error));
}

// Fonction pour éditer une livraison
function editDelivery(id) {
  fetch(`../api/deliveries.php?id=${id}`)
    .then((response) => response.json())
    .then((data) => {
      document.getElementById("editFormTitle").innerText = "Edit Delivery";
      document.getElementById("editDeliveryId").value = data.delivery_id;
      document.getElementById("edit_delivery_date").value = data.delivery_date;
      document.getElementById("edit_recipient_type").value =
        data.recipient_type;
      document.getElementById("edit_recipient_id").value = data.recipient_id;
      document.getElementById("edit_status").value = data.status;

      // Affichage du lien PDF existant
      if (data.pdf_report_path) {
        document.getElementById(
          "edit_pdf_report_link"
        ).innerHTML = `<a href="../uploads/${data.pdf_report_path}" target="_blank">View PDF</a>`;
      } else {
        document.getElementById("edit_pdf_report_link").innerHTML =
          "No PDF available";
      }

      // Afficher le formulaire d'édition et masquer le formulaire de création
      showEditDeliveryForm();
    })
    .catch((error) => console.error("Error:", error));
}

// Fonction pour gérer l'envoi du formulaire de création
function handleDeliveryFormSubmit(event) {
  event.preventDefault();

  // Récupérer les valeurs du formulaire
  const deliveryDate = document.getElementById("delivery_date").value;
  const recipientType = document.getElementById("recipient_type").value;
  const recipientId = document.getElementById("recipient_id").value;
  const status = document.getElementById("status").value;
  const pdfReport = document.getElementById("pdf_report").files[0];
  const deliveryId = document.getElementById("deliveryId").value;

  // Vérifiez si les champs requis sont remplis
  if (!deliveryDate || !recipientType || !recipientId || !status) {
    alert("Please fill in all required fields.");
    return;
  }

  // Préparer les données du formulaire pour l'envoi
  const formData = new FormData();
  formData.append("delivery_date", deliveryDate);
  formData.append("recipient_type", recipientType);
  formData.append("recipient_id", recipientId);
  formData.append("status", status);

  // Ajouter le fichier PDF s'il existe
  if (pdfReport) {
    formData.append("pdf_report", pdfReport);
  }

  // Déterminer la méthode HTTP et l'URL
  const method = deliveryId ? "PUT" : "POST";
  const url = deliveryId
    ? `../api/deliveries.php?id=${deliveryId}`
    : "../api/deliveries.php";

  // Envoyer la requête
  fetch(url, {
    method: method,
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Form submission response:", data);
      if (data.errors) {
        console.error("Server validation errors:", data.errors);
        alert(
          "There were errors with your submission. Check the console for details."
        );
      } else {
        loadDeliveries(); // Recharger les livraisons après la soumission

        // Réinitialiser le formulaire
        const deliveryForm = document.getElementById("deliveryForm");
        if (deliveryForm && typeof deliveryForm.reset === "function") {
          deliveryForm.reset();
        }

        // Masquer le formulaire de création
        document.getElementById("deliveryFormContainer").style.display = "none";
      }
    })
    .catch((error) => console.error("Error submitting form:", error));
}

// Fonction pour gérer l'envoi du formulaire d'édition
function handleEditFormSubmit(event) {
  event.preventDefault();

  // Récupérer les valeurs du formulaire d'édition
  const deliveryDate = document.getElementById("edit_delivery_date").value;
  const recipientType = document.getElementById("edit_recipient_type").value;
  const recipientId = document.getElementById("edit_recipient_id").value;
  const status = document.getElementById("edit_status").value;
  const pdfReport = document.getElementById("edit_pdf_report").files[0];
  const deliveryId = document.getElementById("editDeliveryId").value;

  // Vérifiez si les champs requis sont remplis
  if (!deliveryDate || !recipientType || !recipientId || !status) {
    alert("Please fill in all required fields.");
    return;
  }

  // Préparer les données du formulaire pour l'envoi
  const formData = new URLSearchParams();
  formData.append("delivery_id", deliveryId);
  formData.append("delivery_date", deliveryDate);
  formData.append("recipient_type", recipientType);
  formData.append("recipient_id", recipientId);
  formData.append("status", status);

  // Ajouter le fichier PDF s'il existe
  if (pdfReport) {
    const reader = new FileReader();
    reader.onloadend = function () {
      formData.append("pdf_report", reader.result.split(",")[1]); // Extraire la partie base64
      // Envoyer la requête
      fetch(`../api/deliveries.php`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: formData.toString(),
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Edit form submission response:", data);
          if (data.errors) {
            console.error("Server validation errors:", data.errors);
            alert(
              "There were errors with your submission. Check the console for details."
            );
          } else {
            loadDeliveries(); // Recharger les livraisons après la soumission

            // Réinitialiser le formulaire d'édition
            const editForm = document.getElementById("editDeliveryForm");
            if (editForm && typeof editForm.reset === "function") {
              editForm.reset();
            }

            // Masquer le formulaire d'édition
            document.getElementById("editDeliveryFormContainer").style.display =
              "none";
          }
        })
        .catch((error) => console.error("Error submitting form:", error));
    };
    reader.readAsDataURL(pdfReport);
  } else {
    // Envoyer la requête sans PDF
    fetch(`../api/deliveries.php`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: formData.toString(),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("Edit form submission response:", data);
        if (data.errors) {
          console.error("Server validation errors:", data.errors);
          alert(
            "There were errors with your submission. Check the console for details."
          );
        } else {
          loadDeliveries(); // Recharger les livraisons après la soumission

          // Réinitialiser le formulaire d'édition
          const editForm = document.getElementById("editDeliveryForm");
          if (editForm && typeof editForm.reset === "function") {
            editForm.reset();
          }

          // Masquer le formulaire d'édition
          document.getElementById("editDeliveryFormContainer").style.display =
            "none";
        }
      })
      .catch((error) => console.error("Error submitting form:", error));
  }
}

// Fonction pour supprimer une livraison
function deleteDelivery(id) {
  fetch(`../api/deliveries.php?id=${id}`, {
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
      const row = document.getElementById(`delivery-${id}`);
      if (row) {
        row.remove(); // Retirer la ligne du tableau
      }
    })
    .catch((error) => console.error("Error during delete:", error));
}
