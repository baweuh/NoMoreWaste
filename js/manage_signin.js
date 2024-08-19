document.addEventListener("DOMContentLoaded", function () {
  const profileForm = document.getElementById("profileForm");
  const usernameField = document.getElementById("username");
  const roleField = document.getElementById("role");

  function fetchProfile() {
    fetch("../../api/user_profile.php", {
      method: "GET",    
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.user_id) {
          usernameField.value = data.username;
          roleField.value = data.role;
        } else {
          alert("Erreur lors de la récupération des informations.");
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  profileForm.addEventListener("submit", function (event) {
    event.preventDefault();
    const updatedProfile = {
      username: usernameField.value,
      role: roleField.value,
    };

    fetch("../../api/user_profile.php", {
      method: "PUT",
      body: JSON.stringify(updatedProfile),
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.message === "User was updated.") {
          alert("Profil mis à jour avec succès.");
        } else {
          alert("Erreur lors de la mise à jour du profil.");
        }
      })
      .catch((error) => console.error("Error:", error));
  });

  fetchProfile();
});
