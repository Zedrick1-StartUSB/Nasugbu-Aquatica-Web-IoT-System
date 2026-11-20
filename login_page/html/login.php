<?php
require_once '../../global/auth.php';
if (!empty($_SESSION['username']) && strtolower((string) ($_SESSION['role'] ?? '')) === 'admin') {
    header('Location: ../../Admin/html/homepage.php');
    exit;
}
if (!empty($_SESSION['username']) && strtolower((string) ($_SESSION['role'] ?? '')) === 'staff') {
    header('Location: ../../Staff/html/staff.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="login-card card shadow-sm border p-4 p-md-4">
            <div class="card-body">
                <!-- Logo & Heading -->
                <div class="text-center mb-4">
                    <img src="../../business_adds/blogo.jpg" alt="Aquatica Logo" class="login-logo mb-3"
                        onerror="this.style.display='none'">
                    <h3 class="fw-bold text-primary mb-1">Aquatica System</h3>
                    <p class="text-muted small">Live Animal Business Management System</p>
                </div>

                <!-- Login Form -->
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
                    <div class="alert alert-danger" role="alert">Invalid username or password. Please try again.</div>
                <?php endif; ?>
                <form method="POST" action="../auth.php" id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold small">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Enter username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100 py-2" id="loginbtn">Log in</button>
                </form>
            </div>

        </div>
    </div>

    <!-- Scripts -->
</body>

</html>
