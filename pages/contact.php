<?php include __DIR__ . '/../header.php'; ?>

<!-- Terms Section -->
<!-- Contact Form -->
<main class="container">
  <h2>Contact Us</h2>
  <p>If you have any questions or concerns, please fill out the form below or email us directly at
    <a href="mailto:support@toolrentalco.com">support@toolrentalco.com</a>.
  </p>

  <form id="contactForm">
    <div class="form-group">
      <label for="name">Your Name</label>
      <input type="text" id="name" name="name" required />
    </div>

    <div class="form-group">
      <label for="email">Your Email</label>
      <input type="email" id="email" name="email" required />
    </div>

    <div class="form-group">
      <label for="message">Message</label>
      <textarea id="message" name="message" rows="5" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Send Message</button>
  </form>
</main>

<?php include __DIR__ . '/../footer.php'; ?>
</body>

</html>