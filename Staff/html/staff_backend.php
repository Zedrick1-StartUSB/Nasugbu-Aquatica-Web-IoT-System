<?php
/**
 * Staff page controller. UI files set $staffBackendAction before requiring
 * this file; database access and request handling stay centralized here.
 */
require_once '../../global/auth.php';
$requiredRole = $requiredRole ?? 'staff';
if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== strtolower($requiredRole)) {
    http_response_code(empty($_SESSION['username']) ? 401 : 403);
    header('Location: ../../login_page/html/login.php');
    exit('You do not have permission to access this page.');
}
require '../../database_conn.php';

$staffBackendAction = $staffBackendAction ?? '';
$inventoryResult = null;
$inventoryStats = ['varieties' => 0, 'total_stock' => 0, 'low_stock' => 0];
$savedMessage = '';
$formError = '';
$inventoryError = '';
$buyerName = '';
$paymentMethod = '';
$transactionNumber = '';
$items = [['product' => '', 'quantity' => '']];
$catalogResult = null;
$transactionLogsResult = null;
$manageInventoryResult = null;


if ($staffBackendAction === 'inventory' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $inventoryId = filter_var($_POST['inventory_id'] ?? null, FILTER_VALIDATE_INT);

    if ($inventoryId === false || $inventoryId < 1) {
        $inventoryError = 'Select a valid inventory item to delete.';
    } else {
        try {
            $deleteQuery = 'DELETE FROM inventory_management WHERE inv_id = ?';
            $deleteStatement = $conn->prepare($deleteQuery);
            $deleteStatement->bind_param('i', $inventoryId);
            $deleteStatement->execute();
            if ($deleteStatement->affected_rows > 0) {
                header('Location: inventory.php?deleted=1');
                exit;
            }
            $inventoryError = 'The inventory item was not found.';
        } catch (mysqli_sql_exception $error) {
            $inventoryError = 'The inventory item could not be deleted. Try again.';
        }
    }
}


if ($staffBackendAction === 'inventory' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $inventoryId = filter_var($_POST['inventory_id'] ?? null, FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    $price = filter_var($_POST['cost_per_piece'] ?? null, FILTER_VALIDATE_FLOAT);
    $imageFile = $_FILES['image'] ?? null;
    $productImage = null;

    if ($inventoryId === false || $inventoryId < 1 || $quantity === false || $quantity < 0 || $price === false || $price < 0) {
        $inventoryError = 'Enter a valid stock quantity and price.';
    } elseif ($imageFile !== null && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($imageFile['error'] !== UPLOAD_ERR_OK || $imageFile['size'] > 5 * 1024 * 1024) {
            $inventoryError = 'Choose an image smaller than 5 MB.';
        } else {
            $imageInfo = getimagesize($imageFile['tmp_name']);
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedImageTypes, true)) {
                $inventoryError = 'Upload a JPG, PNG, GIF, or WebP image.';
            } else {
                $productImage = file_get_contents($imageFile['tmp_name']);
                if ($productImage === false) {
                    $inventoryError = 'The selected image could not be read.';
                }
            }
        }
    }

    if ($inventoryError === '') {
        try {
            if ($productImage === null) {
                $updateQuery = 'UPDATE inventory_management SET quantity = ?, cost_per_piece = ? WHERE inv_id = ?';
                $updateStatement = $conn->prepare($updateQuery);
                $updateStatement->bind_param('idi', $quantity, $price, $inventoryId);
            } else {
                $updateQuery = 'UPDATE inventory_management SET quantity = ?, cost_per_piece = ?, product_image = ? WHERE inv_id = ?';
                $updateStatement = $conn->prepare($updateQuery);
                $updateStatement->bind_param('idsi', $quantity, $price, $productImage, $inventoryId);
            }
            $updateStatement->execute();
            header('Location: inventory.php?updated=1');
            exit;
        } catch (mysqli_sql_exception $error) {
            $inventoryError = 'The inventory item could not be updated. Try again.';
        }
    }
}


