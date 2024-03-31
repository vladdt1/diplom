<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия поискового запроса и количества студентов
if (isset($postData->searchTerm)) {
    $searchTerm = $postData->searchTerm;

    // Подготовленный запрос для поиска студентов по нескольким полям с использованием LIMIT
    $stmt = $conn->prepare("SELECT customer_id, 
        customer_fio, 
        login, 
        customer_group, 
        path FROM customer 
        WHERE 
        role = '0' AND
        (customer_fio LIKE ? OR 
        email LIKE ? OR 
        login LIKE ? OR 
        customer_group LIKE ?)");
    
    $likeTerm = "%{$searchTerm}%";
    $stmt->bind_param("ssss", $likeTerm, $likeTerm, $likeTerm, $likeTerm);
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
    // В случае отсутствия поискового запроса или количества студентов возвращаем ошибку
    echo json_encode(["error" => "Search term or StudentCount not provided"]);
}
$conn->close();
?>
