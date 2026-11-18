<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatica | Device Monitoring</title>
    <link rel="stylesheet" href="../../bootstrap_template/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="../css/device.css">
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
                <a class="nav-link" href="homepage.php">Dashboard</a>
                <a class="nav-link" href="inventory.php">Inventory</a>
                <a class="nav-link active" href="device.php" aria-current="page">Device Monitoring</a>
                <a class="nav-link create-account-nav-link" href="../../login_page/html/signup.php">Create account</a>
            </nav>
            <a class="sidebar-logout" href="../../login_page/html/login.php">Logout</a>
            <div class="sidebar-workspace">
                <span class="sidebar-status-dot" aria-hidden="true"></span>
                <span><strong>Nasugbu Aquatica</strong><small>Admin workspace</small></span>
            </div>
        </aside>

        <main class="main-content device-content">
            <div class="device-breadcrumb d-flex align-items-center justify-content-between">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="homepage.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Device monitoring</li>
                    </ol>
                </nav>
                <span class="device-location"><span aria-hidden="true"></span>Nasugbu Aquatica</span>
            </div>

            <header class="device-header">
                <div>
                    <p class="welcome-label">DEVICE MONITORING</p>
                    <h1>Water monitoring</h1>
                    <p class="page-subtitle">Current water and room measurements.</p>
                </div>
                <div class="device-status"><span></span> Sample readings</div>
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
                        <div class="measurement-note"><span class="measurement-note-icon" aria-hidden="true">&#9678;</span><span><small>Reading
                                    type</small><strong>Ambient air</strong></span></div>
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
                        <div class="measurement-note"><span class="measurement-note-icon" aria-hidden="true">&#8596;</span><span><small>Example
                                    range</small><strong>24 &ndash; 28 &#176;C</strong></span></div>
                    </article>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
