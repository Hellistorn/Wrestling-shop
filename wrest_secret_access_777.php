<?php
session_start();
$my_password = "admin_spartan_2026"; 

if (isset($_POST['pass'])) {
    if ($_POST['pass'] === $my_password) {
        $_SESSION['is_admin'] = true; // Устанавливаем ТОЛЬКО если пароль совпал
        header("Location: index.php");
        exit;
    }
}
?>
<form method="POST">
    <input type="password" name="pass">
    <button type="submit">Войти</button>
</form>