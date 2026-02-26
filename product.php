<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'includes/db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id === 0) { header("Location: catalog.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) { die("Товар не найден."); }

$img_stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$img_stmt->bind_param("i", $product_id);
$img_stmt->execute();
$extra_images = $img_stmt->get_result();

$related_stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$related_stmt->bind_param("si", $product['category'], $product_id);
$related_stmt->execute();
$related_products = $related_stmt->get_result();

$is_fallback = false;
if ($related_products->num_rows === 0) {
    $related_products = $conn->query("SELECT * FROM products WHERE id != $product_id ORDER BY RAND() LIMIT 4");
    $is_fallback = true;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> | WrestSpartan</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #ff0000; --bg: #050505; --card-bg: #111111; }
        
        /* Базовые настройки страницы */
        html, body { 
            background: var(--bg); 
            color: #fff; 
            font-family: 'Oswald', sans-serif; 
            margin: 0; 
            padding: 0;
            width: 100%;
            min-height: 100vh;
        }

        /* Контейнер страницы */
        .product-page { 
            max-width: 1400px; 
            margin: 40px auto; 
            padding: 0 20px; 
            display: grid; 
            grid-template-columns: 1.2fr 0.8fr; 
            gap: 60px; 
            align-items: start;
        }

        /* Сетка изображений */
        .product-image-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .product-image-grid img { width: 100%; display: block; border: 1px solid #111; object-fit: cover; }
        .product-image-grid img:first-child { grid-column: 1 / span 2; }

        /* Описание товара */
        .product-description {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #222;
            font-family: 'Roboto', sans-serif;
            line-height: 1.8;
            color: #ddd;
        }
        .product-description h3 {
            color: #fff;
            text-transform: uppercase;
            font-family: 'Oswald';
            margin-bottom: 15px;
        }

        /* Инфо-блок (правый) */
        .product-info { position: sticky; top: 100px; }
        .product-info h1 { font-size: 3rem; text-transform: uppercase; margin: 0; line-height: 1.1; }
        .price-big { font-size: 2.5rem; color: var(--accent); font-weight: 700; margin: 20px 0; }
        
        .size-selection { margin: 30px 0; }
        .size-wrapper { display: flex; flex-wrap: wrap; gap: 8px; }
        
        .size-btn { 
            min-width: 60px; height: 50px;
            border: 1px solid #333; background: transparent; color: #fff; 
            cursor: pointer; transition: 0.2s; font-family: 'Oswald'; font-size: 1rem;
        }
        .size-btn.active { background: #fff; color: #000; border-color: #fff; }

        .btn-main { 
            background: #222; color: #fff; border: none; padding: 25px; 
            font-weight: bold; text-transform: uppercase; cursor: pointer; 
            font-size: 1.2rem; transition: 0.3s; width: 100%; letter-spacing: 2px;
        }
        .btn-main.ready { background: var(--accent); }
        
        .details-box { color: #888; font-family: 'Roboto'; font-size: 0.9rem; margin-top: 30px; border-top: 1px solid #222; padding-top: 20px; }

        /* Похожие товары */
        .related-section { max-width: 1400px; margin: 80px auto; padding: 0 20px; border-top: 1px solid #222; padding-top: 40px; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .related-card { text-decoration: none; color: white; background: #111; padding: 15px; border: 1px solid #222; transition: 0.3s; }
        .related-card img { width: 100%; height: 300px; object-fit: cover; margin-bottom: 15px; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="product-page">
    <div>
        <div class="product-image-grid">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php while($img = $extra_images->fetch_assoc()): ?>
                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Вид товара">
            <?php endwhile; ?>
        </div>

        <?php if(!empty($product['description'])): ?>
        <div class="product-description">
            <h3 style="text-transform: uppercase; margin-bottom: 30px;">Описание товара</h3>
            <div><?= nl2br(htmlspecialchars($product['description'])) ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="product-info">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <div class="price-big"><?= number_format($product['price'], 0, '', ' ') ?> ₸</div>
        
        <div class="size-selection">
            <h4>Выберите размер:</h4>
            <div class="size-wrapper">
                <?php 
                $sizes = explode(',', $product['sizes'] ?? ''); 
                foreach($sizes as $size): 
                    $size = trim($size);
                    if(!empty($size)):
                ?>
                    <button class="size-btn" onclick="selectSize(this, '<?= $size ?>')"><?= $size ?></button>
                <?php endif; endforeach; ?>
            </div>
        </div>

        <input type="hidden" id="selected-size" value="">
        <button id="add-to-cart-btn" class="btn-main" onclick="handleAddToCart()">Добавить в корзину</button>

        <div class="details-box">
            <p><strong>Бесплатная доставка</strong> при заказе от 50 000 ₸</p>
            <p>Наличие: <?= $product['stock'] > 0 ? 'В наличии' : 'Под заказ' ?></p>
        </div>
    </div>
</div>

<div class="related-section">
    <h2 style="text-transform: uppercase; margin-bottom: 30px;"><?= $is_fallback ? 'Рекомендуем также' : 'Похожие модели' ?></h2>
    <div class="related-grid">
        <?php while($item = $related_products->fetch_assoc()): ?>
            <a href="product.php?id=<?= $item['id'] ?>" class="related-card">
                <img src="<?= htmlspecialchars($item['image']) ?>">
                <h4 style="margin: 0; text-transform: uppercase; font-size: 1rem;"><?= htmlspecialchars($item['name']) ?></h4>
                <div style="color: var(--accent); font-weight: bold; margin-top: 10px;"><?= number_format($item['price'], 0, '', ' ') ?> ₸</div>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<script src="js/script.js"></script>
<script>
let currentSize = "";
function selectSize(element, size) {
    document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    currentSize = size;
    document.getElementById('selected-size').value = size;
    document.getElementById('add-to-cart-btn').classList.add('ready');
}

function handleAddToCart() {
    if (!currentSize) { alert("Пожалуйста, выберите размер!"); return; }
    const productName = "<?= addslashes($product['name']) ?> (Размер: " + currentSize + ")";
    const productPrice = <?= $product['price'] ?>;
    if (typeof addToCart === "function") { 
        addToCart(productName, productPrice); 
    }
}
</script>

</body>
</html>