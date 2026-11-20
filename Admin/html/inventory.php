<?php
require_once '../../global/auth.php';
if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(empty($_SESSION['username']) ? 401 : 403);
    header('Location: ../../login_page/html/login.php');
    exit('You do not have permission to access this page.');
}
$requiredRole = 'admin';
$staffBackendAction = 'inventory';
require '../../Staff/html/staff_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Inventory</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="../css/inventory.css">
    <script src="../../bootstrap_template/js/bootstrap.bundle.min.js" defer></script>
    <script src="../../Staff/js/staff-panels.js" defer></script>
</head>
<body>
<div class="dashboard">

    <!-- Sidebar -->
    <aside class="sidebar">
        <a class="brand" href="homepage.php">
            <span class="brand-logo">A</span>
            <span>Aquatica</span>
        </a>
        <p class="menu-title">MAIN MENU</p>
        <nav class="nav flex-column" aria-label="Main navigation">
            <a class="nav-link" href="homepage.php">Dashboard</a>
            <a class="nav-link active" href="inventory.php" aria-current="page">Inventory</a>
            <a class="nav-link" href="device.php">Device Monitoring</a>
            <a class="nav-link" href="#accountsModal" data-bs-toggle="modal" data-bs-target="#accountsModal">Manage accounts</a>
            <a class="nav-link create-account-nav-link" href="../../login_page/html/signup.php">Create account</a>
        </nav>
        <a class="sidebar-logout" href="../../login_page/logout.php">Logout</a>
        <div class="sidebar-workspace">
            <span class="sidebar-status-dot" aria-hidden="true"></span>
            <span><strong>Nasugbu Aquatica</strong><small>Admin workspace</small></span>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content py-4">

        <!-- Breadcrumb row -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="homepage.php" class="text-secondary text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold">Inventory</li>
                </ol>
            </nav>
            <span class="badge bg-white border text-secondary fw-normal small">&#9679; Nasugbu Aquatica</span>
        </div>

        <!-- Page Header -->
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <p class="welcome-label mb-1">INVENTORY MANAGEMENT</p>
                <h1 class="fw-bold mb-1" style="font-size:28px;">Fish inventory</h1>
                <p class="text-muted small mb-0">Monitor available fish, stock levels, and pricing.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-success btn-sm px-3 py-2" type="button"
                    data-bs-toggle="modal" data-bs-target="#inventory-modal">
                    Manage inventory
                </button>
                <button class="btn btn-success btn-sm px-3 py-2" type="button"
                    data-bs-toggle="modal" data-bs-target="#add-item-modal">
                    &#43; Add item
                </button>
            </div>
        </div>

        <?php if ($savedMessage !== ''): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars($savedMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($inventoryError !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($inventoryError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="inv-stat-card">
                    <div class="inv-stat-icon green">&#8776;</div>
                    <div>
                        <p>Fish varieties</p>
                        <strong><?= (int) $inventoryStats['varieties'] ?></strong>
                        <small>Product types</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="inv-stat-card">
                    <div class="inv-stat-icon blue">&#9638;</div>
                    <div>
                        <p>Total fish in stock</p>
                        <strong><?= (int) $inventoryStats['total_stock'] ?></strong>
                        <small>Across all varieties</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="inv-stat-card">
                    <div class="inv-stat-icon yellow">&#9888;</div>
                    <div>
                        <p>Needs attention</p>
                        <strong><?= (int) $inventoryStats['low_stock'] ?></strong>
                        <small>Low stock variety</small>
                    </div>  
                </div>
            </div>
        </div>

        <!-- Inventory Table Card -->
        <div class="inv-table-card">

            <!-- Table Header -->
            <div class="inv-table-header">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5>Available products</h5>
                        <span class="badge rounded-pill text-bg-light border">varieties</span>
                    </div>
                    <p>Current fish stock and pricing for Nasugbu Aquatica</p>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table inv-table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Type</th>
                            <th scope="col">Total stock</th>
                            <th scope="col">Price each</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-rows">
                        <?php if ($inventoryResult->num_rows === 0): ?>
                            <tr data-search-empty><td colspan="6" class="text-center text-muted py-4">No inventory items found.</td></tr>
                        <?php else: ?>
                            <?php while ($item = $inventoryResult->fetch_assoc()): ?>
                                <?php $quantity = (int) $item['quantity']; ?>
                                <tr data-search-item>
                                    <td><?= htmlspecialchars($item['product'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>Fish</td>
                                    <td><?= $quantity ?></td>
                                    <td>₱<?= number_format((float) $item['cost_per_piece'], 2) ?></td>
                                    <td><?= $quantity <= 10 ? 'Low stock' : 'In stock' ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr data-search-empty hidden><td colspan="6" class="text-center text-muted py-4">No matching products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- Manage Inventory Modal -->
<div class="modal fade" id="inventory-modal" tabindex="-1" aria-labelledby="inventory-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <p class="welcome-label mb-1">STOCK CONTROL</p>
                    <h5 class="modal-title fw-bold" id="inventory-modal-title">Manage inventory</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="inventory-search-input" class="form-label small fw-semibold">Find a product</label>
                <div class="d-flex gap-2 mb-3">
                    <input type="search" class="form-control" id="inventory-search-input" placeholder="Search by fish name">
                    <button class="btn btn-outline-success" type="button" id="inventory-search-btn">Search</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th scope="col">Product name</th><th scope="col">Quantity</th><th scope="col" class="text-end">Action</th></tr></thead>
                        <tbody id="manage-inventory-rows">
                            <?php if ($manageInventoryResult === null || $manageInventoryResult->num_rows === 0): ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">No inventory items found.</td></tr>
                            <?php else: ?>
                                <?php while ($manageItem = $manageInventoryResult->fetch_assoc()): ?>
                                    <tr data-manage-inventory-item>
                                        <td><?= htmlspecialchars($manageItem['product'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= (int) $manageItem['quantity'] ?></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-outline-success btn-sm" data-edit-inventory
                                                    data-id="<?= (int) $manageItem['inv_id'] ?>"
                                                    data-product="<?= htmlspecialchars($manageItem['product'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-quantity="<?= (int) $manageItem['quantity'] ?>"
                                                    data-price="<?= (float) $manageItem['cost_per_piece'] ?>">Update</button>
                                                <form method="post" action="inventory.php" onsubmit="return confirmInventoryDelete();">
                                                    <input type="hidden" name="inventory_id" value="<?= (int) $manageItem['inv_id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" name="delete_item" value="1">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr id="manage-inventory-empty" hidden><td colspan="3" class="text-center text-muted py-4">No matching products found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top"><button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<!-- Update Inventory Modal -->
<div class="modal fade" id="update-item-modal" tabindex="-1" aria-labelledby="update-item-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header border-bottom">
                <div><p class="welcome-label mb-1">STOCK CONTROL</p><h5 class="modal-title fw-bold" id="update-item-modal-title">Update inventory item</h5></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="inventory.php" enctype="multipart/form-data">
                <input type="hidden" name="inventory_id" id="edit-item-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-item-name" class="form-label small fw-semibold">Product name</label>
                        <input type="text" class="form-control" id="edit-item-name" readonly>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="edit-item-stock" class="form-label small fw-semibold">Stock quantity</label>
                            <input type="number" class="form-control" id="edit-item-stock" name="quantity" min="0" step="1" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="edit-item-price" class="form-label small fw-semibold">Price each</label>
                            <input type="number" class="form-control" id="edit-item-price" name="cost_per_piece" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="edit-item-image" class="form-label small fw-semibold">Change product image <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="file" class="form-control" id="edit-item-image" name="image" accept="image/*">
                        <div class="form-text">Leave blank to keep the current image.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm" name="update_item" value="1">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="add-item-modal" tabindex="-1" aria-labelledby="add-item-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <p class="welcome-label mb-1">INVENTORY MANAGEMENT</p>
                    <h5 class="modal-title fw-bold" id="add-item-modal-title">Add item</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="inventory.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new-item-name" class="form-label small fw-semibold">Product name</label>
                        <input type="text" class="form-control" id="new-item-name" name="product"
                            placeholder="Enter fish name" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="new-item-stock" class="form-label small fw-semibold">Stock quantity</label>
                            <input type="number" class="form-control" id="new-item-stock"
                                name="quantity" min="0" step="1" placeholder="0" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="new-item-price" class="form-label small fw-semibold">Price each</label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input type="number" class="form-control" id="new-item-price"
                                    name="cost_per_piece" min="0" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new-item-image" class="form-label small fw-semibold">Product image <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="file" class="form-control" id="new-item-image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm" name="add_item" value="1">Save item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmInventoryDelete() {
    return confirm('Are you sure you want to delete this inventory item?');
}

const inventorySearchInput = document.getElementById('inventory-search-input');
const manageInventoryRows = document.querySelectorAll('[data-manage-inventory-item]');
const manageInventoryEmpty = document.getElementById('manage-inventory-empty');

function filterManageInventory() {
    const searchText = inventorySearchInput.value.trim().toLocaleLowerCase();
    let shownCount = 0;

    manageInventoryRows.forEach(function (row) {
        const matches = row.textContent.toLocaleLowerCase().includes(searchText);
        row.hidden = !matches;
        if (matches) shownCount += 1;
    });

    if (manageInventoryEmpty) manageInventoryEmpty.hidden = shownCount > 0;
}

document.getElementById('inventory-search-btn').addEventListener('click', filterManageInventory);
inventorySearchInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') filterManageInventory();
});

document.querySelectorAll('[data-edit-inventory]').forEach(function (button) {
    button.addEventListener('click', function () {
        document.getElementById('edit-item-id').value = button.dataset.id;
        document.getElementById('edit-item-name').value = button.dataset.product;
        document.getElementById('edit-item-stock').value = button.dataset.quantity;
        document.getElementById('edit-item-price').value = button.dataset.price;

        const manageModal = document.getElementById('inventory-modal');
        manageModal.addEventListener('hidden.bs.modal', function showUpdateModal() {
            manageModal.removeEventListener('hidden.bs.modal', showUpdateModal);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('update-item-modal')).show();
        });
        bootstrap.Modal.getOrCreateInstance(manageModal).hide();
    });
});
</script>

<?php require 'manage-accounts.php'; ?>
</body>
</html>
