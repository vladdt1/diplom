<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия данных
if ($postData && isset($postData->idBook)) {
    // Подготовленный запрос
    $sql = "SELECT * FROM book_title WHERE title_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $postData->idBook);
    $stmt->execute();
    $result = $stmt->get_result();

    // Проверка наличия данных
    if ($result->num_rows > 0) {
        // Преобразование результата в ассоциативный массив
        $profileData = $result->fetch_assoc();

        // Определение количества строк с соответствием title_id в book_istance
        $titleId = $postData->idBook;
        $countSql = "SELECT COUNT(title_id) AS bookCount FROM book_istance WHERE title_id = ? AND customer_id IS NULL";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param("s", $titleId);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $countData = $countResult->fetch_assoc();

        // Добавление количества книг в массив $profileData
        $profileData['bookCount'] = $countData['bookCount'];

        // Отправка данных в формате JSON
        header('Content-Type: application/json');
        echo json_encode($profileData);
    } else {
        // Если профиль не найден
        echo json_encode(["error" => "Profile not found"]);
    }

    // Закрытие соединения с базой данных
    $stmt->close();
    $countStmt->close();
} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

$conn->close();
?>
