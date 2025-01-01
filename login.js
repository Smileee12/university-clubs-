function validateForm() {
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  let isValid = true;

  if (!isValidEmail(email)) {
    document.getElementById("emailError").textContent = "Please enter a valid email address.";
    isValid = false;
  } else {
    document.getElementById("emailError").textContent = "";
  }

  if (password === "") {
    document.getElementById("passwordError").textContent = "Password is required.";
    isValid = false;
  } else {
    document.getElementById("passwordError").textContent = "";
  }

  return isValid;
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function handleSubmit(event) {
  event.preventDefault();

  if (validateForm()) {
    const form = event.target;
    const formData = new FormData(form);

    fetch("login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          localStorage.setItem("user", JSON.stringify(data.user));
          window.location.href = data.redirect;
        } else {
          displayError(data.message);
        }
      })
      .catch(() => {
        displayError("An error occurred. Please try again later.");
      });
  }
}

function displayError(message) {
  const errorMessage = document.getElementById("errorMessage");
  errorMessage.textContent = message;
  errorMessage.style.display = "block";
}
