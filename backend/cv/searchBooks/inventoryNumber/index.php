<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->bookid)) {
    // Подготовка SQL-запроса
    $sql = "SELECT inventory_number FROM book_istance WHERE title_id = ? AND status = '0'";

    // Подготовка выражения
    $stmt = $conn->prepare($sql);

    // Привязка параметров
    $stmt->bind_param("i", $postData->bookid);

    // Выполнение запроса
    if ($stmt->execute()) {
        // Получение результатов запроса
        $result = $stmt->get_result();

        // Инициализация массива для хранения данных
        $inventoryNumbers = [];

        // Получение данных из результатов запроса
        while ($row = $result->fetch_assoc()) {
            $inventoryNumbers[] = $row['inventory_number'];
        }

        // Отправка данных клиенту в формате JSON
        echo json_encode($inventoryNumbers);
    } else {
        // Если произошла ошибка выполнения запроса
        echo json_encode(["error" => "Ошибка выполнения запроса"]);
    }

    // Закрытие выражения
    $stmt->close();
} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
