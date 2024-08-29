document.addEventListener('DOMContentLoaded', function() {
    const userId = userID; // ID du bénévole connecté

    // Fonction pour recharger les données
    function reloadTable() {
        fetch(`../../api/volunteer_list_delivery.php?user_id=${userId}`)
            .then(response => response.json())
            .then(responseData => {
                const data = responseData.data || [];
                const tableBody = document.querySelector('#tours-table tbody');
                tableBody.innerHTML = ''; // Vider le corps du tableau avant de le remplir

                if (Array.isArray(data)) {
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.delivery_id}</td>
                            <td>${new Date(row.delivery_date).toLocaleDateString()}</td>
                            <td>${row.start_time}</td>
                            <td>${row.end_time}</td>
                            <td>${row.address}</td>
                            <td>${row.status}</td>
                            <td>${row.volunteer_name}</td>
                            <td>${row.service_name}</td>
                            <td>
                                <button class="confirm-reception-button" data-delivery-id="${row.delivery_id}">Confirmer la récupération</button>
                                <button class="confirm-delivery-button" data-delivery-id="${row.delivery_id}">Confirmer la livraison</button>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });

                    // Gestionnaire d'événements pour confirmer la récupération du lot
                    document.querySelectorAll('.confirm-reception-button').forEach(button => {
                        button.addEventListener('click', function() {
                            const deliveryId = this.getAttribute('data-delivery-id');

                            fetch('../../api/confirm_delivery.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ delivery_id: deliveryId, user_id: userId, action: 'confirm_recovery' })
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.status === 'success') {
                                    alert('Récupération confirmée avec succès.');
                                    reloadTable(); // Recharger les données
                                } else {
                                    alert('Erreur lors de la confirmation: ' + result.message);
                                }
                            })
                            .catch(error => console.error('Erreur:', error));
                        });
                    });

                    // Gestionnaire d'événements pour confirmer la livraison du lot
                    document.querySelectorAll('.confirm-delivery-button').forEach(button => {
                        button.addEventListener('click', function() {
                            const deliveryId = this.getAttribute('data-delivery-id');

                            fetch('../../api/confirm_delivery.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ delivery_id: deliveryId, user_id: userId, action: 'confirm_delivery' })
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.status === 'success') {
                                    alert('Livraison confirmée avec succès.');
                                    reloadTable(); // Recharger les données
                                } else {
                                    alert('Erreur lors de la confirmation: ' + result.message);
                                }
                            })
                            .catch(error => console.error('Erreur:', error));
                        });
                    });
                } else {
                    console.error('Les données reçues ne sont pas un tableau');
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Charger les données initiales
    reloadTable();
});
