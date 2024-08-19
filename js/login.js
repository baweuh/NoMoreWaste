document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  fetch("api/login.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username: username, password: password }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        switch (data.role) {
          case "admin":
            window.location.href = "template/admin/dashboard.php";
            break;
          case "clients":
            window.location.href = "template/customer/dashboard.php";
            break;
          case "commercants":
            window.location.href = "template/merchant/dashboard.php";
            break;
          case "benevoles":
            window.location.href = "template/volunteer/dashboard.php";
            break;
        }
      } else {
        document.getElementById("message").innerText = data.message;
      }
    })
    .catch((error) => console.error("Error:", error));
});