if ($staffBackendAction === 'inventory' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $product = trim((string) ($_POST['product'] ?? ''));
    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    $price = filter_var($_POST['cost_per_piece'] ?? null, FILTER_VALIDATE_FLOAT);
    $productImage = '';

    if ($product === '' || $quantity === false || $quantity < 0 || $price === false || $price < 0) {
        $inventoryError = 'Enter a product name, a valid stock quantity, and a price of zero or more.';
    } else {
        $imageFile = $_FILES['image'] ?? null;
        if ($imageFile !== null && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($imageFile['error'] !== UPLOAD_ERR_OK || $imageFile['size'] > 5 * 1024 * 1024) {
                $inventoryError = 'Choose an image smaller than 5 MB.';
            } else {
                $imageInfo = getimagesize($imageFile['tmp_name']);
                $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedImageTypes, true)) {
                    $inventoryError = 'Upload a JPG, PNG, GIF, or WebP image.';
                } else {
                    $imageContents = file_get_contents($imageFile['tmp_name']);
                    if ($imageContents === false) {
                        $inventoryError = 'The selected image could not be read.';
                    } else {
                        $productImage = $imageContents;
                    }
                }
            }
        }

        if ($inventoryError === '') {
            try {
                $insertQuery = 'INSERT INTO inventory_management (product, cost_per_piece, quantity, product_image) VALUES (?, ?, ?, ?)';
                $insertStatement = $conn->prepare($insertQuery);
                $insertStatement->bind_param('sdis', $product, $price, $quantity, $productImage);
                $insertStatement->execute();
                header('Location: inventory.php?added=1');
                exit;
            } catch (mysqli_sql_exception $error) {
                $inventoryError = 'The item could not be added. Check the inventory table and try again.';
            }
        }
    }
}

if ($staffBackendAction === 'inventory' && isset($_GET['added'])) {
    $savedMessage = 'Inventory item added successfully.';
}
if ($staffBackendAction === 'inventory' && isset($_GET['updated'])) {
    $savedMessage = 'Inventory item updated successfully.';
}
if ($staffBackendAction === 'inventory' && isset($_GET['deleted'])) {
    $savedMessage = 'Inventory item deleted successfully.';
}


$inventoryQuery = 'SELECT product, cost_per_piece, quantity FROM inventory_management';
if ($staffBackendAction === 'transaction') {
    $inventoryQuery .= ' WHERE quantity > 0';
}
$inventoryQuery .= ' ORDER BY product';
$inventoryResult = $conn->query($inventoryQuery);

if ($staffBackendAction === 'inventory') {
    $manageInventoryQuery = 'SELECT inv_id, product, quantity, cost_per_piece FROM inventory_management ORDER BY product';
    $manageInventoryResult = $conn->query($manageInventoryQuery);
}


$statsQuery = 'SELECT COUNT(*) AS varieties, COALESCE(SUM(quantity), 0) AS total_stock, '
    . 'COALESCE(SUM(CASE WHEN quantity <= 10 THEN 1 ELSE 0 END), 0) AS low_stock '
    . 'FROM inventory_management';
$statsResult = $conn->query($statsQuery);
$inventoryStats = $statsResult->fetch_assoc();
$statsResult->free();


if ($staffBackendAction === 'catalog') {
    $catalogQuery = 'SELECT product, product_image FROM inventory_management ORDER BY product';
    $catalogResult = $conn->query($catalogQuery);
}

if ($staffBackendAction === 'dashboard') {
    $recentTransactionsQuery = 'SELECT customer, product_bought, total_quantity, total_paid, transaction_number '
        . 'FROM `transaction` ORDER BY transaction_id DESC LIMIT 5';
    $recentTransactionsResult = $conn->query($recentTransactionsQuery);
}


