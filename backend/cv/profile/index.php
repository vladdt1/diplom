<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из GET-запроса
$sessionId = $_GET['sessionId'];

// Запрос к базе данных для получения данных профиля
$sql = "SELECT * FROM customer WHERE customer_id = '$sessionId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Преобразование результата в ассоциативный массив
    $profileData = $result->fetch_assoc();

    // Отправка данных в формате JSON
    header('Content-Type: application/json');
    echo json_encode($profileData);
} else {
    // Если профиль не найден
    echo "Profile not found";
}

// Закрытие соединения с базой данных
$conn->close();
?>