<?php
include('../../includes/session.php');

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Bénévole</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclure votre CSS ici -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .card {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .card h2 {
            margin-top: 0;
        }
        .card p {
            margin: 5px 0;
        }
        .card ul {
            list-style-type: none;
            padding: 0;
        }
        .card ul li {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Bienvenue sur votre tableau de bord, <?php echo htmlspecialchars($username); ?> !</h1>

    <nav>
        <ul>
            <?php include('../../includes/menus.php'); ?>
        </ul>
    </nav>

    <!-- Section pour les tâches assignées -->
    <div class="card">
        <h2>Tâches Assignées</h2>
        <ul id="taskList">
            <li>Chargement des tâches...</li>
        </ul>
    </div>

    <!-- Section pour les événements à venir -->
    <div class="card">
        <h2>Événements à venir</h2>
        <ul id="eventList">
            <li>Chargement des événements...</li>
        </ul>
    </div>

    <!-- Section pour les messages et notifications -->
    <div class="card">
        <h2>Messages et Notifications</h2>
        <ul id="notificationList">
            <li>Chargement des messages...</li>
        </ul>
    </div>

    <!-- Section pour le résumé de l'activité -->
    <div class="card">
        <h2>Résumé de l'Activité</h2>
        <p>Vous avez participé à <span id="activityCount">0</span> activités ce mois-ci.</p>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    loadTasks();
    loadEvents();
    loadNotifications();
    loadActivitySummary();
});

function loadTasks() {
    fetch('../../api/get_tasks.php')
        .then(response => response.json())
        .then(data => {
            const taskList = document.getElementById('taskList');
            taskList.innerHTML = '';
            if (data.length > 0) {
                data.forEach(task => {
                    const li = document.createElement('li');
                    li.textContent = `${task.title} - ${task.due_date}`;
                    taskList.appendChild(li);
                });
            } else {
                taskList.innerHTML = '<li>Aucune tâche assignée.</li>';
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function loadEvents() {
    fetch('../../api/get_events.php')
        .then(response => response.json())
        .then(data => {
            const eventList = document.getElementById('eventList');
            eventList.innerHTML = '';
            if (data.length > 0) {
                data.forEach(event => {
                    const li = document.createElement('li');
                    li.textContent = `${event.name} - ${event.date}`;
                    eventList.appendChild(li);
                });
            } else {
                eventList.innerHTML = '<li>Aucun événement à venir.</li>';
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function loadNotifications() {
    fetch('../../api/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notificationList');
            notificationList.innerHTML = '';
            if (data.length > 0) {
                data.forEach(notification => {
                    const li = document.createElement('li');
                    li.textContent = `${notification.message} - ${notification.date}`;
                    notificationList.appendChild(li);
                });
            } else {
                notificationList.innerHTML = '<li>Aucun message.</li>';
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function loadActivitySummary() {
    fetch('../../api/get_activity_summary.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('activityCount').textContent = data.activity_count;
        })
        .catch(error => console.error('Erreur:', error));
}
</script>

</body>
</html>
