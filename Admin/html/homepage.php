<?php
require_once '../../global/auth.php';
if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(empty($_SESSION['username']) ? 401 : 403);
    header('Location: ../../login_page/html/login.php');
    exit('You do not have permission to access this page.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Dashboard</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <script src="../../bootstrap_template/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <a class="brand" href="homepage.php">
                <span class="brand-logo">A</span>
                <span>Aquatica</span>
            </a>

            <p class="menu-title">MAIN MENU</p>
            <nav class="nav flex-column" aria-label="Main navigation">
                <a class="nav-link active" href="homepage.php">Dashboard</a>
                <a class="nav-link" href="inventory.php">Inventory</a>
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
        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="welcome-label">Nasugbu Aquatica Dashboard</p>
                    <h1>Admin / Owner Panel</h1>
                </div>
            </header>

            <section class="summary-section">
                <div class="row g-3">
                    <!-- Weekly Sales -->
                    <div class="col-12 col-md-4">
                        <article class="summary-card">
                            <div class="stat-icon blue">&#128200;</div>
                            <p>Weekly Sales</p>
                            <div class="stat-bottom"><strong>&#8369;0</strong><span class="stat-change">+0%</span></div>
                            <small>compared to last week</small>
                        </article>
                    </div>
                    <!-- Monthly Sales -->
                    <div class="col-12 col-md-4">
                        <article class="summary-card">
                            <div class="stat-icon yellow">&#128197;</div>
                            <p>Monthly Sales</p>
                            <div class="stat-bottom"><strong>&#8369;0</strong><span class="stat-change">+0%</span></div>
                            <small>compared to last month</small>
                        </article>
                    </div>
                    <!-- Tank Condition -->
                    <div class="col-12 col-md-4">
                        <article class="summary-card">
                            <div class="stat-icon mint" aria-hidden="true">&#128167;</div>
                            <p>Tank Condition</p>
                            <div class="stat-bottom"><strong>Normal</strong></div>
                            <small>Based on sample water readings</small>
                        </article>
                    </div>
                </div>
            </section>

            <div class="row g-3 lower-section">
                <div class="col-lg-7">
                    <section class="content-card sales-card">
                        <div class="card-heading">
                            <div>
                                <h2>Weekly Sales</h2>
                                <p>Your sales over the past 7 days</p>
                            </div>
                        </div>
                        <div class="chart">
                            <div class="chart-line"><span>&#8369;800</span></div>
                            <div class="chart-line"><span>&#8369;600</span></div>
                            <div class="chart-line"><span>&#8369;400</span></div>
                            <div class="chart-line"><span>&#8369;200</span></div>
                            <div class="chart-bars">
                                <div class="bar-item">
                                    <div class="bar" style="height: 45%"></div><span>Mon</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 70%"></div><span>Tue</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 55%"></div><span>Wed</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 85%"></div><span>Thu</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 65%"></div><span>Fri</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 95%"></div><span>Sat</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 75%"></div><span>Sun</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-lg-5">
                    <section class="content-card activity-card">
                        <div class="card-heading">
                            <div>
                                <h2>Monthly Sales</h2>
                                <p>Your sales breakdown this month</p>
                            </div>
                        </div>
                        <div class="chart">
                            <div class="chart-line"><span>&#8369;800</span></div>
                            <div class="chart-line"><span>&#8369;600</span></div>
                            <div class="chart-line"><span>&#8369;400</span></div>
                            <div class="chart-line"><span>&#8369;200</span></div>
                            <div class="chart-bars">
                                <div class="bar-item">
                                    <div class="bar" style="height: 60%"></div><span>Wk 1</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 80%"></div><span>Wk 2</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 50%"></div><span>Wk 3</span>
                                </div>
                                <div class="bar-item">
                                    <div class="bar" style="height: 90%"></div><span>Wk 4</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
<?php require 'manage-accounts.php'; ?>
</body>

</html>
