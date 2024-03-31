<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия необходимых параметров
if ($postData && isset($postData->UserId) && isset($postData->TitleId)) {

    // Получение UserId и TitleId из POST-запроса
    $userId = $postData->UserId;
    $titleId = $postData->TitleId;

    // Получение текущей даты
    $currentDate = date('Y-m-d');

    // SQL-запрос для выбора данных из таблицы request
    $selectQuery = "SELECT * FROM request
                    WHERE customer_id = '$postData->UserId' AND title_id = '$postData->TitleId' AND status='2'";

    // Выполнение запроса
    $result = $conn->query($selectQuery);

    if ($result->num_rows > 0) {
        // Массив для хранения выбранных данных
        $dataToInsert = array();

        while ($row = $result->fetch_assoc()) {
            // Сохраняем выбранные данные
            $dataToInsert[] = $row;
        }

        // SQL-запрос для обновления таблицы request
        $updateQuery = "DELETE FROM request
                        WHERE customer_id = '$postData->UserId' AND title_id = '$postData->TitleId' AND status='2'";

        // Выполнение запроса
        if ($conn->query($updateQuery) === TRUE) {
            // SQL-запрос для вставки данных в таблицу returnted_books
            $insertQuery = "INSERT INTO returnted_books (customer_id, status, returned_date, title_id, delay) VALUES";

            foreach ($dataToInsert as $data) {
                $insertQuery .= "('" . $data['customer_id'] . "', '1', '$currentDate', '" . $data['title_id'] . "', '0'),";
            }

            // Удаление последней запятой
            $insertQuery = rtrim($insertQuery, ',');

            // Выполнение запроса
            if ($conn->query($insertQuery) === TRUE) {
                echo json_encode(array("success" => true, "message" => "Data updated and inserted successfully"));
            } else {
                echo json_encode(array("success" => false, "message" => "Error inserting data: " . $conn->error));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "Error updating data: " . $conn->error));
        }
    } else {
        echo json_encode(array("success" => true, "message" => "No data to process"));
    }

} else {
    // Если отсутствуют необходимые данные
    echo json_encode(array("success" => false, "message" => "Missing required data"));
}

// Закрытие соединения с базой данных
$conn->close();
?>
