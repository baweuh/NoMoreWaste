<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion du Panier</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Gestion du Panier</h1>
    <table id="panier-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Client</th>
                <th>ID Produit</th>
                <th>Quantité</th>
                <th>Validation</th>
                <th>Date d'ajout</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Les données seront injectées ici par JavaScript -->
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchPanierData();
        });

        function fetchPanierData() {
            fetch('../../api/admin_panier.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Données reçues:', data); // Affichez les données reçues pour débogage
                    const tableBody = document.querySelector('#panier-table tbody');
                    tableBody.innerHTML = '';

                    if (data.status === 'success' && Array.isArray(data.data)) {
                        data.data.forEach(row => {
                            console.log('Ligne:', row); // Affichez chaque ligne pour le débogage
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                        <td>${row.panier_id !== undefined ? row.panier_id : 'N/A'}</td>
                        <td>${row.customer_id !== undefined ? row.customer_id : 'N/A'}</td>
                        <td>${row.product_id !== undefined ? row.product_id : 'N/A'}</td>
                        <td>${row.quantity !== undefined ? row.quantity : 'N/A'}</td>
                        <td>${row.validated !== undefined ? row.validated : 'N/A'}</td>
                        <td>${row.added_at ? new Date(row.added_at).toLocaleDateString() : 'N/A'}</td>
                        <td>
                            <button onclick="updatePanier(${row.panier_id})">Modifier</button>
                            <button onclick="deletePanier(${row.panier_id})">Supprimer</button>
                        </td>
                    `;
                            tableBody.appendChild(tr);
                        });
                    } else {
                        console.error('Erreur:', data.message || 'Données incorrectes.');
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        function updatePanier(panierId) {
            const quantity = prompt('Entrez la nouvelle quantité:');
            if (quantity !== null) {
                fetch('../../api/admin_panier.php', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            panier_id: panierId,
                            quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Panier mis à jour.');
                            fetchPanierData();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }

        function deletePanier(panierId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                fetch('../../api/admin_panier.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            panier_id: panierId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Panier supprimé.');
                            fetchPanierData();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        }
    </script>
</body>

</html>