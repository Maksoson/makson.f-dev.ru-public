<header class="header">
    <div class="head">
        <div class="logo"><i class="fas fa-blog fa-3x"></i></div>
        <div class="menu">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/Blog/">Posts</a></li>
            </ul>
            <div class="log">
                <ul>
                    <?php if (!isset($_SESSION['session_username'])) : ?>
                        <li class="in"><a href="/labs/kursach/autorization/">Login</a></li>
                    <?php else: ?>
                        <li class="out"><a class="logout">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</header>