<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->titleId)) {
    // Инициализация массива для хранения результата
    $response = [
        'inventoryNumbers' => [],
        'requests' => []
    ];

    // Запрос в таблицу book_istance
    $sqlBookInstance = "SELECT inventory_number FROM book_istance WHERE title_id = ?";
    $stmtBookInstance = $conn->prepare($sqlBookInstance);
    $stmtBookInstance->bind_param("i", $postData->titleId);

    if ($stmtBookInstance->execute()) {
        $resultBookInstance = $stmtBookInstance->get_result();
        while ($row = $resultBookInstance->fetch_assoc()) {
            $response['inventoryNumbers'][] = $row['inventory_number'];
        }
    } else {
        echo json_encode(["error" => "Ошибка выполнения запроса к таблице book_istance"]);
        $stmtBookInstance->close();
        $conn->close();
        exit();
    }

    $stmtBookInstance->close();

    // Запрос в таблицу request
    $sqlRequest = "SELECT request_date FROM request WHERE title_id = ?";
    $stmtRequest = $conn->prepare($sqlRequest);
    $stmtRequest->bind_param("i", $postData->titleId);

    if ($stmtRequest->execute()) {
        $resultRequest = $stmtRequest->get_result();
        while ($row = $resultRequest->fetch_assoc()) {
            $response['requests'][] = $row;
        }
    } else {
        echo json_encode(["error" => "Ошибка выполнения запроса к таблице request"]);
        $stmtRequest->close();
        $conn->close();
        exit();
    }

    $stmtRequest->close();

    // Отправка собранных данных клиенту
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

$conn->close();
?>
