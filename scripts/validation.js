
document.addEventListener("DOMContentLoaded", () => {
  console.log("validation.js is running");

  const form = document.getElementById("signupForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    const username = document.getElementById("username");
    const email    = document.getElementById("email");
    const password = document.getElementById("password");
    const confirm  = document.getElementById("confirmPassword");

    // Clear previous errors
    ["usernameError", "emailError", "passwordError", "confirmError"].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = "";
    });

    // Reset input borders
    [username, email, password, confirm].forEach(input => {
      if (input) input.classList.remove("invalid", "valid");
    });

    // Username validation
    if (username && username.value.trim() === "") {
      document.getElementById("usernameError").textContent = "Username is required.";
      username.classList.add("invalid");
      isValid = false;
    } else if (username) {
      username.classList.add("valid");
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailPattern.test(email.value.trim())) {
      document.getElementById("emailError").textContent = "Enter a valid email.";
      email.classList.add("invalid");
      isValid = false;
    } else if (email) {
      email.classList.add("valid");
    }

    // Password validation
    const pwdPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;
    if (password && !pwdPattern.test(password.value)) {
      document.getElementById("passwordError").textContent =
        "Password must be 8+ chars, include uppercase, number & symbol.";
      password.classList.add("invalid");
      isValid = false;
    } else if (password) {
      password.classList.add("valid");
    }

    // Confirm password match
    if (confirm && password && confirm.value !== password.value) {
      document.getElementById("confirmError").textContent = "Passwords do not match.";
      confirm.classList.add("invalid");
      isValid = false;
    } else if (confirm) {
      confirm.classList.add("valid");
    }

    if (!isValid) {
      e.preventDefault();
      console.log("Form has errors. Submission blocked.");
    }
  });
});
