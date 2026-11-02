<aside class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-calendar-alt"></i> EventHub</h2>
        <p>User Panel</p>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="events.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='events.php'?'active':'' ?>">
            <i class="fas fa-calendar-alt"></i> Browse Events
        </a>
        <a href="my_registrations.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='my_registrations.php'?'active':'' ?>">
            <i class="fas fa-ticket-alt"></i> My Registrations
        </a>
        <a href="../logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>