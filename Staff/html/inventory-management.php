<?php
$staffBackendAction = 'inventory';
require 'staff_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Inventory Management</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Admin/css/homepage.css">
    <link rel="stylesheet" href="../css/staff.css">
    <script src="../js/staff-panels.js" defer></script>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <a class="brand" href="staff.php"><span class="brand-logo">A</span><span>Aquatica</span></a>
            <p class="menu-title">MAIN MENU</p>
            <nav class="nav flex-column" aria-label="Main navigation">
                <a class="nav-link" href="staff.php">Dashboard</a>
                <a class="nav-link" href="fish-catalog.php">Fish Catalog</a>
                <a class="nav-link active" href="inventory-management.php" aria-current="page">Inventory</a>
                <a class="nav-link" href="transaction.php">Billing</a>
            </nav>
            <a class="sidebar-logout" href="../../login_page/html/login.php">Logout</a>
            <div class="sidebar-workspace"><span class="sidebar-status-dot" aria-hidden="true"></span><span><strong>Nasugbu
                        Aquatica</strong><small>Staff workspace</small></span></div>
        </aside>

        <main class="main-content staff-content">
            <div class="staff-page-top">
                <nav aria-label="Breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="staff.php">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Inventory Management</li></ol></nav>
                <span class="staff-location"><span aria-hidden="true"></span>Nasugbu Aquatica</span>
            </div>
            <header class="staff-heading">
                <div><p class="welcome-label">STOCK REFERENCE</p><h1>Inventory Management</h1><p class="page-subtitle">View current stock and pricing. Staff access is read-only.</p></div>
                <span class="inventory-readonly-badge">&#128065; View only</span>
            </header>

            <section class="inventory-panel" aria-labelledby="inventory-list-title">
                <div class="inventory-panel-header">
                    <div>
                        <h2 id="inventory-list-title">Current inventory</h2>
                        <p>Search by fish name or stock status.</p>
                        <small class="text-secondary"><?= (int) $inventoryResult->num_rows ?> products</small>
                    </div>
                    <div class="input-group inventory-search">
                        <input class="form-control" type="search" placeholder="Search inventory" aria-label="Search inventory" data-search-for="staff-inventory-rows">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table inventory-table align-middle" id="inventory-items">
                        <thead>
                            <tr>
                                <th scope="col">Product Name</th>
                                <th scope="col">Stock quantity</th>
                                <th scope="col">Price each</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody id="staff-inventory-rows">
                            <?php if ($inventoryResult->num_rows === 0): ?>
                                <tr data-search-empty><td colspan="4" class="text-center text-muted py-4">No inventory items found.</td></tr>
                            <?php else: ?>
                                <?php while ($item = $inventoryResult->fetch_assoc()): ?>
                                    <?php $quantity = (int) $item['quantity']; ?>
                                    <tr data-search-item>
                                        <td class="inventory-item-name"><?= htmlspecialchars($item['product'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= $quantity ?></td>
                                        <td>&#8369;<?= number_format((float) $item['cost_per_piece'], 2) ?></td>
                                        <td><span class="inventory-stock-badge <?= $quantity <= 10 ? 'low' : '' ?>"><?= $quantity <= 10 ? 'Low stock' : 'In stock' ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr data-search-empty hidden><td colspan="4" class="text-center text-muted py-4">No matching inventory items found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

