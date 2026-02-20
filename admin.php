<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}
include 'includes/db.php';

// Получаем все категории для формы
$all_categories = $conn->query("SELECT * FROM categories");

// Логика удаления товара
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT image FROM products WHERE id = $id");
    $item = $res->fetch_assoc();
    if ($item && file_exists($item['image'])) {
        unlink($item['image']); 
    }
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель | WrestSpartan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { max-width: 1000px; margin: 50px auto; padding: 20px; background: #1c1c1c; color: white; border-radius: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        th { color: #e10600; text-transform: uppercase; font-size: 0.8rem; }
        .product-img-mini { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        
        .btn-delete { color: #ff4d4d; text-decoration: none; font-weight: bold; }
        .btn-edit { color: #ffca28; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .btn-manage-cat { color: #aaa; text-decoration: underline; font-size: 0.9rem; float: right; }
        
        .add-form { background: #252525; padding: 20px; border-radius: 10px; margin-bottom: 40px; }
        .add-form input, .add-form select { width: 100%; padding: 10px; margin: 10px 0; background: #111; border: 1px solid #444; color: white; border-radius: 5px; }
        .btn-add { background: #25d366; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
    </style>
</head>
<body>

<div class="admin-container">
    <h1>Управление магазином</h1>
    <div style="margin-bottom: 20px;">
        <a href="index.php" style="color: #aaa; text-decoration: none;">← На сайт</a>
        <a href="manage_categories.php" class="btn-manage-cat">Управление категориями</a>
    </div>

    <div class="add-form">
        <h3>Добавить новый товар</h3>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Название товара" required>
            <input type="number" name="price" placeholder="Цена (₸)" required>
            
            <select name="category" required>
                <option value="" disabled selected>Выберите категорию</option>
                <?php while($cat = $all_categories->fetch_assoc()): ?>
                    <option value="<?= $cat['name'] ?>"><?= $cat['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <div style="margin: 10px 0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #ffca28; font-weight: bold;">
                    <input type="checkbox" name="is_new" value="1" style="width: 20px; height: 20px; cursor: pointer;"> 
                    Отметить как "Новинку" (появится на главной)
                </label>
            </div>
            <input type="file" name="image" required>
            <button type="submit" class="btn-add">Опубликовать товар</button>
        </form>
    </div>

    <h3>Список товаров</h3>
    <table>
        <thead>
            <tr>
                <th>Фото</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Категория</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
            while($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><img src="<?= $row['image'] ?>" class="product-img-mini"></td>
                <td><?= $row['name'] ?></td>
                <td><?= number_format($row['price'], 0, '', ' ') ?> ₸</td>
                <td><?= $row['category'] ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn-edit">Изменить</a>
                    <a href="admin.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Удалить этот товар?')">Удалить</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>