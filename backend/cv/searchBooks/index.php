<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия поискового запроса
if (isset($postData->searchTerm)) {
    $searchTerm = $postData->searchTerm;

    // Подготовленный запрос для поиска книг по названию или автору
    $stmt = $conn->prepare("SELECT * FROM book_title WHERE title LIKE ? OR autor LIKE ?");
    $likeTerm = "%{$searchTerm}%";
    $stmt->bind_param("ss", $likeTerm, $likeTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Проверяем результат запроса
    if ($result) {
        if ($result->num_rows > 0) {
            // Выводим данные в JSON формате
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            // В случае отсутствия данных возвращаем пустой массив
            echo json_encode([]);
        }
    } else {
        // В случае ошибки базы данных выводим сообщение об ошибке
        echo json_encode(["error" => $conn->error]);
    }

    // Закрываем соединение с базой данных
    $stmt->close();
} else {
    // В случае отсутствия поискового запроса возвращаем ошибку
    echo json_encode(["error" => "Search term not provided"]);
}
$conn->close();
?>
