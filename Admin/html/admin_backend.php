<?php
require_once '../../global/auth.php';
if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(empty($_SESSION['username']) ? 401 : 403);
    exit('You do not have permission to manage accounts.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_account'])) {
    http_response_code(405);
    exit('Unsupported request.');
}

$returnTo = (string) ($_POST['return_to'] ?? 'homepage.php');
if (!in_array($returnTo, ['homepage.php', 'inventory.php', 'device.php'], true)) {
    $returnTo = 'homepage.php';
}

if (empty($_SESSION['accounts_csrf']) || !hash_equals($_SESSION['accounts_csrf'], (string) ($_POST['csrf_token'] ?? ''))) {
    header('Location: ' . $returnTo . '?account_error=csrf#accountsModal');
    exit;
}

$accountId = filter_var($_POST['account_id'] ?? null, FILTER_VALIDATE_INT);
if ($accountId === false || $accountId === null || $accountId < 1) {
    header('Location: ' . $returnTo . '?account_error=invalid#accountsModal');
    exit;
}

require '../../database_conn.php';
$conn->begin_transaction();
$adminAccountsResult = $conn->query("SELECT id FROM users WHERE LOWER(role) = 'admin' FOR UPDATE");
if ($adminAccountsResult === false) {
    $conn->rollback();
    header('Location: ' . $returnTo . '?account_error=delete#accountsModal');
    exit;
}
$adminCount = $adminAccountsResult->num_rows;

$selectedAccountStatement = $conn->prepare('SELECT role FROM users WHERE id = ? FOR UPDATE');
$selectedAccountStatement->bind_param('i', $accountId);
$selectedAccountStatement->execute();
$selectedAccountResult = $selectedAccountStatement->get_result();
if ($selectedAccountResult->num_rows !== 1) {
    $conn->rollback();
    header('Location: ' . $returnTo . '?account_error=invalid#accountsModal');
    exit;
}
$selectedAccount = $selectedAccountResult->fetch_assoc();
if (strtolower((string) $selectedAccount['role']) === 'admin' && $adminCount <= 1) {
    $conn->rollback();
    header('Location: ' . $returnTo . '?account_error=last_admin#accountsModal');
    exit;
}

$deleteStatement = $conn->prepare('DELETE FROM users WHERE id = ?');
$deleteStatement->bind_param('i', $accountId);
if ($deleteStatement->execute() && $deleteStatement->affected_rows === 1) {
    $conn->commit();
    header('Location: ' . $returnTo . '?account_deleted=1#accountsModal');
    exit;
}

$conn->rollback();
header('Location: ' . $returnTo . '?account_error=delete#accountsModal');
exit;
