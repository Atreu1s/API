<?php
// Подключаемся к базе данных
require 'config.php';

// Устанавливаем заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Получаем ID товара из запроса
        $id = intval($_GET['id']);
        
        // Подготавливаем запрос для получения товара по ID
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Товар не найден"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Не указан ID товара"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Метод не поддерживается"]);
}
?>
