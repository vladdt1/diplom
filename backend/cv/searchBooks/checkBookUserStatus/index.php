<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия данных
if ($postData && isset($postData->UserId, $postData->idBook)) {
    // Подготовленный запрос для проверки наличия строки в таблице book_istance
    $checkBookingQuery = "SELECT COUNT(*) as count FROM book_istance WHERE title_id = ? AND customer_id = ? LIMIT 1";
    $checkBookingStmt = $conn->prepare($checkBookingQuery);
    $checkBookingStmt->bind_param("ss", $postData->idBook, $postData->UserId);
    $checkBookingStmt->execute();
    $result = $checkBookingStmt->get_result();
    $row = $result->fetch_assoc();
    $count1 = (int)$row['count'];

    // Подготовленный запрос для проверки наличия строки в таблице request
    $checkRequestQuery = "SELECT COUNT(*) as count FROM request WHERE title_id = ? AND customer_id = ? LIMIT 1";
    $checkRequestStmt = $conn->prepare($checkRequestQuery);
    $checkRequestStmt->bind_param("ss", $postData->idBook, $postData->UserId);
    $checkRequestStmt->execute();
    $result2 = $checkRequestStmt->get_result();
    $row2 = $result2->fetch_assoc();
    $count2 = (int)$row2['count'];

    // Отправка результата в формате JSON
    echo json_encode(["result1" => $count1, "result2" => $count2]);

    // Закрытие подготовленных запросов
    $checkBookingStmt->close();
    $checkRequestStmt->close();

} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
