
<?php include __DIR__ . '/../header.php'; ?>


<main class="container">
  <h2>Login</h2>

  <form id="loginForm" action="../server/login_customer.php" method="POST" novalidate>
    <!-- Email -->
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />
      <span class="error" id="emailError"></span>
    </div>

    <!-- Password -->
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />
      <span class="error" id="passwordError"></span>
    </div>

    <!-- Show/hide toggle -->
    <div class="form-group">
      <input type="checkbox" id="showPassword" />
      <label for="showPassword">Show Password</label>
    </div>

    <button type="submit" class="btn btn-primary">Login</button>
  </form>

  <p style="margin-top: 1rem;">
    Don’t have an account?
    <a href="signup.php">Sign up here</a>.
  </p>
</main>

<?php include __DIR__ . '/../footer.php'; ?>

<!-- Only include once -->
<script src="../scripts/login-validation.js"></script>

<!-- Toggle password visibility -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("showPassword");
  const pwd = document.getElementById("password");
  if (toggle && pwd) {
    toggle.addEventListener("change", () => {
      pwd.type = toggle.checked ? "text" : "password";
    });
  }
});
</script>
</body>
</html>
