<aside class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-calendar-alt"></i> EventHub</h2>
        <p>Admin Panel</p>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="events.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
        <a href="add_event.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'add_event.php' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i> Add Event
        </a>
        <a href="users.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="../logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>