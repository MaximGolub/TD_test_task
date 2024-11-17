<?php
try {
    // Подключение к базе данных
    $db = new PDO('sqlite:project.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>