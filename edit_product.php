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
    
    // По умолчанию оставляем старое фото
    $image_path = $product['image'];

    // Если загружено новое фото
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $new_image = "images/products/" . time() . "_" . $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], $new_image)) {
            // Удаляем старое фото с диска, чтобы не копился мусор
            if (file_exists($product['image'])) { unlink($product['image']); }
            $image_path = $new_image;
        }
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image=?, category=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $image_path, $category, $id);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
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
        .edit-container { max-width: 600px; margin: 50px auto; background: #222; padding: 30px; border-radius: 15px; color: white; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; background: #111; border: 1px solid #444; color: white; }
        .btn-save { background: #ffca28; color: black; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Редактирование товара #<?php echo $id; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
            <select name="category">
                <option value="T-Shirts" <?php if($product['category'] == 'T-Shirts') echo 'selected'; ?>>Футболки</option>
                <option value="Singlets" <?php if($product['category'] == 'Singlets') echo 'selected'; ?>>Трио</option>
                <option value="Shoes" <?php if($product['category'] == 'Shoes') echo 'selected'; ?>>Борцовки</option>
            </select>
            <p>Текущее фото: <img src="<?php echo $product['image']; ?>" width="50"></p>
            <label>Заменить фото (необязательно):</label>
            <input type="file" name="image">
            <button type="submit" class="btn-save">Сохранить изменения</button>
            <a href="admin.php" style="display:block; text-align:center; color: #aaa; margin-top:15px;">Отмена</a>
        </form>
    </div>
</body>
</html>