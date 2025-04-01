<header class="admin-header">
    <div class="admin-header-content">
        <div class="admin-logo">
            <a href="index.php">Dev Blog Admin</a>
        </div>
        <div class="admin-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>
</header>

