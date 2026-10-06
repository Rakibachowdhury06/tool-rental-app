<?php
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

include __DIR__ . '/../header.php';

require __DIR__ . '/../Includes/database.inc.php';
$pdo = setConnectionInfo();

// Re-use selected values if validation fails
$selectedCustomerId = isset($_POST['customerID']) ? (int) $_POST['customerID'] : 0;
$selectedToolId = isset($_POST['inventoryID']) ? (int) $_POST['inventoryID'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';

$errors = [];
$notice = "";

/* ---------- Handle Delete (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $url = strtok($_SERVER['REQUEST_URI'], '?'); // for redirects
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        header("Location: {$url}?error=csrf");
        exit;
    }
    $deleteId = (int) $_POST['delete_id'];
    if ($deleteId > 0) {
        try {
            $del = $pdo->prepare("DELETE FROM orders WHERE id = :id");
            $del->execute([':id' => $deleteId]);
            header("Location: {$url}?deleted=1");
            exit;
        } catch (PDOException $e) {
            // e.g., FK constraint errors
            header("Location: {$url}?error=delete");
            exit;
        }
    } else {
        header("Location: {$url}?error=id");
        exit;
    }
}

/* ---------- Handle Create (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    // Validate
    if ($selectedCustomerId <= 0)
        $errors[] = "Please select a customer.";
    if ($selectedToolId <= 0)
        $errors[] = "Please select a tool.";
    if ($quantity < 1)
        $errors[] = "Quantity must be at least 1.";
    if (!$startDate || !$endDate)
        $errors[] = "Start and end dates are required.";

    // Date validation
    $days = 0;
    if ($startDate && $endDate) {
        try {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            if ($end < $start) {
                $errors[] = "End date cannot be before start date.";
            } else {
                $days = (int) $start->diff($end)->days;
                if ($days < 1)
                    $days = 1;
            }
        } catch (Exception $e) {
            $errors[] = "Invalid date format.";
        }
    }

    // If valid, compute total and insert
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT price FROM tool WHERE id = :id");
        $stmt->execute([':id' => $selectedToolId]);
        $priceRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$priceRow) {
            $errors[] = "Selected tool not found.";
        } else {
            $price = (float) $priceRow['price'];
            $total = $price * $quantity * $days;

            $ins = $pdo->prepare(
                "INSERT INTO orders (customer_id, tool_id, qty, startdate, enddate, total)
                 VALUES (:customer_id, :tool_id, :qty, :startdate, :enddate, :total)"
            );
            $ins->execute([
                ':customer_id' => $selectedCustomerId,
                ':tool_id' => $selectedToolId,
                ':qty' => $quantity,
                ':startdate' => $startDate,
                ':enddate' => $endDate,
                ':total' => $total,
            ]);

            // PRG redirect back to this page to avoid resubmission
            $url = strtok($_SERVER['REQUEST_URI'], '?');
            header("Location: {$url}?added=1");
            exit;
        }
    }
}

// Notices after redirects
if (isset($_GET['added'])) {
    $notice = "Order placed successfully.";
}
if (isset($_GET['deleted'])) {
    $notice = "Order deleted.";
}
if (isset($_GET['error'])) {
    $errors[] = match ($_GET['error']) {
        'csrf' => 'Security check failed. Please try again.',
        'delete' => 'Could not delete the order.',
        'id' => 'Invalid order id.',
        default => 'An error occurred.',
    };
}

$selectedCustomerId = isset($_POST['customerID']) ? (int) $_POST['customerID'] : 0;
$selectedToolId = isset($_POST['inventoryID']) ? (int) $_POST['inventoryID'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';

if ($selectedToolId === 0) {
    if (isset($_GET['tool_id'])) {
        $selectedToolId = (int) $_GET['tool_id'];
    }
}

/* ---------- Load dropdown data ---------- */
$stmt = $pdo->query("SELECT id, first_name, last_name, email FROM customers ORDER BY last_name, first_name");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query("SELECT id, name, price FROM tool ORDER BY name");
$tools = $stmt2->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Load current orders list ---------- */
$sqlOrders = "SELECT 
                o.id AS order_id,
                c.first_name,
                c.last_name,
                t.name AS tool_name,
                o.qty,
                o.startdate,
                o.enddate,
                o.total
              FROM orders o
              JOIN customers c ON o.customer_id = c.id
              JOIN tool t ON o.tool_id = t.id
              ORDER BY o.startdate DESC";
$stmt3 = $pdo->query($sqlOrders);
$orders = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
    <h2>Rent Orders</h2>

    <?php if (!empty($notice)): ?>
        <div class="alert success"><?= htmlspecialchars($notice) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- New Order Form -->
    <form id="orderForm" action="" method="POST" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <!-- Customer Select -->
        <div class="form-group">
            <label for="customerID">Customer</label>
            <select id="customerID" name="customerID" required>
                <option value="">– Select Customer –</option>
                <?php foreach ($customers as $c): ?>
                    <?php
                    $cid = (int) $c['id'];
                    $name = htmlspecialchars($c['first_name'] . ' ' . $c['last_name'], ENT_QUOTES, 'UTF-8');
                    $mail = htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8');
                    $sel = ($selectedCustomerId === $cid) ? ' selected' : '';
                    ?>
                    <option value="<?= $cid ?>" <?= $sel ?>><?= $name ?> (<?= $mail ?>)</option>
                <?php endforeach; ?>
            </select>
            <span class="error" id="customerError"></span>
        </div>

        <!-- Tool Select -->
        <div class="form-group">
            <label for="inventoryID">Tool</label>
            <select id="inventoryID" name="inventoryID" required>
                <option value="">– Select Tool –</option>
                <?php foreach ($tools as $t): ?>
                    <?php
                    $tid = (int) $t['id'];
                    $tname = htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8');
                    $price = number_format((float) $t['price'], 2);
                    $sel = ($selectedToolId === $tid) ? ' selected' : '';
                    ?>
                    <option value="<?= $tid ?>" <?= $sel ?>><?= $tname ?> — $<?= $price ?></option>
                <?php endforeach; ?>
            </select>
            <span class="error" id="toolError"></span>
        </div>

        <!-- Quantity -->
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1"
                value="<?= htmlspecialchars((string) $quantity) ?>" required>
            <span class="error" id="quantityError"></span>
        </div>

        <!-- Start Date -->
        <div class="form-group">
            <label for="startDate">Start Date</label>
            <input type="date" id="startDate" name="startDate" value="<?= htmlspecialchars($startDate) ?>" required>
            <span class="error" id="startDateError"></span>
        </div>

        <!-- End Date -->
        <div class="form-group">
            <label for="endDate">End Date</label>
            <input type="date" id="endDate" name="endDate" value="<?= htmlspecialchars($endDate) ?>" required>
            <span class="error" id="endDateError"></span>
        </div>

        <!-- Total Cost (client-side calc optional; server is source of truth) -->
        <div class="form-group">
            <label>Total Cost</label>
            <div id="totalCost">$0.00</div>
        </div>

        <button type="submit" class="btn btn-primary">Place Order</button>
    </form>

    <hr>

    <!-- Existing Orders List -->
    <h3>Current Rentals</h3>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Tool</th>
                <th>Qty</th>
                <th>Start</th>
                <th>End</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= (int) $o['order_id'] ?></td>
                        <td><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></td>
                        <td><?= htmlspecialchars($o['tool_name']) ?></td>
                        <td><?= (int) $o['qty'] ?></td>
                        <td><?= htmlspecialchars($o['startdate']) ?></td>
                        <td><?= htmlspecialchars($o['enddate']) ?></td>
                        <td>$<?= number_format((float) $o['total'], 2) ?></td>
                        <td>
                            <a href="edit_order.php?id=<?= (int) $o['order_id'] ?>" class="btn btn-secondary btn-sm">Edit</a> |
                            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this order?');">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                <input type="hidden" name="delete_id" value="<?= (int) $o['order_id'] ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">(No current rentals found)</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include __DIR__ . '/../footer.php'; ?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toolSelect = document.getElementById("inventoryID");
        const qtyInput = document.getElementById("quantity");
        const startDate = document.getElementById("startDate");
        const endDate = document.getElementById("endDate");
        const totalDisplay = document.getElementById("totalCost");

        // Map tool ID → price from PHP array
        const toolPrices = {
            <?php foreach ($tools as $t): ?>
                <?= (int) $t['id'] ?>: <?= (float) $t['price'] ?>,
            <?php endforeach; ?>
        };

        function calculateTotal() {
            const toolId = parseInt(toolSelect.value, 10);
            const price = toolPrices[toolId] || 0;
            const qty = parseInt(qtyInput.value, 10) || 0;

            let days = 0;
            const start = startDate.value ? new Date(startDate.value) : null;
            const end = endDate.value ? new Date(endDate.value) : null;
            if (start && end && !isNaN(start) && !isNaN(end)) {
                const diffMs = end - start;
                if (diffMs >= 0) {
                    days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                    if (days < 1) days = 1; // at least 1 day
                }
            }

            const total = price * qty * days;
            totalDisplay.textContent = `$${total.toFixed(2)}`;
        }

        [toolSelect, qtyInput, startDate, endDate].forEach(el => {
            el.addEventListener("input", calculateTotal);
        });
    });
</script>

</body>

</html>