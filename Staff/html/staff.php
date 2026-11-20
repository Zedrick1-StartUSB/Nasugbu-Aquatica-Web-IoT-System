<?php
$staffBackendAction = 'dashboard';
require 'staff_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Staff Dashboard</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Admin/css/homepage.css">
    <link rel="stylesheet" href="../../Admin/css/device.css">
    <link rel="stylesheet" href="../css/staff.css">
</head>
<body class="staff-dashboard-page">
    <div class="dashboard">
        <aside class="sidebar">
            <a class="brand" href="staff.php"><span class="brand-logo">A</span><span>Aquatica</span></a>
            <p class="menu-title">MAIN MENU</p>
            <nav class="nav flex-column" aria-label="Main navigation">
                <a class="nav-link active" href="staff.php" aria-current="page">Dashboard</a>
                <a class="nav-link" href="fish-catalog.php">Fish Catalog</a>
                <a class="nav-link" href="inventory-management.php">Inventory</a>
                <a class="nav-link" href="transaction.php">Billing</a>
            </nav>
            <a class="sidebar-logout" href="../../login_page/logout.php">Logout</a>
            <div class="sidebar-workspace"><span class="sidebar-status-dot" aria-hidden="true"></span><span><strong>Nasugbu
                        Aquatica</strong><small>Staff workspace</small></span></div>
        </aside>
    
        <main class="main-content staff-content staff-dashboard-content">
            <div class="staff-page-top">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
                <span class="staff-location"><span aria-hidden="true"></span>Nasugbu Aquatica</span>
            </div>
    
            <header class="staff-heading">
                <div>
                    <p class="welcome-label">STAFF WORKSPACE</p>
                    <h1>Dashboard</h1>
                    <p class="page-subtitle">Quick access to fish, inventory, and transaction tools.</p>
                </div>
                <a class="btn btn-success staff-primary-action" href="transaction.php">Start a transaction</a>
            </header>
    
            <section class="row g-3 g-xl-4 monitoring-cards" aria-label="Water and room measurements">
                <div class="col-12 col-md-4">
                    <article class="monitor-card ph-card card border-0 h-100">
                        <div class="monitor-top">
                            <div class="monitor-icon" aria-hidden="true">pH</div>
                            <span class="reading-status status-good"><span></span> Normal</span>
                        </div>
                        <p class="monitor-label">Water pH</p>
                        <div class="reading-value">7.2 <span>pH</span></div>
                        <p class="reading-description">Water acidity level</p>
                        <div class="measurement-note"><span class="measurement-note-icon"
                                aria-hidden="true">&#8596;</span><span><small>Reference range</small><strong>6.5 &ndash; 8.0 pH</strong></span>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-md-4">
                    <article class="monitor-card humidity-card card border-0 h-100">
                        <div class="monitor-top">
                            <div class="monitor-icon" aria-hidden="true">%</div>
                            <span class="reading-status status-good"><span></span> Normal</span>
                        </div>
                        <p class="monitor-label">Humidity</p>
                        <div class="reading-value">62 <span>%</span></div>
                        <p class="reading-description">Air humidity near the aquarium</p>
                        <div class="measurement-note"><span class="measurement-note-icon" aria-hidden="true">&#9678;</span><span><small>Reading type</small><strong>Ambient air</strong></span></div>
                    </article>
                </div>

                <div class="col-12 col-md-4">
                    <article class="monitor-card temperature-card card border-0 h-100">
                        <div class="monitor-top">
                            <div class="monitor-icon" aria-hidden="true">&#176;C</div>
                            <span class="reading-status status-good"><span></span> Normal</span>
                        </div>
                        <p class="monitor-label">Water temperature</p>
                        <div class="reading-value">26.4 <span>&#176;C</span></div>
                        <p class="reading-description">Current aquarium temperature</p>
                        <div class="measurement-note"><span class="measurement-note-icon" aria-hidden="true">&#8596;</span><span><small>Example range</small><strong>24 &ndash; 28 &#176;C</strong></span></div>
                    </article>
                </div>
            </section>
    
            <section class="row g-3 mt-1" aria-label="Recent transactions">
                <div class="col-12">
                    <div class="staff-panel">
                        <div class="staff-panel-heading">
                            <div>
                                <h2>Recent transactions</h2>
                                <p>Latest activity recorded by the team</p>
                            </div><a class="staff-link" href="transaction.php">Open transaction processing &#8594;</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Transaction number</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Products bought</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($recentTransactionsResult) && $recentTransactionsResult->num_rows > 0): ?>
                                        <?php while ($recentTransaction = $recentTransactionsResult->fetch_assoc()): ?>
                                            <?php $dashboardProducts = json_decode($recentTransaction['product_bought'], true); ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string) $recentTransaction['transaction_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($recentTransaction['customer'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <?php if (is_array($dashboardProducts)): ?>
                                                        <?php foreach ($dashboardProducts as $dashboardProduct => $dashboardItem): ?>
                                                            <?php if (is_array($dashboardItem) && isset($dashboardItem['product'], $dashboardItem['quantity'])): ?>
                                                                <div><?= htmlspecialchars((string) $dashboardItem['product'], ENT_QUOTES, 'UTF-8') ?> &times; <?= (int) $dashboardItem['quantity'] ?></div>
                                                            <?php else: ?>
                                                                <div><?= htmlspecialchars((string) $dashboardProduct, ENT_QUOTES, 'UTF-8') ?> &times; <?= (int) $dashboardItem ?></div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($recentTransaction['product_bought'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= (int) $recentTransaction['total_quantity'] ?></td>
                                                <td>&#8369;<?= number_format((float) $recentTransaction['total_paid'], 2) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-secondary py-4">No transaction activity to show. Completed transactions will appear here.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

