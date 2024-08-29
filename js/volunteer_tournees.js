document.addEventListener("DOMContentLoaded", function () {
  const userId = userID; // Assure-toi que userID est défini quelque part

  fetch(`../../api/volunteer_tournees.php?user_id=${userId}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((responseData) => {
      const data = responseData.data || [];
      const tableBody = document.querySelector("#tours-table tbody");
      tableBody.innerHTML = "";

      if (Array.isArray(data)) {
        if (data.length === 0) {
          console.log("Aucune tournée trouvée.");
        } else {
          data.forEach((row) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                            <td>${row.delivery_id}</td>
                            <td>${new Date(
                              row.delivery_date
                            ).toLocaleDateString()}</td>
                            <td>${row.service_name}</td>
                            <td>
                                <button class="calendar-button" 
                                        data-delivery-id="${row.delivery_id}" 
                                        data-service-id="${row.service_id}">
                                    Voir le Calendrier
                                </button>
                            </td>
                        `;
            tableBody.appendChild(tr);
          });

          document.querySelectorAll(".calendar-button").forEach((button) => {
            button.addEventListener("click", function () {
              const deliveryId = this.getAttribute("data-delivery-id");
              const serviceId = this.getAttribute("data-service-id");
              if (deliveryId && serviceId) {
                // Envoyer les données au serveur pour les stocker dans la session
                fetch("../../include/update_session.php", {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                  },
                  body: `delivery_id=${deliveryId}&service_id=${serviceId}`,
                })
                  .then((response) => response.text())
                  .then(() => {
                    // Redirection après stockage en session
                    // Assure-toi que deliveryId et serviceId sont bien définis
                    if (deliveryId && serviceId) {
                      window.location.href = `calendrier.php?delivery_id=${deliveryId}&service_id=${serviceId}`;
                    } else {
                      console.error("ID de livraison ou service ID manquant.");
                    }
                  })
                  .catch((error) =>
                    console.error(
                      "Erreur lors de la sauvegarde des données en session:",
                      error
                    )
                  );
              } else {
                console.error("ID de livraison ou service ID manquant.");
              }
            });
          });
        }
      } else {
        console.error("Les données reçues ne sont pas un tableau");
      }
    })
    .catch((error) => console.error("Erreur:", error));
});
