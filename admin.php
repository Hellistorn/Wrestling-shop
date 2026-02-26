<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}
include 'includes/db.php';

// Получаем все категории для формы
$all_categories = $conn->query("SELECT * FROM categories");

// ЛОГИКА УДАЛЕНИЯ ТОВАРА
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // 1. Удаляем дополнительные изображения с сервера
    $extra_res = $conn->query("SELECT image_path FROM product_images WHERE product_id = $id");
    while($img = $extra_res->fetch_assoc()) { 
        if(!empty($img['image_path']) && file_exists($img['image_path'])) {
            unlink($img['image_path']); 
        }
    }
    
    // 2. Удаляем основное изображение с сервера
    $res = $conn->query("SELECT image FROM products WHERE id = $id");
    $item = $res->fetch_assoc();
    if ($item && !empty($item['image']) && file_exists($item['image'])) {
        unlink($item['image']); 
    }
    
    // 3. Удаляем записи из БД
    $conn->query("DELETE FROM products WHERE id = $id");
    
    header("Location: admin.php?msg=deleted");
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
        .admin-container { max-width: 1100px; margin: 50px auto; padding: 20px; background: #1c1c1c; color: white; border-radius: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        th { color: #e10600; text-transform: uppercase; font-size: 0.8rem; }
        .product-img-mini { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        
        .btn-delete { color: #ff4d4d; text-decoration: none; font-weight: bold; }
        .btn-edit { color: #ffca28; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .btn-manage-cat { color: #aaa; text-decoration: underline; font-size: 0.9rem; float: right; }
        
        .add-form { background: #252525; padding: 20px; border-radius: 10px; margin-bottom: 40px; }
        .add-form input, .add-form select, .add-form textarea { width: 100%; padding: 10px; margin: 10px 0; background: #111; border: 1px solid #444; color: white; border-radius: 5px; box-sizing: border-box; font-family: inherit; }
        .btn-add { background: #25d366; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 10px; }
        
        .stock-badge { padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .stock-in { background: #1b5e20; color: #81c784; }
        .stock-out { background: #4a3b00; color: #ffca28; }

        label { display: block; margin-top: 10px; color: #aaa; font-size: 0.85rem; }
        .file-input-group { background: #111; border: 1px dashed #444; padding: 15px; border-radius: 5px; margin: 10px 0; }
        
        .alert-success { background: #1b5e20; color: #fff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="admin-container">
    <h1>Управление магазином</h1>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert-success">✓ Товар успешно добавлен!</div>
    <?php endif; ?>

    <div style="margin-bottom: 20px;">
        <a href="index.php" style="color: #aaa; text-decoration: none;">← На сайт</a>
        <a href="manage_categories.php" class="btn-manage-cat">Управление категориями</a>
    </div>

    <div class="add-form">
        <h3>Добавить новый товар</h3>
<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <div style="display: flex; gap: 15px;">
        <div style="flex: 2;">
            <label>Название товара</label>
            <input type="text" name="name" placeholder="Например: Борцовки Nike" required>
        </div>
        <div style="flex: 1;">
            <label>Цена (₸)</label>
            <input type="number" name="price" placeholder="55000" required>
        </div>
    </div>
    
    <label>Описание товара</label>
    <textarea name="description" rows="4" placeholder="Введите подробное описание товара..." 
              style="width: 100%; padding: 10px; margin: 10px 0; background: #111; border: 1px solid #444; color: white; border-radius: 5px; box-sizing: border-box;"></textarea>
    
    <div style="display: flex; gap: 15px;">
        <div style="flex: 2;">
            <label>Доступные размеры</label>
            <input type="text" name="sizes" placeholder="S, M, L или 38, 39, 40">
        </div>
        <div style="flex: 1;">
            <label>Кол-во в наличии</label>
            <input type="number" name="stock" placeholder="0" required>
        </div>
    </div>
    
    <label>Категория</label>
    <select name="category" required>
        <option value="" disabled selected>Выберите категорию</option>
        <?php 
        // Сброс указателя, чтобы категории загрузились, если цикл выше уже был
        if(isset($all_categories)) $all_categories->data_seek(0); 
        while($cat = $all_categories->fetch_assoc()): 
        ?>
            <option value="<?= $cat['name'] ?>"><?= $cat['name'] ?></option>
        <?php endwhile; ?>
    </select>
    
    <div style="margin: 15px 0;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #ffca28; font-weight: bold;">
            <input type="checkbox" name="is_new" value="1" style="width: 20px; height: 20px; cursor: pointer;"> 
            Отметить как "Новинку"
        </label>
    </div>

<div class="file-input-group">
    <label style="color: #fff; font-weight: bold;">Основное фото (обложка):</label>
    <input type="file" name="image" required style="margin-bottom: 20px;">
    
    <label style="color: #fff; font-weight: bold;">Дополнительные фото (скрины):</label>
    <div id="drop-zone" style="border: 2px dashed #444; padding: 30px; text-align: center; border-radius: 10px; cursor: pointer; background: #0a0a0a; transition: 0.3s; margin-top: 10px;">
        <span style="color: #888;">Нажмите сюда или перетащите фото</span>
        <input type="file" name="extra_images[]" id="extra_images" multiple accept="image/*" style="display: none;">
        <div id="file-list" style="margin-top: 15px; font-size: 0.8rem; color: #25d366; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;"></div>
    </div>
    <small style="color: #666; display: block; margin-top: 10px;">Можно выбрать сразу много фото без Ctrl, если просто выделить их мышкой в папке</small>
</div>

    <button type="submit" class="btn-add">Опубликовать товар</button>
</form>
    </div>

    <h3>Список товаров</h3>
    <table>
        <thead>
            <tr>
                <th>Фото</th>
                <th>Название</th>
                <th>Размеры</th>
                <th>Наличие</th>
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
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><small style="color: #bbb;"><?= !empty($row['sizes']) ? htmlspecialchars($row['sizes']) : '—' ?></small></td>
                <td>
                    <div style="font-size: 0.75rem; color: #ffca28; margin-bottom: 4px;">Заказ: 20-25 дн.</div>
                    <?php if($row['stock'] > 0): ?>
                        <span class="stock-badge stock-in"><?= $row['stock'] ?> шт.</span>
                    <?php else: ?>
                        <span class="stock-badge stock-out">0 шт.</span>
                    <?php endif; ?>
                </td>
                <td><?= number_format($row['price'], 0, '', ' ') ?> ₸</td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn-edit">Изменить</a>
                    <a href="admin.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Удалить товар и все его фото?')">Удалить</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('extra_images');
const fileList = document.getElementById('file-list');

// Открытие окна выбора при клике на зону
dropZone.addEventListener('click', () => fileInput.click());

// Подсветка при перетаскивании
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = '#25d366';
    dropZone.style.background = '#151515';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = '#444';
    dropZone.style.background = '#0a0a0a';
});

// Отображение списка выбранных файлов
fileInput.addEventListener('change', () => {
    fileList.innerHTML = ''; // Очистить список
    if (fileInput.files.length > 0) {
        dropZone.style.borderColor = '#25d366';
        for (let file of fileInput.files) {
            const span = document.createElement('span');
            span.textContent = '📸 ' + file.name;
            span.style.background = '#1b5e20';
            span.style.padding = '2px 8px';
            span.style.borderRadius = '4px';
            fileList.appendChild(span);
        }
    }
});
</script>

</body>
</html>