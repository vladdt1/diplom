<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение текущей даты
$currentDate = date('Y-m-d');

// SQL-запрос для выбора данных из таблицы book_istance
$selectQuery = "SELECT customer_id, title_id
                FROM request
                WHERE request_date < '$currentDate' AND status = '2'";

// Выполнение запроса
$result = $conn->query($selectQuery);

if ($result->num_rows > 0) {
    // Массив для хранения выбранных данных
    $dataToInsert = array();

    while ($row = $result->fetch_assoc()) {
        // Сохраняем выбранные данные
        $dataToInsert[] = $row;
    }

    // SQL-запрос для обновления таблицы book_istance
    $updateQuery = "DELETE FROM request 
                    WHERE request_date < '$currentDate' AND status = '2'";

    // Выполнение запроса
    if ($conn->query($updateQuery) === TRUE) {
        // SQL-запрос для вставки данных в таблицу returnted_books
        $insertQuery = "INSERT INTO returnted_books (customer_id, status, returned_date, title_id, delay) VALUES";

        foreach ($dataToInsert as $data) {
            $insertQuery .= "('" . $data['customer_id'] . "', '0', '$currentDate', '" . $data['title_id'] . "', '0'),";
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

// Закрытие соединения с базой данных
$conn->close();
?>
