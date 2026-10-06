<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up - Tool Rental Co.</title>
  <link rel="stylesheet" href="/assignment2/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="/assignment2/scripts/validation.js" defer></script>
</head>

<?php
$pageClass = basename($_SERVER['PHP_SELF'], '.php') . '-page';
?>

<body class="<?php echo $pageClass; ?>">

    <header>
        <div class="container">
            <h1 class="logo">Tool Rental Co.</h1>
            <nav>
                <ul class="nav-list">
                    <li><a href="/assignment2/index.php">Home</a></li>
                    <li><a href="/assignment2/pages/inventory.php">Inventory</a></li>
                    <li><a href="/assignment2/pages/rent_orders.php">Rent Orders</a></li>
                    <li><a href="/assignment2/pages/reviews.php">Testimonials</a></li>
                    <li><a href="/assignment2/pages/terms.php">Terms &amp; Privacy</a></li>
                    <li><a href="/assignment2/pages/signup.php">Sign Up</a></li>
                    <li><a href="/assignment2/pages/login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>