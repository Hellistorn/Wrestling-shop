<?php
include 'includes/db.php';

// 1. ПОЛУЧАЕМ ВСЕ КАТЕГОРИИ
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$category = $_GET['category'] ?? null;

// 2. ФИЛЬТРАЦИЯ
if ($category) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог | WrestSpartan</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --accent: #ff0000;
            --bg: #050505;
            --card-bg: #111111;
            --text-main: #ffffff;
            --text-dim: #888888;
        }

        body { background-color: var(--bg); color: var(--text-main); font-family: 'Oswald', sans-serif; margin: 0; }
        
        .catalog-hero { position: relative; padding: 120px 20px; text-align: center; background: linear-gradient(45deg, rgba(0,0,0,0.9) 30%, rgba(225,6,0,0.3)), url('images/hero-bg.jpg') center/cover; border-bottom: 2px solid var(--accent); }
        .catalog-hero h1 { font-size: clamp(2rem, 8vw, 4.5rem); color: #fff; text-transform: uppercase; margin: 0; letter-spacing: 5px; }
        
        .filters { display: flex; justify-content: center; padding: 30px; gap: 10px; background: #000; flex-wrap: wrap; }
        .filters a { padding: 10px 20px; color: var(--text-dim); text-decoration: none; border: 1px solid #222; transition: 0.3s; font-weight: bold; text-transform: uppercase; }
        .filters a:hover, .filters a.active { color: #fff; border-color: var(--accent); background: rgba(255,0,0,0.1); }
        
        .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; max-width: 1300px; margin: 40px auto; padding: 0 20px; }
        .catalog-card { background: var(--card-bg); border: 1px solid #1a1a1a; transition: 0.4s; position: relative; overflow: hidden; }
        .catalog-card:hover { transform: translateY(-5px); border-color: var(--accent); }
        
        /* Стили для ссылок и изображений */
        .image-link-wrapper { display: block; width: 100%; height: 300px; overflow: hidden; }
        .catalog-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .catalog-card:hover img { transform: scale(1.05); }
        
        .card-content { padding: 20px; text-align: center; }
        .product-title-link { text-decoration: none; color: #fff; display: block; margin-bottom: 10px; }
        .catalog-card h3 { font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .catalog-card:hover h3 { color: var(--accent); }
        
        .catalog-card .price { font-size: 1.4rem; color: var(--accent); font-weight: 700; margin-bottom: 15px; }
        
        .btn-buy { width: 100%; background: transparent; border: 1px solid #333; color: #fff; padding: 12px; cursor: pointer; font-weight: bold; text-transform: uppercase; transition: 0.3s; font-family: 'Oswald'; }
        .btn-buy:hover { background: var(--accent); border-color: var(--accent); }

        /* Мобильная версия */
        @media (max-width: 768px) {
            .header { display: flex !important; flex-direction: column !important; align-items: center !important; height: auto !important; padding: 15px !important; }
            .catalog-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; padding: 0 10px !important; }
            .image-link-wrapper { height: 180px !important; }
            .catalog-card h3 { font-size: 0.9rem !important; }
            .catalog-card .price { font-size: 1.1rem !important; }
        }

        .side-cart, #cart { z-index: 9999 !important; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="catalog-hero">
    <h1>ЭКИПИРОВКА ЧЕМПИОНОВ</h1>
</section>

<section class="filters">
    <a href="catalog.php" class="<?= !$category ? 'active' : '' ?>">Все</a>
    <?php while($cat_row = $cat_result->fetch_assoc()): ?>
        <a href="catalog.php?category=<?= urlencode($cat_row['name']) ?>" 
           class="<?= $category == $cat_row['name'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat_row['name']) ?>
        </a>
    <?php endwhile; ?>
</section>

<section class="catalog-grid">
<?php if($result && $result->num_rows > 0): ?>
    <?php while($product = $result->fetch_assoc()): ?>
        <div class="catalog-card">
            <a href="product.php?id=<?= $product['id'] ?>" class="image-link-wrapper">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </a>
            
<div class="card-content">
    <a href="product.php?id=<?= $product['id'] ?>" class="product-title-link">
        <h3><?= htmlspecialchars($product['name']) ?></h3>
    </a>
    <div class="price"><?= number_format($product['price'], 0, '', ' ') ?> ₸</div>
    </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div style="text-align: center; grid-column: 1/-1; padding: 50px;">
        <h2 style="color: #444;">В этой категории пока нет товаров</h2>
    </div>
<?php endif; ?>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>