if ($staffBackendAction === 'transaction') {
    $buyerName = trim((string) ($_POST['buyer_name'] ?? ''));
    $paymentMethod = (string) ($_POST['payment_method'] ?? '');
    $transactionNumber = trim((string) ($_POST['transaction_number'] ?? ''));
    $submittedItems = $_POST['items'] ?? null;
    if (is_array($submittedItems) && $submittedItems !== []) {
        $items = array_values(array_filter($submittedItems, 'is_array'));
    }

    if (isset($_GET['saved'])) {
        $savedMessage = 'Transaction saved successfully.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_transaction'])) {
        $transactionNumberValue = $transactionNumber === ''
            ? null
            : filter_var($transactionNumber, FILTER_VALIDATE_INT);
        $productQuantities = [];
        $productBoughtDictionary = [];
        $totalQuantity = 0;
        $totalPaid = 0;

        if ($buyerName === '' || !in_array($paymentMethod, ['Cash', 'GCash', 'Bank transfer', 'Other'], true)
            || ($transactionNumberValue !== null && ($transactionNumberValue === false || $transactionNumberValue < 1))) {
            $formError = 'Enter a customer name, select a payment method, and use a positive whole number for the optional transaction number.';
        } else {
            foreach ($items as $itemIndex => $item) {
                $productName = trim((string) ($item['product'] ?? ''));
                $itemQuantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
                if ($productName === '' || $itemQuantity === false || $itemQuantity < 1) {
                    $formError = 'Select a product and enter a quantity of at least one for every item.';
                    break;
                }
                $productBoughtDictionary['item_' . ((int) $itemIndex + 1)] = [
                    'product' => $productName,
                    'quantity' => $itemQuantity,
                ];
                $productQuantities[$productName] = ($productQuantities[$productName] ?? 0) + $itemQuantity;
                $totalQuantity += $itemQuantity;
            }

            if ($formError === '' && $productQuantities === []) {
                $formError = 'Add at least one product to the transaction.';
            }
        }

        if ($formError === '') {
            try {
                $conn->begin_transaction();
                $productQuery = 'SELECT cost_per_piece, quantity FROM inventory_management WHERE product = ? FOR UPDATE';
                $productStatement = $conn->prepare($productQuery);

                foreach ($productQuantities as $productName => $itemQuantity) {
                    $productStatement->bind_param('s', $productName);
                    $productStatement->execute();
                    $productData = $productStatement->get_result()->fetch_assoc();
                    if ($productData === null || (int) $productData['quantity'] < $itemQuantity) {
                        throw new RuntimeException('Not enough stock is available for ' . $productName . '.');
                    }
                    $totalPaid += (int) $productData['cost_per_piece'] * $itemQuantity;
                }

                $productBought = json_encode($productBoughtDictionary, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                $totalQuantityForDatabase = (string) $totalQuantity;
                if ($transactionNumberValue === null) {
                    $insertQuery = 'INSERT INTO `transaction` (customer, product_bought, total_quantity, total_paid, transaction_number) VALUES (?, ?, ?, ?, NULL)';
                    $insertStatement = $conn->prepare($insertQuery);
                    $insertStatement->bind_param('sssi', $buyerName, $productBought, $totalQuantityForDatabase, $totalPaid);
                } else {
                    $insertQuery = 'INSERT INTO `transaction` (customer, product_bought, total_quantity, total_paid, transaction_number) VALUES (?, ?, ?, ?, ?)';
                    $insertStatement = $conn->prepare($insertQuery);
                    $insertStatement->bind_param('sssii', $buyerName, $productBought, $totalQuantityForDatabase, $totalPaid, $transactionNumberValue);
                }
                $insertStatement->execute();

                $stockUpdateQuery = 'UPDATE inventory_management SET quantity = quantity - ? WHERE product = ?';
                $stockUpdateStatement = $conn->prepare($stockUpdateQuery);
                foreach ($productQuantities as $productName => $itemQuantity) {
                    $stockUpdateStatement->bind_param('is', $itemQuantity, $productName);
                    $stockUpdateStatement->execute();
                }

                $conn->commit();
                header('Location: transaction.php?saved=1');
                exit;
            } catch (Throwable $error) {
                $conn->rollback();
                $formError = $error instanceof RuntimeException
                    ? $error->getMessage()
                    : 'The transaction could not be saved. Check the transaction table and try again.';
            }
        }
    }

    $transactionLogsQuery = 'SELECT customer, product_bought, total_quantity, total_paid, transaction_number '
        . 'FROM `transaction` ORDER BY transaction_id DESC LIMIT 20';
    $transactionLogsResult = $conn->query($transactionLogsQuery);
}
