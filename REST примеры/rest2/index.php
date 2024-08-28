<?php
// Заголовки для обработки CORS (если нужно)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Массив с данными о товарах
$products = [
    ["id" => 1, "name" => "Телефон", "price" => 500],
    ["id" => 2, "name" => "Ноутбук", "price" => 1000],
    ["id" => 3, "name" => "Планшет", "price" => 300]
];

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

// Проверяем, является ли метод GET
if ($method === 'GET') {
    // Получаем ID товара из URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // Преобразуем в целое число для безопасности

        // Поиск товара по ID
        $product = array_filter($products, function ($p) use ($id) {
            return $p['id'] === $id;
        });

        // Если товар найден
        if (!empty($product)) {
            // Возвращаем товар в формате JSON
            echo json_encode(array_values($product)[0]);
        } else {
            // Товар не найден
            http_response_code(404);
            echo json_encode(["message" => "Товар не найден"]);
        }
    } else {
        // Если ID не указан, возвращаем ошибку
        http_response_code(400);
        echo json_encode(["message" => "Не указан ID товара"]);
    }
} else {
    // Метод не поддерживается
    http_response_code(405);
    echo json_encode(["message" => "Метод не поддерживается"]);
}
?>
