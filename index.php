<?php 
// 1. Самым первым делом запускаем сессию
session_start(); 

// 2. Подключаем базу данных
include 'includes/db.php'; 

// 3. Подключаем шапку
include 'includes/header.php'; 

// Делаем запрос в базу: выбираем новинки
$query = "SELECT * FROM products WHERE is_new = 1 ORDER BY id DESC";
$result = $conn->query($query); 

if (!$result) {
    die("Ошибка запроса: " . $conn->error);
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
    if ($result->num_rows > 0): 
        while ($product = $result->fetch_assoc()): 
    ?>
        <a href="product.php?id=<?= $product['id'] ?>" class="product-card">
            <div class="badge-new">NEW</div>
            <div class="img-container">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p class="price"><?= number_format($product['price'], 0, '', ' ') ?> ₸</p>
        </a>
    <?php 
        endwhile; 
    else: 
    ?>
        <p class="no-news">Новинки скоро появятся!</p>
    <?php endif; ?>
    </div>
</section>

<section class="promo">
    <div class="promo-content">
        <h2>TRAIN HARD. FIGHT SMART.</h2>
        <p>Экипировка, созданная для настоящих бойцов.</p>
    </div>
</section>

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
            <a href="https://www.instagram.com/wrestspartan?igsh=MWRwcnMyZjNzZ2w1cw%3D%3D&utm_source=qr">Instagram</a>
            <a href="#">Telegram</a>
        </div>
    </div>
    <div class="footer-bottom">
        © 2026 WRESTSPARTAN. Все права защищены.
    </div>
</footer>

</body>
</html>