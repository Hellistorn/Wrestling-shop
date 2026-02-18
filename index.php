
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Minimal Wear</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
$products = [
    ["id" => 1, "name" => "Rashguard Black", "price" => 18000, "image" => "images/products/rashguard.jpg"],
    ["id" => 2, "name" => "ASICS MATBLAZER", "price" => 16000, "image" => "images/products/ASICS.jpg"],
    ["id" => 3, "name" => "Wrestling Hoodie", "price" => 22000, "image" => "images/products/hoodie.jpg"]
];
?>

<header class="header">
    <div class="nav-left">
        <a href="#">Главная</a>
        <a href="#">Каталог</a>
        <a href="#">Новинки</a>
        <a href="#">Контакты</a>
    </div>

    <div class="logo">
        WRESTSPARTAN
    </div>

    <div class="nav-right">
        <input type="text" placeholder="Поиск..." id="searchInput">
        <button class="icon-btn" onclick="openCart()">🛒</button>
    </div>
</header>


<section class="hero">
    <h1>BORN TO FIGHT</h1>
    <p>Профессиональная борцовская экипировка</p>
</section>

<section class="products">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <img src="<?= $product['image'] ?>" alt="">
            <h3><?= $product['name'] ?></h3>
            <p><?= $product['price'] ?> ₸</p>
            <button onclick="addToCart('<?= $product['name'] ?>', <?= $product['price'] ?>)">В корзину</button>
        </div>
    <?php endforeach; ?>
</section>

<section class="promo">
    <div class="promo-content">
        <h2>TRAIN HARD. FIGHT SMART.</h2>
        <p>Экипировка, созданная для настоящих бойцов.</p>
        <button class="promo-btn">Смотреть коллекцию</button>
    </div>
</section>

<div id="cart" class="cart">
    <h2>Корзина</h2>
    <ul id="cart-items"></ul>
    <p id="total"></p>
    <button onclick="closeCart()">Закрыть</button>
</div>

<script src="js/script.js"></script>

<footer class="footer">
    <div class="footer-container">

        <div class="footer-column">
            <h3>WRESTSPARTAN</h3>
            <p>Профессиональная экипировка для борьбы.</p>
        </div>

        <div class="footer-column">
            <h4>Контакты</h4>
            <p>Email: support@minimal.kz</p>
            <p>Телефон: +7 (777) 777-77-77</p>
            <p>Адрес: Алматы, Казахстан</p>
        </div>

        <div class="footer-column">
            <h4>Навигация</h4>
            <a href="#">Каталог</a>
            <a href="#">Новинки</a>
            <a href="#">О нас</a>
            <a href="#">Доставка</a>
        </div>

        <div class="footer-column">
            <h4>Социальные сети</h4>
            <a href="#">Instagram</a>
            <a href="#">Telegram</a>
            <a href="#">TikTok</a>
        </div>

    </div>

    <div class="footer-bottom">
        © 2026 WRESTSPARTAN. Все права защищены.
    </div>
</footer>
</body>
</html>
