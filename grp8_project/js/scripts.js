// Make the alert message disappear after 2 seconds
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    var message = document.getElementById("message");
    if (message) {
      message.style.display = "none";
    }
  }, 2000);

  // Retrieve cart data from local storage
  let localCart = localStorage.getItem("cart");
  if (localCart) {
    localCart = JSON.parse(localCart);

    // Send the local storage data to the server to update the session cart
    fetch("update_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(localCart),
    })
      .then((response) => response.text())
      .then((result) => console.log(result))
      .catch((error) => console.error("Error:", error));
  }
});
