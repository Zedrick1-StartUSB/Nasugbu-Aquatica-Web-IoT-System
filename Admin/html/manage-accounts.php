<?php
require_once '../../global/auth.php';
if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(empty($_SESSION['username']) ? 401 : 403);
    exit('You do not have permission to view account details.');
}

require_once '../../database_conn.php';
if (empty($_SESSION['accounts_csrf'])) {
    $_SESSION['accounts_csrf'] = bin2hex(random_bytes(32));
}
$accountsResult = $conn->query('SELECT id, username, role FROM users ORDER BY id ASC');
$accountsError = $accountsResult === false ? 'Account details could not be loaded.' : '';
$accounts = [];
$adminCount = 0;
if ($accountsResult !== false) {
    while ($account = $accountsResult->fetch_assoc()) {
        $accounts[] = $account;
        if (strtolower((string) $account['role']) === 'admin') {
            $adminCount++;
        }
    }
}
?>
<div class="modal fade" id="accountsModal" tabindex="-1" aria-labelledby="accountsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title fs-5" id="accountsModalLabel">Business accounts</h2>
                    <p class="text-secondary small mb-0">Account ID, username, and role</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($_GET['account_deleted'])): ?>
                    <div class="alert alert-success" role="status">Account deleted successfully.</div>
                <?php elseif (isset($_GET['account_error']) && $_GET['account_error'] === 'last_admin'): ?>
                    <div class="alert alert-warning" role="alert">At least one admin account must remain. This account cannot be deleted.</div>
                <?php elseif (isset($_GET['account_error'])): ?>
                    <div class="alert alert-danger" role="alert">The account could not be deleted. Please try again.</div>
                <?php endif; ?>
                <?php if ($accountsError !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($accountsError, ENT_QUOTES, 'UTF-8') ?></div>
                <?php elseif (count($accounts) === 0): ?>
                    <p class="text-secondary mb-0">No accounts found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Business role</th><th scope="col">Delete</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accounts as $account): ?>
                                    <tr>
                                        <td><?= (int) $account['id'] ?></td>
                                        <td><?= htmlspecialchars($account['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars(ucfirst(strtolower($account['role'])), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <form action="admin_backend.php" method="POST" onsubmit="return confirm('Delete this account? This action cannot be undone.');">
                                                <input type="hidden" name="delete_account" value="1">
                                                <input type="hidden" name="account_id" value="<?= (int) $account['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['accounts_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="return_to" value="<?= htmlspecialchars(basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'homepage.php')), ENT_QUOTES, 'UTF-8') ?>">
                                                <?php if (strtolower((string) $account['role']) === 'admin' && $adminCount <= 1): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="At least one admin account must remain">Required</button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete account <?= (int) $account['id'] ?>">Delete</button>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php if (isset($_GET['account_deleted']) || isset($_GET['account_error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('accountsModal');
    if (modalElement && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }
});
</script>
<?php endif; ?>
