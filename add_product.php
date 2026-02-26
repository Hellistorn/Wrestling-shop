<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    exit("Доступ запрещен");
}

require_once "includes/db.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $category = trim($_POST['category']);
    $sizes = isset($_POST['sizes']) ? trim($_POST['sizes']) : '';
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0; 
    $is_new = isset($_POST['is_new']) ? 1 : 0; 

    if (empty($name) || $price <= 0 || empty($category)) {
        die("Заполните основные поля (название, цена, категория)");
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetPath = "images/products/" . $imageName; 

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            
            // ВАЖНО: Проверь количество знаков вопроса и колонок в базе!
            $stmt = $conn->prepare(
                "INSERT INTO products (name, description, price, image, category, is_new, sizes, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssdssisi", $name, $description, $price, $targetPath, $category, $is_new, $sizes, $stock);

            if ($stmt->execute()) {
                $product_id = $conn->insert_id; 

                // Загрузка доп. фото (если есть)
                if (!empty($_FILES['extra_images']['name'][0])) {
                    $extra_files = $_FILES['extra_images'];
                    $stmt_extra = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");

                    for ($i = 0; $i < count($extra_files['name']); $i++) {
                        if ($extra_files['error'][$i] === 0) {
                            $extraName = time() . "_" . uniqid() . "_extra_" . basename($extra_files['name'][$i]);
                            $extraPath = "images/products/" . $extraName;
                            if (move_uploaded_file($extra_files['tmp_name'][$i], $extraPath)) {
                                $stmt_extra->bind_param("is", $product_id, $extraPath);
                                $stmt_extra->execute();
                            }
                        }
                    }
                }

                // ВОТ ЭТА СТРОКА РЕШАЕТ ПРОБЛЕМУ:
                // После успеха она мгновенно кидает тебя назад в список товаров
                header("Location: admin.php?success=1");
                exit(); 
            } else {
                echo "Ошибка БД: " . $conn->error;
            }
        }
    }
}
$conn->close();
?>