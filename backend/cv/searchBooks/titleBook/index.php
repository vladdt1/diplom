<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Запрос к таблице book_istance
$stmt = $conn->prepare("SELECT inventory_number FROM book_istance WHERE status = '0'");
$stmt->execute();
$stmt->bind_result($inventory_number);

$inventory_numbers = array();

// Получение результатов запроса
while ($stmt->fetch()) {
    $inventory_numbers[] = $inventory_number;
}

// Закрытие запроса
$stmt->close();

// Отправка результатов в формате JSON
echo json_encode($inventory_numbers);

// Закрытие соединения с базой данных
$conn->close();
?>
