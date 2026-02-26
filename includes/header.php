<?php 
// Сессия должна стартовать в самом верху, до любого вывода текста
if (session_status() === PHP_SESSION_NONE) { session_start(); } 
?>
<header class="header">
    <div class="nav-left">
        <a href="index.php">Главная</a>
        <a href="catalog.php">Каталог</a>
        <a href="index.php#new-arrivals">Новинки</a>
    </div>

    <div class="logo">
        WRESTSPARTAN
    </div>

    <div class="nav-right">
        <button class="icon-btn" onclick="openCart()">🛒</button>
        
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <a href="admin.php" class="admin-btn">+</a>
            <a href="logout.php" style="color: #888; font-size: 12px; margin-left: 10px;">Выйти</a>
        <?php endif; ?>
    </div>
</header>

<?php include 'includes/cart_ui.php'; ?>