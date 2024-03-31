<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Функция для получения значения поля 'adm' по определенному 'id'
function getAdminStatusById($conn, $userId) {
    $userId = mysqli_real_escape_string($conn, $userId); // Защита от SQL-инъекций

    $sql = "SELECT adm FROM users WHERE id = '$userId'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['adm'];
    } else {
        return null; // В случае, если пользователя с таким userId не найдено
    }
}

// Проверка наличия параметра 'userId' в GET-запросе
if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $adminStatus = getAdminStatusById($conn, $userId);

    if ($adminStatus !== null) {
        echo json_encode(['adm' => $adminStatus]);
    } else {
        echo json_encode(['error' => 'Пользователь с указанным userId не найден.']);
    }
} else {
    echo json_encode(['error' => 'Отсутствует параметр "userId" в GET-запросе.']);
}

// Закрытие соединения с базой данных
$conn->close();
?>