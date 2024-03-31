<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия данных
if ($postData && isset($postData->UserId, $postData->idBook, $postData->bookingDate)) {
    $bookingDate = date('Y-m-d', strtotime($postData->bookingDate));

    // Подготовленный запрос для поиска свободной книги по idBook
    $findBookQuery = "SELECT * FROM book_istance WHERE title_id = ? AND customer_id IS NULL LIMIT 1";
    $findBookStmt = $conn->prepare($findBookQuery);
    $findBookStmt->bind_param("s", $postData->idBook);
    $findBookStmt->execute();
    $result = $findBookStmt->get_result();

    // Проверка наличия свободных книг
    if ($result->num_rows > 0) {
        // Получение первой свободной книги
        $bookInstance = $result->fetch_assoc();

        // Подготовленный запрос для обновления данных книги
        $updateBookQuery = "INSERT INTO request (customer_id, request_date, status, title_id) VALUES (?, ?, '2', ?)";
        $updateBookStmt = $conn->prepare($updateBookQuery);
        $updateBookStmt->bind_param("sss", $postData->UserId, $bookingDate, $postData->idBook);
        
        // Выполнение запроса на обновление данных книги
        if ($updateBookStmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => "Failed to update book instance"]);
        }

        // Закрытие подготовленных запросов
        $updateBookStmt->close();
        $findBookStmt->close();
    } else {
        echo json_encode(["error" => "No available books for booking"]);
    }
} else {
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
