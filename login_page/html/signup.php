<?php
require_once '../../global/auth.php';
if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(empty($_SESSION['username']) ? 401 : 403);
    header('Location: login.php');
    exit('Only an administrator can create accounts.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Create Account</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/signup.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="signup-card card shadow-sm border w-100" style="max-width: 820px;">
            <div class="row g-0">

                <!-- Left Panel: Logo & Business Info -->
                <div class="col-md-6 left-panel d-flex flex-column align-items-center justify-content-center text-center p-4">
                    <img src="../../business_adds/blogo.jpg" alt="Aquatica Logo" class="signup-logo mb-3" onerror="this.style.display='none'">
                    <h4 class="fw-bold text-white mb-1">Aquatica System</h4>
                    <p class="text-white small mb-4">Live Animal Business Management System</p>
                    <hr class="border-white w-50 opacity-25">
                    <p class="text-white small mt-3 px-2">Create your account to start managing your business operations.</p>
                </div>

                <!-- Right Panel: Signup Form -->
                <div class="col-md-6 p-4">
                    <div class="mb-3">
                        <h5 class="fw-bold mb-0">Create an Account</h5>
                        <p class="text-muted small">Fill in the details below to register.</p>
                    </div>

                    <?php if (isset($_GET['created'])): ?>
                        <div class="alert alert-success" role="status">Account created successfully.</div>
                    <?php endif; ?>

                    <form action="../auth.php" method="POST" id="signupForm">
                        <input type="hidden" name="signup" value="1">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold small">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter a username" autocomplete="username" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" for="role">Business Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" autocomplete="new-password" required>
                        </div>

                        <div class="mb-4">
                            <label for="confirm-password" class="form-label fw-bold small">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Re-enter your password" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" id="signupbtn">Create Account</button>
                    </form>

                    <a href="login.php" class="btn btn-outline-primary w-100 mt-3">Back to Login</a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
