document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const merchantId = document.getElementById('merchantId');
    
    // Fonction pour charger les informations du commerçant
    async function loadProfile() {
        try {
            // Envoie une requête GET à l'API pour obtenir les informations du commerçant connecté
            const response = await fetch('../../api/merchant_profil.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            if (data.message) {
                alert(data.message);
            } else {
                // Remplit le formulaire avec les informations du commerçant
                document.getElementById('name').value = data.name || '';
                document.getElementById('address').value = data.address || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('membershipStartDate').value = data.membership_start_date || '';
                document.getElementById('membershipEndDate').value = data.membership_end_date || '';
                merchantId.value = data.merchant_id || '';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Fonction pour mettre à jour les informations du commerçant
    async function updateProfile(event) {
        event.preventDefault();
        
        const formData = new FormData(profileForm);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        try {
            const response = await fetch('../../api/merchant_profil.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error('Network response was not ok');
            
            const result = await response.json();
            alert(result.message);
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Charger les informations du commerçant au chargement de la page
    loadProfile();
    
    // Ajouter un écouteur d'événement pour le formulaire
    profileForm.addEventListener('submit', updateProfile);
});
