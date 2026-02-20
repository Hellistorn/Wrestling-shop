<?php
session_start();
// Проверка прав
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    exit("Доступ запрещен");
}

require_once "includes/db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Берем данные из формы
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    
    // --- НОВОЕ: Обработка галочки "Новинка" ---
    // Если галочка нажата, в POST придет '1', если нет — ставим 0
    $is_new = isset($_POST['is_new']) ? 1 : 0; 

    if (empty($name) || empty($price) || empty($category)) {
        die("Заполните все поля");
    }

    // 2. Проверка загрузки изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = "images/products/" . $imageName; 

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {

            // 3. Обновленный SQL запрос (Добавили колонку is_new и 5-й знак вопроса)
            $stmt = $conn->prepare(
                "INSERT INTO products (name, price, image, category, is_new) VALUES (?, ?, ?, ?, ?)"
            );

            // "sdssi" означает: 
            // s - string (name), d - double (price), s - string (image), s - string (category), i - integer (is_new)
            $stmt->bind_param("sdssi", $name, $price, $targetPath, $category, $is_new);

            if ($stmt->execute()) {
                header("Location: admin.php?success=1");
                exit();
            } else {
                echo "Ошибка БД: " . $stmt->error;
            }

            $stmt->close();

        } else {
            echo "Ошибка загрузки изображения. Проверьте папку images/products/";
        }

    } else {
        echo "Изображение обязательно или файл слишком большой";
    }

    $conn->close();
}
?>