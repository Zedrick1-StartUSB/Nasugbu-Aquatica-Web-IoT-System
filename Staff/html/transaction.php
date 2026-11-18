<?php
$staffBackendAction = 'transaction';
require 'staff_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Billing</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Admin/css/homepage.css">
    <link rel="stylesheet" href="../css/staff.css">
    <link rel="stylesheet" href="../css/transaction.css">
    <script src="../../bootstrap_template/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <a class="brand" href="staff.php"><span class="brand-logo">A</span><span>Aquatica</span></a>
            <p class="menu-title">MAIN MENU</p>
            <nav class="nav flex-column" aria-label="Main navigation">
                <a class="nav-link" href="staff.php">Dashboard</a>
                <a class="nav-link" href="fish-catalog.php">Fish Catalog</a>
                <a class="nav-link" href="inventory-management.php">Inventory</a>
                <a class="nav-link active" href="transaction.php" aria-current="page">Billing</a>
            </nav>
            <a class="sidebar-logout" href="../../login_page/html/login.php">Logout</a>
            <div class="sidebar-workspace"><span class="sidebar-status-dot" aria-hidden="true"></span><span><strong>Nasugbu
                        Aquatica</strong><small>Staff workspace</small></span></div>
        </aside>

        <main class="main-content staff-content transaction-content">
            <div class="staff-page-top">
                <nav aria-label="Breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="staff.php">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Billing</li></ol></nav>
                <span class="staff-location"><span aria-hidden="true"></span>Nasugbu Aquatica</span>
            </div>

            <header class="transaction-page-heading">
                <div><p class="welcome-label">SALES WORKSPACE</p><h1>Transaction Processing</h1><p>Enter the buyer, payment method, and items purchased.</p></div>
                <button type="button" class="btn btn-success transaction-logs-button" data-bs-toggle="modal" data-bs-target="#transaction-logs"><span aria-hidden="true">&#9776;</span> View transaction logs</button>
            </header>

            <section class="transaction-entry-card" aria-labelledby="transaction-entry-title">
                <div class="transaction-entry-heading">
                    <span class="transaction-entry-icon" aria-hidden="true">&#8644;</span>
                    <div><h2 id="transaction-entry-title">New transaction</h2><p>Fill in the transaction details below.</p></div>
                </div>
                <?php if ($savedMessage !== ''): ?><div class="alert alert-success" role="status"><?= htmlspecialchars($savedMessage, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                <?php if ($formError !== ''): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                <form method="post" id="transaction-form" class="row g-3 align-items-end">
                    <div class="col-12">
                        <div class="transaction-customer-row">
                            <div>
                                <label for="buyer-name" class="form-label">Name</label>
                                <input class="form-control" type="text" id="buyer-name" name="buyer_name" autocomplete="name" placeholder="Enter customer name" value="<?= htmlspecialchars($buyerName, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div>
                                <label for="payment-method" class="form-label">Mode of payment</label>
                                <select class="form-select" id="payment-method" name="payment_method" required>
                                    <option value="" selected disabled>Select payment method</option>
                                    <?php foreach (['Cash', 'GCash', 'Bank transfer', 'Other'] as $method): ?>
                                        <option value="<?= htmlspecialchars($method, ENT_QUOTES, 'UTF-8') ?>" <?= $paymentMethod === $method ? 'selected' : '' ?>><?= htmlspecialchars($method, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="transaction-number" class="form-label">Transaction number</label>
                                <input class="form-control" type="number" id="transaction-number" name="transaction_number" min="1" step="1" placeholder="Optional" value="<?= htmlspecialchars($transactionNumber, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Items bought</label>
                        <div id="transaction-items" class="d-grid gap-2">
                            <?php foreach ($items as $index => $item): ?>
                                <div class="row g-2 align-items-end transaction-item-row">
                                    <div class="col-sm-9">
                                        <label class="form-label small" for="item-product-<?= $index ?>">Product</label>
                                        <select class="form-select" id="item-product-<?= $index ?>" name="items[<?= $index ?>][product]" required>
                                            <option value="" disabled <?= empty($item['product']) ? 'selected' : '' ?>><?= $inventoryResult->num_rows === 0 ? 'No product, please add stock' : 'Select a product' ?></option>
                                            <?php if ($inventoryResult->num_rows > 0): ?>
                                                <?php $inventoryResult->data_seek(0); ?>
                                                <?php while ($product = $inventoryResult->fetch_assoc()): ?>
                                                    <option value="<?= htmlspecialchars($product['product'], ENT_QUOTES, 'UTF-8') ?>" data-price="<?= (float) $product['cost_per_piece'] ?>" <?= ($item['product'] ?? '') === $product['product'] ? 'selected' : '' ?>><?= htmlspecialchars($product['product'], ENT_QUOTES, 'UTF-8') ?> — &#8369;<?= number_format((float) $product['cost_per_piece'], 2) ?> (<?= (int) $product['quantity'] ?> available)</option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3"><label class="form-label small" for="item-quantity-<?= $index ?>">Quantity</label><input class="form-control" id="item-quantity-<?= $index ?>" name="items[<?= $index ?>][quantity]" type="number" min="1" step="1" placeholder="0" value="<?= htmlspecialchars((string) ($item['quantity'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-success mt-3" id="add-transaction-item">+ Add item</button>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 transaction-actions">
                        <button type="button" class="btn btn-outline-success" id="view-bill-button" data-bs-toggle="modal" data-bs-target="#bill-preview">View bill</button>
                        <button class="btn btn-success" type="submit" name="save_transaction" value="1">Save transaction</button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <div class="modal fade" id="bill-preview" tabindex="-1" aria-labelledby="bill-preview-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content transaction-logs-modal">
                <div class="modal-header">
                    <div><p class="welcome-label mb-1">BILL PREVIEW</p><h2 class="modal-title" id="bill-preview-title">Transaction bill</h2></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <dl class="bill-customer-details">
                        <div><dt>Transaction number</dt><dd id="bill-transaction-number">&#8212;</dd></div>
                        <div><dt>Customer</dt><dd id="bill-customer">&#8212;</dd></div>
                        <div><dt>Mode of payment</dt><dd id="bill-payment">&#8212;</dd></div>
                    </dl>
                    <div class="table-responsive">
                        <table class="table transaction-logs-table align-middle mb-0">
                            <thead><tr><th scope="col">Product</th><th scope="col">Qty</th><th scope="col">Amount</th></tr></thead>
                            <tbody id="bill-items"><tr><td colspan="3" class="text-center text-muted py-3">No items added.</td></tr></tbody>
                        </table>
                    </div>
                    <div class="bill-total-row"><span>Total</span><strong id="bill-total">&#8369;0.00</strong></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transaction-logs" tabindex="-1" aria-labelledby="transaction-logs-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content transaction-logs-modal">
                <div class="modal-header">
                    <div><p class="welcome-label mb-1">SALES HISTORY</p><h2 class="modal-title" id="transaction-logs-title">Transaction logs</h2></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table transaction-logs-table align-middle mb-0">
                            <thead><tr><th scope="col">Transaction number</th><th scope="col">Customer</th><th scope="col">Products and quantities</th><th scope="col">Total quantity</th><th scope="col">Total paid</th></tr></thead>
                            <tbody>
                                <?php if ($transactionLogsResult !== null && $transactionLogsResult->num_rows > 0): ?>
                                    <?php while ($transactionLog = $transactionLogsResult->fetch_assoc()): ?>
                                        <?php $boughtProducts = json_decode($transactionLog['product_bought'], true); ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $transactionLog['transaction_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($transactionLog['customer'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?php if (is_array($boughtProducts)): ?>
                                                    <?php foreach ($boughtProducts as $productName => $boughtProduct): ?>
                                                        <?php if (is_array($boughtProduct) && isset($boughtProduct['product'], $boughtProduct['quantity'])): ?>
                                                            <div><?= htmlspecialchars((string) $boughtProduct['product'], ENT_QUOTES, 'UTF-8') ?> &times; <?= (int) $boughtProduct['quantity'] ?></div>
                                                        <?php else: ?>
                                                            <div><?= htmlspecialchars((string) $productName, ENT_QUOTES, 'UTF-8') ?> &times; <?= (int) $boughtProduct ?></div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($transactionLog['product_bought'], ENT_QUOTES, 'UTF-8') ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= (int) $transactionLog['total_quantity'] ?></td>
                                            <td>&#8369;<?= number_format((float) $transactionLog['total_paid'], 2) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5"><div class="transaction-logs-empty"><span aria-hidden="true">&#8644;</span><strong>No transaction logs available</strong><small>Completed transactions will appear here.</small></div></td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
</body>
<script>
document.getElementById('add-transaction-item').addEventListener('click', function () {
    const container = document.getElementById('transaction-items');
    const index = container.querySelectorAll('.transaction-item-row').length;
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end transaction-item-row';
    row.innerHTML = `<div class="col-sm-9"><label class="form-label small" for="item-product-${index}">Product</label><select class="form-select" id="item-product-${index}" name="items[${index}][product]" required></select></div><div class="col-sm-3"><label class="form-label small" for="item-quantity-${index}">Quantity</label><input class="form-control" id="item-quantity-${index}" name="items[${index}][quantity]" type="number" min="1" step="1" placeholder="0" required></div>`;
    const firstProductSelect = document.querySelector('select[name$="[product]"]');
    const newProductSelect = row.querySelector('select[name$="[product]"]');
    newProductSelect.innerHTML = firstProductSelect.innerHTML;
    newProductSelect.selectedIndex = 0;
    container.appendChild(row);
    updateBillPreview();
});

function updateBillPreview() {
    let total = 0;
    const billItems = document.getElementById('bill-items');
    billItems.replaceChildren();
    let itemCount = 0;

    document.getElementById('bill-customer').textContent = document.getElementById('buyer-name').value.trim() || '\u2014';
    document.getElementById('bill-payment').textContent = document.getElementById('payment-method').value || '\u2014';
    document.getElementById('bill-transaction-number').textContent = document.getElementById('transaction-number').value.trim() || '\u2014';

    document.querySelectorAll('.transaction-item-row').forEach(function (row) {
        const productInput = row.querySelector('select[name$="[product]"]');
        const quantityInput = row.querySelector('input[name$="[quantity]"]');
        const selectedProduct = productInput.selectedOptions[0];
        const unitPrice = selectedProduct && selectedProduct.dataset.price !== undefined
            ? Number(selectedProduct.dataset.price)
            : undefined;
        const quantity = Number.parseInt(quantityInput.value, 10);
        if (!productInput.value || !Number.isFinite(quantity) || quantity < 1) return;

        const subtotal = unitPrice === undefined ? null : unitPrice * quantity;
        if (subtotal !== null) total += subtotal;
        const tableRow = document.createElement('tr');
        const productCell = document.createElement('td');
        const quantityCell = document.createElement('td');
        const amountCell = document.createElement('td');
        productCell.textContent = productInput.value;
        quantityCell.textContent = String(quantity);
        amountCell.textContent = subtotal === null ? 'Price unavailable' : '\u20B1' + subtotal.toFixed(2);
        tableRow.append(productCell, quantityCell, amountCell);
        billItems.appendChild(tableRow);
        itemCount += 1;
    });
    if (itemCount === 0) {
        const emptyRow = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.colSpan = 3;
        emptyCell.className = 'text-center text-muted py-3';
        emptyCell.textContent = 'No items added.';
        emptyRow.appendChild(emptyCell);
        billItems.appendChild(emptyRow);
    }
    document.getElementById('bill-total').textContent = '\u20B1' + total.toFixed(2);
}
document.getElementById('transaction-items').addEventListener('input', updateBillPreview);
document.getElementById('transaction-items').addEventListener('change', updateBillPreview);
document.getElementById('buyer-name').addEventListener('input', updateBillPreview);
document.getElementById('payment-method').addEventListener('change', updateBillPreview);
document.getElementById('transaction-number').addEventListener('input', updateBillPreview);
document.getElementById('view-bill-button').addEventListener('click', updateBillPreview);
updateBillPreview();
</script>
</html>

