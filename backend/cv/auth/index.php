<?php
include __DIR__ . '/../../db.php';

// Получение данных из GET-запроса
$login = $_GET['login'] ?? '';
$password = $_GET['password'] ?? '';
$passwordsha = hash('sha256', $password);
// Подготовленный запрос
$stmt = $conn->prepare("SELECT customer_id, role FROM customer WHERE login = ? AND password = ?");
$stmt->bind_param("ss", $login, $passwordsha);
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
$conn->close();
?>
