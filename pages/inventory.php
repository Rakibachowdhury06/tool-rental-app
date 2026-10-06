<?php include __DIR__ . '/../header.php'; ?>

<?php
require __DIR__ . '/../Includes/database.inc.php';
$pdo = setConnectionInfo();

$sort = "name";

// --- get filters from GET ---
$search = isset($_GET['q']) && $_GET['q'] !== '' ? "%{$_GET['q']}%" : '%';
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$location = isset($_GET['location']) && $_GET['location'] !== '' ? $_GET['location'] : null;

// --- build query with optional filters ---
$sql = "SELECT id, name, price, category, location 
        FROM view_tool
        WHERE name LIKE ?";
$params = [$search];

if ($category !== null) {
    $sql .= " AND category_id = ?";
    $params[] = $category;
}

if ($location !== null) {
    $sql .= " AND location_id = ?";
    $params[] = $location;
}

$sql .= " ORDER BY $sort";

// --- execute ---
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tools_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;

// --- get categories ---
$sqlCat = "SELECT id, category FROM category ORDER BY category";
$stmtCat = $pdo->query($sqlCat);
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
$stmtCat = null;

// --- get locations ---
$sqlLoc = "SELECT id, location FROM location ORDER BY location";
$stmtLoc = $pdo->query($sqlLoc);
$locations = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);
$stmtLoc = null;
?>

<main class="container">
    <h2>Our Tool Inventory</h2>

    <!-- Filters form -->
    <form class="inventory-filters" method="get">
        <input type="text" name="q" placeholder="Search…"
            value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">

        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['id']) ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="location">
            <option value="">All Locations</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= htmlspecialchars($loc['id']) ?>" <?= ($location == $loc['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($loc['location']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-primary">Filter</button>
    </form>

    <!-- Inventory table -->
    <table class="inventory-table">
        <thead>
            <tr>
                <th>Tool</th>
                <th>Category</th>
                <th>Location</th>
                <th>Price/Day</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tools_raw): ?>
                <?php foreach ($tools_raw as $tool): ?>
                    <tr>
                        <td><?= htmlspecialchars($tool['name']) ?></td>
                        <td><?= htmlspecialchars($tool['category']) ?></td>
                        <td><?= htmlspecialchars($tool['location']) ?></td>
                        <td>$<?= number_format($tool['price'], 2) ?></td>
                        <td><a href="rent_orders.php?tool_id=<?= $tool['id'] ?>" class="btn btn-secondary">Rent Now</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No tools found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include __DIR__ . '/../footer.php'; ?>

</body>

</html>