document.addEventListener('DOMContentLoaded', function() {
    const collectsTableBody = document.getElementById('collectsTableBody');
    const addCollectForm = document.getElementById('addCollectForm');
    const editCollectForm = document.getElementById('editCollectForm');
    const editSection = document.getElementById('editSection');

    // Fonction pour charger les collectes
    function loadCollects() {
        fetch('../../api/merchant_collection.php')
            .then(response => response.json())
            .then(data => {
                collectsTableBody.innerHTML = '';
                data.forEach(collect => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${collect.collection_id}</td>
                        <td>${collect.name}</td>
                        <td>${collect.collection_date}</td>
                        <td>${collect.total_items}</td>
                        <td>
                            <button class="editButton" data-id="${collect.collection_id}">Modifier</button>
                            <button class="deleteButton" data-id="${collect.collection_id}">Supprimer</button>
                        </td>
                    `;
                    collectsTableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching collections:', error));
    }

    // Fonction pour ajouter une collecte
    addCollectForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(addCollectForm);
        const data = {
            name: formData.get('productName'),
            collection_date: formData.get('collectDate'),
            total_items: formData.get('quantity')
        };

        console.log('Add data:', data); // Debug log

        fetch('../../api/merchant_collection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Add response:', data); // Debug log
            alert(data.message);
            loadCollects();
            addCollectForm.reset();
        })
        .catch(error => console.error('Error adding collection:', error));
    });

    // Fonction pour modifier une collecte
    editCollectForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(editCollectForm);
        const data = {
            collection_id: formData.get('collectId'),
            name: formData.get('productName'),
            collection_date: formData.get('collectDate'),
            total_items: formData.get('quantity')
        };

        console.log('Edit data:', data); // Debug log

        fetch('../../api/merchant_collection.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Edit response:', data); // Debug log
            alert(data.message);
            loadCollects();
            editCollectForm.reset();
            editSection.style.display = 'none'; // Masquer le formulaire d'édition après la mise à jour
        })
        .catch(error => console.error('Error editing collection:', error));
    });

    // Fonction pour remplir le formulaire d'édition et afficher le formulaire
    collectsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('editButton')) {
            const collectId = e.target.dataset.id;
            fetch(`../../api/merchant_collection.php?id=${collectId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editCollectId').value = data.collection_id;
                    document.getElementById('editProductName').value = data.name;
                    document.getElementById('editCollectDate').value = data.collection_date;
                    document.getElementById('editQuantity').value = data.total_items;
                    editSection.style.display = 'block'; // Afficher le formulaire d'édition
                })
                .catch(error => console.error('Error fetching collection:', error));
        } else if (e.target.classList.contains('deleteButton')) {
            const collectId = e.target.dataset.id;
            if (confirm('Voulez-vous vraiment supprimer cette collecte?')) {
                fetch(`../../api/merchant_collection.php?id=${collectId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Delete response:', data); // Debug log
                    alert(data.message);
                    loadCollects();
                })
                .catch(error => console.error('Error deleting collection:', error));
            }
        }
    });

    // Charger les collectes lors du chargement de la page
    loadCollects();
});
