<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия данных
if ($postData && isset($postData->UserId, $postData->idBook)) {
    // Подготовленный запрос для вставки новой строки в таблицу request
    $sql = "INSERT INTO request (title_id, customer_id, request_date, status) VALUES (?, ?, NOW(), '1')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $postData->idBook, $postData->UserId);
    
    // Выполнение запроса
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Ошибка выполнения запроса"]);
    }

    // Закрытие подготовленного запроса
    $stmt->close();
} else {
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
