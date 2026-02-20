<?php
session_start();
if (!isset($_SESSION['is_admin'])) exit;
include 'includes/db.php';

// Добавление
if (isset($_POST['add_cat'])) {
    $name = trim($_POST['cat_name']);
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

// Удаление
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id = $id");
}

$cats = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { background: #111; color: white; font-family: sans-serif; padding: 50px; }
        input { padding: 10px; background: #222; border: 1px solid #444; color: white; }
        button { padding: 10px; background: #e10600; color: white; border: none; cursor: pointer; }
        table { width: 400px; margin-top: 20px; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #333; }
    </style>
</head>
<body>
    <h2>Управление категориями</h2>
    <form method="POST">
        <input type="text" name="cat_name" placeholder="Название категории" required>
        <button type="submit" name="add_cat">Добавить</button>
    </form>

    <table>
        <?php while($c = $cats->fetch_assoc()): ?>
        <tr>
            <td><?= $c['name'] ?></td>
            <td><a href="?delete=<?= $c['id'] ?>" style="color: red;">Удалить</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="admin.php" style="color: #aaa;">← Назад в админку</a>
</body>
</html>