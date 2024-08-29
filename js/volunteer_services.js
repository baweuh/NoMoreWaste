document.addEventListener("DOMContentLoaded", () => {
    loadServices(); // Charger les services disponibles au chargement de la page
});

function loadServices() {
    fetch('../../api/services.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('service');
        select.innerHTML = ''; // Clear existing options

        if (Array.isArray(data)) {
            data.forEach(service => {
                const option = document.createElement('option');
                option.value = service.service_id;
                option.textContent = service.name;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Erreur:', error));
}

document.getElementById('serviceForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const serviceId = document.getElementById('service').value;

    fetch('../../api/volunteer_service.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            volunteer_id: volunteerId, // Utilisation de la variable globale
            service_id: serviceId
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        // Réinitialiser le formulaire ou effectuer d'autres actions
    })
    .catch(error => console.error('Erreur:', error));
});
