<?php
include 'includes/db.php';

// 1. ПОЛУЧАЕМ ВСЕ КАТЕГОРИИ ИЗ БАЗЫ ДАННЫХ
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// 2. ЛОГИКА ФИЛЬТРАЦИИ ТОВАРОВ
$category = $_GET['category'] ?? null;

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
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #ff0000;
            --bg: #050505;
            --card-bg: #111111;
            --text-main: #ffffff;
            --text-dim: #888888;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: 'Oswald', sans-serif;
            margin: 0;
        }

        .catalog-hero {
            position: relative;
            padding: 120px 20px;
            text-align: center;
            background: linear-gradient(45deg, rgba(0,0,0,0.9) 30%, rgba(225,6,0,0.3)), 
                        url('images/hero-bg.jpg') center/cover;
            border-bottom: 2px solid var(--accent);
        }

        .catalog-hero h1 {
            font-size: clamp(2rem, 8vw, 4.5rem);
            color: #fff;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 5px;
            text-shadow: 0 5px 15px rgba(0,0,0,0.8), 0 0 20px rgba(255,0,0,0.4);
        }

        /* ФИЛЬТРЫ ТЕПЕРЬ ДИНАМИЧЕСКИЕ */
        .filters {
            display: flex;
            justify-content: center;
            padding: 30px;
            gap: 10px;
            background: #000;
            flex-wrap: wrap; /* Чтобы кнопки не вылезали на мобилках */
        }

        .filters a {
            padding: 10px 20px;
            color: var(--text-dim);
            text-decoration: none;
            border: 1px solid #222;
            transition: 0.3s;
            font-weight: bold;
            text-transform: uppercase;
        }

        .filters a:hover, .filters a.active {
            color: #fff;
            border-color: var(--accent);
            background: rgba(255,0,0,0.1);
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
            max-width: 1300px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .catalog-card {
            background: var(--card-bg);
            border: 1px solid #1a1a1a;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .catalog-card:hover {
            transform: scale(1.03);
            border-color: var(--accent);
            box-shadow: 0 10px 30px rgba(255,0,0,0.15);
        }

        .catalog-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 1px solid #1a1a1a;
        }

        .card-content { padding: 20px; }

        .catalog-card h3 { font-size: 1.2rem; margin: 0 0 10px; letter-spacing: 1px; }

        .catalog-card .price {
            font-size: 1.5rem;
            color: var(--accent);
            font-weight: 700;
            margin-bottom: 20px;
        }

        .btn-buy {
            width: 100%;
            background: transparent;
            border: 1px solid #333;
            color: #fff;
            padding: 12px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-buy:hover { background: var(--accent); border-color: var(--accent); color: #fff; }
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
<?php if($result->num_rows > 0): ?>
    <?php while($product = $result->fetch_assoc()): ?>
        <div class="catalog-card">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="card-content">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <div class="price"><?= number_format($product['price'], 0, '', ' ') ?> ₸</div>
                <button class="btn-buy">Добавить в корзину</button>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div style="text-align: center; grid-column: 1/-1; padding: 50px;">
        <h2 style="color: #444;">В этой категории пока нет товаров</h2>
    </div>
<?php endif; ?>
</section>

</body>
</html>