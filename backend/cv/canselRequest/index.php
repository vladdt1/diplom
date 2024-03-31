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

    // SQL-запрос для выбора данных из таблицы request
    $selectRequestQuery = "SELECT * FROM request
                            WHERE customer_id = '$userId' AND title_id = '$titleId'";

    // Выполнение запроса
    $result = $conn->query($selectRequestQuery);

    if ($result->num_rows > 0) {
        // Массив для хранения выбранных данных
        $dataToInsert = array();

        while ($row = $result->fetch_assoc()) {
            // Сохраняем выбранные данные
            $dataToInsert[] = $row;
        }

        // SQL-запрос для удаления строки в таблице request
        $deleteRequestQuery = "DELETE FROM request
                                WHERE customer_id = '$userId' AND title_id = '$titleId'";
        
        // Выполнение запроса на удаление строки
        if ($conn->query($deleteRequestQuery) === TRUE) {
            echo json_encode(array("success" => true, "message" => "Request deleted successfully"));
        } else {
            echo json_encode(array("success" => false, "message" => "Error deleting request: " . $conn->error));
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
