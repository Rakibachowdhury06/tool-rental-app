<?php include __DIR__ . '/../header.php'; ?>
<main class="container">
    <h2>Sign Up</h2>

    <form id="registerForm" action="../server/create_customer.php" method="POST" novalidate>
        <!-- Username -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <span class="error" id="usernameError"></span> <!-- JS validation -->
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <span class="error" id="emailError"></span>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <span class="error" id="passwordError"></span>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <span class="error" id="confirmError"></span>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</main>
<?php include __DIR__ . '/../footer.php'; ?>