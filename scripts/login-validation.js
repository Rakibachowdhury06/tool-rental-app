
document.addEventListener("DOMContentLoaded", () => {
  console.log("login-validation.js is running");

  const form = document.getElementById("loginForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    const email = document.getElementById("email");
    const password = document.getElementById("password");

    // Clear previous error messages
    document.getElementById("emailError").textContent = "";
    document.getElementById("passwordError").textContent = "";

    // Reset styles
    email.classList.remove("invalid", "valid");
    password.classList.remove("invalid", "valid");

    // Validate email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) {
      document.getElementById("emailError").textContent = "Email is required.";
      email.classList.add("invalid");
      isValid = false;
    } else if (!emailPattern.test(email.value.trim())) {
      document.getElementById("emailError").textContent = "Enter a valid email address.";
      email.classList.add("invalid");
      isValid = false;
    } else {
      email.classList.add("valid");
    }

    // Validate password
    if (!password.value.trim()) {
      document.getElementById("passwordError").textContent = "Password is required.";
      password.classList.add("invalid");
      isValid = false;
    } else {
      password.classList.add("valid");
    }

    if (!isValid) {
      e.preventDefault();
      console.log("Login form validation failed.");
    }
  });
});
