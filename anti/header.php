

<header>
    <nav>
        <button class="image-button">
            <img src="menu.png" alt="">
        </button>
        <ul class="nav-links">
            <li><a href="index.php">Главная</a></li>
            <li><a href="merch.php">Мерч</a></li>
            <li><a href="corzina.php">Корзина</a></li>
        </ul>
    </nav>
</header>
<div class="nav-bar">
    <div>
        <ul>
            <li><a href="index.php" class="nav-link">Главная</a></li>
            <li><a href="soo.php" class="nav-link">Сообществa</a></li>
            <li><a href="merch.php" class="nav-link">Мерч</a></li>
            <li><?php if (!isset($_SESSION['user_id'])) : ?>
                <a href="login.php">Войти</a>
                <?php endif; ?></li>
        </ul>
    </div>
    <div class="nav-bar-r">
        <ul>
            <li><a href="corzina.php" class="nav-link">Корзина</a></li>
        </ul>
    </div>
</div>

<div id="content">

</div>


