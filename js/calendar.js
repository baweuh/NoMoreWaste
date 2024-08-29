document.addEventListener("DOMContentLoaded", function () {
    const reservationForm = document.getElementById("reservationForm");
    const dateTimeInput = document.getElementById("dateTime");

    // Récupère les IDs depuis les inputs cachés
    const deliveryId = document.getElementById('deliveryId').value;
    const serviceId = document.getElementById('serviceId').value;
    const volunteerId = document.getElementById('volunteerId').value;

    // Vérifie si les variables sont définies
    if (!deliveryId || !serviceId || !volunteerId) {
        console.error("Les variables deliveryId, serviceId ou volunteerId ne sont pas définies.");
        return;
    }

    // Charge les créneaux disponibles lorsque la page est chargée
    loadAvailableSlots();

    // Gère la soumission du formulaire
    reservationForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const selectedSlot = dateTimeInput.value;

        if (!selectedSlot) {
            alert("Veuillez sélectionner un créneau.");
            return;
        }

        // Appelle l'API pour réserver le créneau sélectionné
        reserveSlot(selectedSlot);
    });

    // Fonction pour charger les créneaux disponibles depuis l'API
    function loadAvailableSlots() {
        fetch(`../../api/calendar.php?delivery_id=${deliveryId}&service_id=${serviceId}`, {
            method: "GET",
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.delivery_date) {
                console.log("Date de livraison:", data.delivery_date);
                // Traite la date de livraison comme nécessaire
            } else {
                console.error("Erreur:", data.message || "Données manquantes.");
            }
        })
        .catch(error => {
            console.error("Erreur lors de la récupération de la date de livraison:", error);
        });
    }

    // Fonction pour réserver un créneau en appelant l'API
    function reserveSlot(dateTime) {
        fetch("../../api/calendar.php", {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                dateTime: dateTime, // Envoie la date et l'heure sélectionnées
                volunteer_id: volunteerId, // Utilise l'ID de bénévole défini
                delivery_id: deliveryId,  // Inclut deliveryId dans la requête
                service_id: serviceId     // Inclut serviceId dans la requête
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.message === "Réservation de créneau réussie.") {
                alert("Créneau réservé avec succès!");
                reservationForm.reset();
                loadAvailableSlots(); // Recharge les créneaux pour refléter les changements
            } else {
                alert("Erreur lors de la réservation du créneau: " + (data.message || "Message d'erreur non spécifié."));
            }
        })
        .catch(error => {
            console.error("Erreur lors de la réservation:", error);
            alert("Une erreur est survenue lors de la réservation. Veuillez réessayer.");
        });
    }
});
