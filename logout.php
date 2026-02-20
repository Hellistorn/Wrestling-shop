<?php
session_start();
session_destroy(); // Удаляет все данные сессии
header("Location: index.php");
exit;