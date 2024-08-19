document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = "../../api/reservation.php";

  completeAddress.addEventListener("submit", (e) => {
    e.preventDefault();

    const address = document.getElementById("address").value;
    const zipcode = document.getElementById("zipcode").value;
    const city = document.getElementById("city").value;

    fetch(apiUrl, {
      method: "POST",
      header: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        address,
        zipcode,
        city,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        //window.location.href = "recapitulatif.php";
      });
  });
});
