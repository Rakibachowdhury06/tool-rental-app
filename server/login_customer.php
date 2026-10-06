<?php include __DIR__ . '/../header.php'; ?>

<?php
require __DIR__ . '/../Includes/database.inc.php';
$pdo = setConnectionInfo();

$email = isset($_POST['email']) ? trim($_POST['email']) : null;

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "<p>Invalid email.</p>";
    include __DIR__ . '/../footer.php';
    exit;
}

$sql = "SELECT id, first_name, last_name, email
        FROM customers
        WHERE email = :email";

$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $email]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
<?php if (!empty($customers)): ?>
    <?php $cust = $customers[0]; ?>
    <h2>Customer successfully logged in</h2>
    <p><strong>First name:</strong> <?= htmlspecialchars($cust['first_name']) ?></p>
    <p><strong>Last name:</strong> <?= htmlspecialchars($cust['last_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($cust['email']) ?></p>
<?php else: ?>
    <h2>Customer successfully created</h2>
<?php endif; ?>
</main>

<?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>
