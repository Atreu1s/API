<?php
$host = 'localhost';
$dbname = 'todo_app';
$username = 'root'; // Измените на ваше имя пользователя
$password = ''; // Измените на ваш пароль

// Создаем подключение к базе данных
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>