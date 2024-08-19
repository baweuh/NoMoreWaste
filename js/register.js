document.getElementById('registerForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;

    fetch('api/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: username, password: password, role: role })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'index.php'; // Rediriger vers la page de connexion
        } else {
            document.getElementById('message').innerText = data.message;
        }
    })
    .catch(error => console.error('Error:', error));
});
