<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="topbar-right">
        <div class="user-menu">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['admin_name'], 0, 1)) ?>
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
    </div>
</header>