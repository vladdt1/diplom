<?php
include __DIR__ . '/../../db.php';

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->email) && isset($postData->login) && isset($postData->password)) {
    $email = $postData->email;
    $login = $postData->login;
    $password = hash('sha256', $postData->password);
    $role = '1';
    // SQL-запрос для вставки данных в таблицу customer
    $sql = "INSERT INTO customer (email, role, password, login) VALUES (?, ?, ?, ?)";
    // Подготавливаем запрос
    $stmt = $conn->prepare($sql);
    // Привязываем параметры
    $stmt->bind_param("ssss", $email, $role, $password, $login);
    // Выполняем запрос
    $stmt->execute();
    // Закрытие выражения
    $stmt->close();
    $response = ['success' => true];
    echo json_encode($response);
} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
