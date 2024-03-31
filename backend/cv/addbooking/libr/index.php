<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json');

$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->UserId, $postData->idBook, $postData->bookingDate, $postData->date_return)) {
    // Получение title_id из book_istance
    $findTitleIdQuery = "SELECT title_id FROM book_istance WHERE inventory_number = ?";
    $findTitleIdStmt = $conn->prepare($findTitleIdQuery);
    $findTitleIdStmt->bind_param("s", $postData->idBook);
    $findTitleIdStmt->execute();
    $findTitleIdResult = $findTitleIdStmt->get_result();
    $titleIdRow = $findTitleIdResult->fetch_assoc();
    $titleId = $titleIdRow['title_id'];

    if ($titleId) {
        // Получение title из book_title
        $findTitleQuery = "SELECT title FROM book_title WHERE title_id = ?";
        $findTitleStmt = $conn->prepare($findTitleQuery);
        $findTitleStmt->bind_param("i", $titleId);
        $findTitleStmt->execute();
        $findTitleResult = $findTitleStmt->get_result();
        $titleRow = $findTitleResult->fetch_assoc();
        $title = $titleRow['title'];

        $date_return = $postData->date_return;
        // Определение текущей даты
        $currentDate = date('Y-m-d');

        // Определение значения для добавления к текущей дате в зависимости от значения date_return
        switch ($date_return) {
            case "Неделя":
                $interval = '1 week';
                break;
            case "2 недели":
                $interval = '2 weeks';
                break;
            case "Месяц":
                $interval = '1 month';
                break;
            case "6 месяцев":
                $interval = '6 months';
                break;
        }
        // Добавление интервала к текущей дате
        $date_return_plus = date('Y-m-d', strtotime($currentDate . ' + ' . $interval));

        // Подготовленный запрос для обновления данных книги
        $updateBookQuery = "UPDATE book_istance SET customer_id = ?, date_booking = ?, date_return = ?, status = '3' WHERE inventory_number = ?";
        $updateBookStmt = $conn->prepare($updateBookQuery);
        $updateBookStmt->bind_param("ssss", $postData->UserId, $postData->bookingDate, $date_return_plus, $postData->idBook);

        if ($updateBookStmt->execute()) {
            // Возвращаем не только статус, но и название книги
            echo json_encode(["status" => 0, "title" => $title]);
        } else {
            echo json_encode(["status" => 2]);
        }

        $updateBookStmt->close();
        $findTitleStmt->close();
    } else {
        echo json_encode(["error" => "Книга не найдена"]);
    }

    $findTitleIdStmt->close();
} else {
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

$conn->close();
?>
