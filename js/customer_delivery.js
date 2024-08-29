document.addEventListener("DOMContentLoaded", () => {
    loadTournees(); // Charger toutes les tournées au chargement de la page
});

function loadTournees(status = null) {
    let url = '../../api/customer_deliveries.php';
    if (status !== null) {
        url += `?status=${status}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById("tourneesTableBody");
            tbody.innerHTML = ""; // Clear existing rows

            if (Array.isArray(data)) {
                data.forEach(tournee => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${tournee.delivery_id}</td>
                        <td>${tournee.delivery_date}</td>
                        <td>${tournee.recipient_type}</td>
                        <td>${statusToText(tournee.status)}</td>
                        <td><a href="../../uploads/${tournee.pdf_report_path}" target="_blank">Voir PDF</a></td>
                        <td><button onclick="cancelDelivery(${tournee.delivery_id})">Annuler</button></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="6">Aucune donnée trouvée</td></tr>`;
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function statusToText(status) {
    switch (status) {
        case '0': return 'En cours de préparation';
        case '1': return 'En livraison';
        case '2': return 'Livré';
        case '3': return 'Annulé';
        default: return 'Inconnu';
    }
}

function cancelDelivery(deliveryId) {
    if (confirm("Êtes-vous sûr de vouloir annuler cette commande ?")) {
        fetch('../../api/customer_deliveries.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'delivery_id': deliveryId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === "Delivery successfully canceled.") {
                alert("Commande annulée avec succès.");
                loadTournees(); // Recharger les tournées après annulation
            } else {
                alert("Erreur lors de l'annulation de la commande : " + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}
