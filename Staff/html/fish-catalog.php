<?php
$staffBackendAction = 'catalog';
require 'staff_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Fish Catalog</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Admin/css/homepage.css">
    <link rel="stylesheet" href="../css/staff.css">
    <script src="../../bootstrap_template/js/bootstrap.bundle.min.js" defer></script>
    <script src="../js/staff-panels.js" defer></script>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <a class="brand" href="staff.php"><span class="brand-logo">A</span><span>Aquatica</span></a>
            <p class="menu-title">MAIN MENU</p>
            <nav class="nav flex-column" aria-label="Main navigation">
                <a class="nav-link" href="staff.php">Dashboard</a>
                <a class="nav-link active" href="fish-catalog.php" aria-current="page">Fish Catalog</a>
                <a class="nav-link" href="inventory-management.php">Inventory</a>
                <a class="nav-link" href="transaction.php">Billing</a>
            </nav>
            <a class="sidebar-logout" href="../../login_page/html/login.php">Logout</a>
            <div class="sidebar-workspace"><span class="sidebar-status-dot" aria-hidden="true"></span><span><strong>Nasugbu
                        Aquatica</strong><small>Staff workspace</small></span></div>
        </aside>

        <main class="main-content staff-content">
            <div class="staff-page-top">
                <nav aria-label="Breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="staff.php">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Fish Catalog</li></ol></nav>
                <span class="staff-location"><span aria-hidden="true"></span>Nasugbu Aquatica</span>
            </div>
            <header class="staff-heading">
                <div><p class="welcome-label">PRODUCT REFERENCE</p><h1>Fish Catalog</h1><p class="page-subtitle">Browse products and images saved in inventory.</p></div>
                <a class="btn btn-outline-success" href="inventory-management.php">View inventory</a>
            </header>

            <div class="staff-panel mb-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div><strong class="text-dark">Inventory products</strong><p class="mb-0 mt-1 text-secondary small">Search by product name.</p></div>
                    <div class="input-group catalog-search">
                        <span class="input-group-text" aria-hidden="true">&#8982;</span>
                        <input class="form-control" type="search" placeholder="Search fish catalog" aria-label="Search fish catalog" data-search-for="catalog-list">
                    </div>
                </div>
            </div>

            <section class="row g-3" id="catalog-list" aria-label="Fish catalog items">
                <?php if ($catalogResult->num_rows === 0): ?>
                    <div class="col-12">
                        <div class="staff-panel staff-empty-state"><strong>No products in inventory</strong><p>Add products in the admin inventory to show them here.</p></div>
                    </div>
                <?php else: ?>
                    <?php while ($fish = $catalogResult->fetch_assoc()): ?>
                        <?php $imageInfo = $fish['product_image'] !== '' ? getimagesizefromstring($fish['product_image']) : false; ?>
                        <div class="col-sm-6 col-xl-3" data-search-card data-search="<?= htmlspecialchars($fish['product'], ENT_QUOTES, 'UTF-8') ?>">
                            <article class="catalog-card h-100">
                                <div class="catalog-card-art">
                                    <?php if ($imageInfo !== false): ?>
                                        <img src="data:<?= htmlspecialchars($imageInfo['mime'], ENT_QUOTES, 'UTF-8') ?>;base64,<?= base64_encode($fish['product_image']) ?>" alt="<?= htmlspecialchars($fish['product'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?php else: ?>
                                        <div class="catalog-image-placeholder" role="img" aria-label="No image available"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="catalog-card-body">
                                    <div class="catalog-card-title-row">
                                        <h2><?= htmlspecialchars($fish['product'], ENT_QUOTES, 'UTF-8') ?></h2>
                                        <button class="btn btn-outline-success btn-sm catalog-view-button" type="button" data-view-fish>View</button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                    <div class="col-12" data-search-empty hidden>
                        <div class="staff-panel staff-empty-state"><strong>No matching products found</strong><p>Try another product name.</p></div>
                    </div>
                <?php endif; ?>
            </section>
            <div class="modal fade" id="fish-image-modal" tabindex="-1" aria-labelledby="fish-image-modal-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content catalog-image-modal">
                        <div class="modal-header">
                            <h2 class="modal-title" id="fish-image-modal-title">Product image</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img id="fish-image-modal-image" src="" alt="" hidden>
                            <p id="fish-image-modal-empty" class="text-secondary text-center mb-0">No image available for this product.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.querySelectorAll('[data-view-fish]').forEach(function (button) {
            button.addEventListener('click', function () {
                const card = button.closest('.catalog-card');
                const productName = card.querySelector('h2').textContent;
                const cardImage = card.querySelector('.catalog-card-art img');
                const modalImage = document.getElementById('fish-image-modal-image');
                const emptyMessage = document.getElementById('fish-image-modal-empty');

                document.getElementById('fish-image-modal-title').textContent = productName;
                if (cardImage) {
                    modalImage.src = cardImage.src;
                    modalImage.alt = productName;
                    modalImage.hidden = false;
                    emptyMessage.hidden = true;
                } else {
                    modalImage.removeAttribute('src');
                    modalImage.hidden = true;
                    emptyMessage.hidden = false;
                }

                bootstrap.Modal.getOrCreateInstance(document.getElementById('fish-image-modal')).show();
            });
        });
    </script>
</body>
</html>

