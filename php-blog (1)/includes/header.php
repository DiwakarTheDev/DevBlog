<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">Dev Blog</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="admin/index.php">Admin</a></li>
                        <li><a href="admin/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="admin/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <button class="menu-toggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

