document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("signup-form");
  const fullName = document.getElementById("full-name");
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm-password");
  const userType = document.getElementById("user-type");

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    if (validateForm()) {
      form.submit();
    }
  });

  function validateForm() {
    let isValid = true;

    // Validate Full Name
    if (fullName.value.trim() === "") {
      setError(fullName, "Full name is required");
      isValid = false;
    } else {
      setSuccess(fullName);
    }

    // Validate Email
    if (email.value.trim() === "") {
      setError(email, "Email is required");
      isValid = false;
    } else if (!isValidEmail(email.value.trim())) {
      setError(email, "Provide a valid email address");
      isValid = false;
    } else {
      setSuccess(email);
    }

    // Validate Password
    if (password.value.trim() === "") {
      setError(password, "Password is required");
      isValid = false;
    } else if (password.value.trim().length < 8) {
      setError(password, "Password must be at least 8 characters");
      isValid = false;
    } else {
      setSuccess(password);
    }

    // Validate Confirm Password
    if (confirmPassword.value.trim() === "") {
      setError(confirmPassword, "Please confirm your password");
      isValid = false;
    } else if (confirmPassword.value !== password.value) {
      setError(confirmPassword, "Passwords do not match");
      isValid = false;
    } else {
      setSuccess(confirmPassword);
    }

    // Validate User Type
    if (userType.value === "") {
      setError(userType, "Please select a user type");
      isValid = false;
    } else {
      setSuccess(userType);
    }

    return isValid;
  }

  function setError(element, message) {
    const inputGroup = element.closest(".input-group");
    const errorDisplay = inputGroup.querySelector(".error-message");

    errorDisplay.innerText = message;
    inputGroup.classList.add("error");
    inputGroup.classList.remove("success");
  }

  function setSuccess(element) {
    const inputGroup = element.closest(".input-group");
    const errorDisplay = inputGroup.querySelector(".error-message");

    errorDisplay.innerText = "";
    inputGroup.classList.add("success");
    inputGroup.classList.remove("error");
  }

  function isValidEmail(email) {
    const re =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
});

console.log("Updated JavaScript validation code loaded successfully.");
