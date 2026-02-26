<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) { header("Location: index.php"); exit; }
include 'includes/db.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $sizes = $_POST['sizes'];
    $stock = intval($_POST['stock']); // НОВОЕ: количество
    $is_new = isset($_POST['is_new']) ? 1 : 0; 
    
    $image_path = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $new_image = "images/products/" . time() . "_" . $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], $new_image)) {
            if (file_exists($product['image'])) { unlink($product['image']); }
            $image_path = $new_image;
        }
    }

    // Обновили SQL: добавили stock=?
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image=?, category=?, is_new=?, sizes=?, stock=? WHERE id=?");
    
    // Типы: sdssisii (последние две 'i' это stock и id)
    $stmt->bind_param("sdssisii", $name, $price, $image_path, $category, $is_new, $sizes, $stock, $id);
    
    if ($stmt->execute()) {
        header("Location: admin.php?success=updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать товар</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container { max-width: 600px; margin: 50px auto; background: #222; padding: 30px; border-radius: 15px; color: white; font-family: sans-serif; }
        input, select { width: 100%; padding: 12px; margin: 10px 0; background: #111; border: 1px solid #444; color: white; border-radius: 5px; box-sizing: border-box; }
        .btn-save { background: #ffca28; color: black; border: none; padding: 12px 20px; font-weight: bold; cursor: pointer; width: 100%; border-radius: 5px; margin-top: 10px; }
        label { color: #aaa; font-size: 0.9rem; margin-top: 10px; display: block; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; margin: 15px 0; cursor: pointer; color: #ffca28; }
        .checkbox-group input { width: auto; margin: 0; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Редактирование товара #<?php echo $id; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            
            <label>Название товара:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Цена (₸):</label>
                    <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
                <div style="flex: 1;">
                    <label>В наличии (шт):</label>
                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
                </div>
            </div>

            <label>Размеры (через запятую):</label>
            <input type="text" name="sizes" value="<?php echo htmlspecialchars($product['sizes']); ?>" placeholder="S, M, L, XL">

            <label>Категория:</label>
            <select name="category">
                <option value="T-Shirts" <?php if($product['category'] == 'T-Shirts') echo 'selected'; ?>>Футболки</option>
                <option value="Singlets" <?php if($product['category'] == 'Singlets') echo 'selected'; ?>>Трико</option>
                <option value="Shoes" <?php if($product['category'] == 'Shoes') echo 'selected'; ?>>Борцовки</option>
            </select>

            <label class="checkbox-group">
                <input type="checkbox" name="is_new" value="1" <?php if($product['is_new']) echo 'checked'; ?>>
                Отметить как «Новинку»
            </label>

            <div style="margin: 20px 0;">
                <p style="margin-bottom: 5px;">Текущее фото:</p>
                <img src="<?php echo $product['image']; ?>" width="100" style="border-radius: 5px; border: 1px solid #444;">
            </div>

            <label>Заменить фото:</label>
            <input type="file" name="image">

            <button type="submit" class="btn-save">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
            <a href="admin.php" style="display:block; text-align:center; color: #aaa; margin-top:15px; text-decoration: none;">Отмена</a>
        </form>
    </div>
</body>
</html>