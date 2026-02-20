<?php
// 1. Подключаем базу данных
include 'includes/db.php'; 

// 2. Получаем ТОЛЬКО новинки напрямую из базы
$result = $conn->query("SELECT * FROM products WHERE is_new = 1 ORDER BY id DESC LIMIT 4");

// Если запрос не прошел, создаем заглушку, чтобы не было ошибок
if (!$result) {
    $result = new stdClass();
    $result->num_rows = 0;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>WrestSpartan | Экипировка для чемпионов</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1>BORN TO FIGHT</h1>
        <p>Профессиональная борцовская экипировка</p>
        <a href="catalog.php" class="hero-btn">Смотреть каталог</a>
    </div>
</section>

<section class="new-arrivals" id="new-arrivals">
    <div class="section-title">
        <h2>НОВИНКИ</h2>
        <p>Свежая экипировка для тренировок и соревнований</p>
    </div>

    <div class="products">
    <?php 
    // Проверяем, есть ли новинки
    if ($result->num_rows > 0): 
        while ($product = $result->fetch_assoc()): 
    ?>
        <div class="product-card">
            <div class="badge-new">NEW</div>
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><?= number_format($product['price'], 0, '', ' ') ?> ₸</p>
            <button onclick="addToCart('<?= addslashes(htmlspecialchars($product['name'])) ?>', <?= $product['price'] ?>)">В корзину</button>
        </div>
    <?php 
        endwhile; 
    else: 
    ?>
        <p style="text-align: center; width: 100%; color: #888; padding: 40px 0;">Новинки скоро появятся!</p>
    <?php endif; ?>
    </div>
</section>

<section class="promo">
    <div class="promo-content">
        <h2>TRAIN HARD. FIGHT SMART.</h2>
        <p>Экипировка, созданная для настоящих бойцов.</p>
    </div>
</section>

<div id="cart" class="side-cart">
    <div class="cart-header">
        <h2>Ваша корзина</h2>
        <button class="close-cart-btn" onclick="closeCart()">✕</button>
    </div>
    <ul id="cart-items" class="cart-list"></ul>
    <div class="cart-footer">
        <div id="total">Итого: 0 ₸</div>
        <button onclick="openCheckoutForm()" class="checkout-btn">Перейти к оформлению</button>
        <button onclick="closeCart()" class="continue-btn">Продолжить покупки</button>
    </div>
</div>

<div id="checkout-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeCheckoutForm()">&times;</span>
        <h2>Оформление заказа</h2>
        <p class="kaspi-info">Оплата производится через Kaspi.kz после подтверждения</p>
        
        <form id="order-form" onsubmit="sendOrder(event)">
            <input type="text" id="fio" placeholder="ФИО полностью" required>
            <input type="tel" id="phone" placeholder="Номер телефона" required>
            <input type="text" id="city" placeholder="Город" required>
            <input type="text" id="address" placeholder="Адрес (улица, дом, квартира)" required>
            <input type="text" id="zip" placeholder="Почтовый индекс" required>
            
            <div class="order-summary">
                <p>К оплате: <span id="final-price">0</span> ₸</p>
                <p class="bank-detail">Банк: <b>Kaspi Bank</b></p>
            </div>

            <div class="checkout-instruction">
                <h4>Что делать дальше?</h4>
                <ul>
                    <li>Нажмите кнопку <b>«Подтвердить и оплатить»</b> ниже.</li>
                    <li>Вас перенаправит в WhatsApp с готовым текстом заказа.</li>
                    <li>Отправьте это сообщение менеджеру.</li>
                    <li>Прикрепите <b>чек об оплате Kaspi</b> прямо в чат.</li>
                </ul>
            </div>
            
            <button type="submit" class="confirm-btn">Подтвердить и оплатить</button>
        </form>
    </div>
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
            <p>Email: support@wrestspartan.kz</p>
            <p>Телефон: +7 (777) 777-77-77</p>
        </div>
        <div class="footer-column">
            <h4>Навигация</h4>
            <a href="catalog.php">Каталог</a>
            <a href="#new-arrivals">Новинки</a>
        </div>
        <div class="footer-column">
            <h4>Социальные сети</h4>
            <a href="#">Instagram</a>
            <a href="#">Telegram</a>
        </div>
    </div>
    <div class="footer-bottom">
        © 2026 WRESTSPARTAN. Все права защищены.
    </div>
</footer>

</body>
</html>