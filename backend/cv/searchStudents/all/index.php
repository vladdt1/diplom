<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия числа
if (isset($postData->StudentCount)) {
    $StudentCount = intval($postData->StudentCount); // Преобразуем в целое число

    // Подготовленный запрос для выборки определенного количества строк
    $stmt = $conn->prepare("SELECT customer_id, customer_fio, login, customer_group, path FROM customer WHERE role = '0' LIMIT ?");
    $stmt->bind_param("i", $StudentCount);
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
    // В случае отсутствия числа возвращаем ошибку
    echo json_encode(["error" => "Number not provided"]);
}
$conn->close();
?>
