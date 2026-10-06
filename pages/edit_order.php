<?php
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

require __DIR__ . '/../Includes/database.inc.php';
$pdo = setConnectionInfo();

include __DIR__ . '/../header.php';

// ---- get id ----
$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($orderId <= 0) {
    echo "<main class='container'><p>Invalid order id.</p></main>";
    include __DIR__ . '/../footer.php';
    exit;
}

// ---- load order ----
$sql = "SELECT id, customer_id, tool_id, qty, startdate, enddate, total
        FROM orders WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<main class='container'><p>Order not found.</p></main>";
    include __DIR__ . '/../footer.php';
    exit;
}

// ---- load dropdown data ----
$customers = $pdo->query("SELECT id, first_name, last_name, email FROM customers ORDER BY last_name, first_name")
    ->fetchAll(PDO::FETCH_ASSOC);

$tools = $pdo->query("SELECT id, name, price FROM tool ORDER BY name")
    ->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$notice = "";

// ---- handle update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    $customerId = isset($_POST['customerID']) ? (int) $_POST['customerID'] : 0;
    $toolId = isset($_POST['inventoryID']) ? (int) $_POST['inventoryID'] : 0;
    $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';

    // validate
    if ($customerId <= 0)
        $errors[] = "Please select a customer.";
    if ($toolId <= 0)
        $errors[] = "Please select a tool.";
    if ($qty < 1)
        $errors[] = "Quantity must be at least 1.";
    if (!$startDate || !$endDate)
        $errors[] = "Start and end dates are required.";

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

    // compute total and update
    if (empty($errors)) {
        $p = $pdo->prepare("SELECT price FROM tool WHERE id = :id");
        $p->execute([':id' => $toolId]);
        $priceRow = $p->fetch(PDO::FETCH_ASSOC);
        if (!$priceRow) {
            $errors[] = "Selected tool not found.";
        } else {
            $price = (float) $priceRow['price'];
            $total = $price * $qty * $days;

            $upd = $pdo->prepare(
                "UPDATE orders
                 SET customer_id = :customer_id,
                     tool_id     = :tool_id,
                     qty         = :qty,
                     startdate   = :startdate,
                     enddate     = :enddate,
                     total       = :total
                 WHERE id = :id"
            );
            $upd->execute([
                ':customer_id' => $customerId,
                ':tool_id' => $toolId,
                ':qty' => $qty,
                ':startdate' => $startDate,
                ':enddate' => $endDate,
                ':total' => $total,
                ':id' => $orderId,
            ]);

            // redirect back to list with success
            $back = dirname($_SERVER['PHP_SELF']) . '/rent_orders.php?updated=1';
            header("Location: $back");
            exit;
        }
    }

    // if errors, repopulate the form fields with current POST
    $order['customer_id'] = $customerId;
    $order['tool_id'] = $toolId;
    $order['qty'] = $qty;
    $order['startdate'] = $startDate;
    $order['enddate'] = $endDate;
}
?>

<main class="container">
    <h2>Edit Order #<?= (int) $order['id'] ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul><?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="edit-order-form" method="POST" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <div class="form-group">
            <label for="customerID">Customer</label>
            <select id="customerID" name="customerID" required>
                <option value="">– Select Customer –</option>
                <?php foreach ($customers as $c): ?>
                    <?php
                    $cid = (int) $c['id'];
                    $name = htmlspecialchars($c['first_name'] . ' ' . $c['last_name'], ENT_QUOTES, 'UTF-8');
                    $mail = htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8');
                    $sel = ($order['customer_id'] == $cid) ? ' selected' : '';
                    ?>
                    <option value="<?= $cid ?>" <?= $sel ?>><?= $name ?> (<?= $mail ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="inventoryID">Tool</label>
            <select id="inventoryID" name="inventoryID" required>
                <option value="">– Select Tool –</option>
                <?php foreach ($tools as $t): ?>
                    <?php
                    $tid = (int) $t['id'];
                    $tname = htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8');
                    $price = number_format((float) $t['price'], 2);
                    $sel = ($order['tool_id'] == $tid) ? ' selected' : '';
                    ?>
                    <option value="<?= $tid ?>" <?= $sel ?>><?= $tname ?> — $<?= $price ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" value="<?= (int) $order['qty'] ?>" required>
        </div>

        <div class="form-group">
            <label for="startDate">Start Date</label>
            <input type="date" id="startDate" name="startDate" value="<?= htmlspecialchars($order['startdate']) ?>"
                required>
        </div>

        <div class="form-group">
            <label for="endDate">End Date</label>
            <input type="date" id="endDate" name="endDate" value="<?= htmlspecialchars($order['enddate']) ?>" required>
        </div>

        <!-- Total Cost  -->
        <div class="form-group">
            <label>Total Cost</label>
            <div id="totalCost">$<?= number_format((float) $order['total'], 2) ?></div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a class="btn" href="rent_orders.php">Cancel</a>
        </div>
    </form>
</main>

<?php include __DIR__ . '/../footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toolSelect = document.getElementById("inventoryID");
        const qtyInput = document.getElementById("quantity");
        const startDate = document.getElementById("startDate");
        const endDate = document.getElementById("endDate");
        const totalEl = document.getElementById("totalCost");

        // PHP → JS: tool id → price
        const toolPrices = {
            <?php foreach ($tools as $t): ?>
              <?= (int) $t['id'] ?>: <?= (float) $t['price'] ?>,
            <?php endforeach; ?>
        };

        function calcDays(s, e) {
            if (!s || !e) return 0;
            const start = new Date(s);
            const end = new Date(e);
            if (isNaN(start) || isNaN(end)) return 0;
            const diff = end - start;
            if (diff < 0) return 0;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            return Math.max(1, days); // at least 1 day
        }

        function renderTotal() {
            const id = parseInt(toolSelect.value, 10);
            const qty = parseInt(qtyInput.value, 10) || 0;
            const days = calcDays(startDate.value, endDate.value);
            const price = toolPrices[id] || 0;
            const total = price * qty * days;
            totalEl.textContent = `$${total.toFixed(2)}`;
        }

        [toolSelect, qtyInput, startDate, endDate].forEach(el => {
            if (el) el.addEventListener("input", renderTotal);
        });

        renderTotal();
    });
</script>

</body>

</html>