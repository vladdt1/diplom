<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

$data = json_decode(file_get_contents("php://input"), true);
$idBook = $data['IdBook'];
$updatedBookData = $data['updatedBookData'];

// Проверка наличия данных перед выполнением запроса
if (!empty($idBook) && !empty($updatedBookData)) {
    // Формирование части SQL-запроса для обновления данных
    $updateClause = '';
    foreach ($updatedBookData as $field => $value) {
        $updateClause .= "$field = '$value', ";
    }
    // Удаление последней запятой
    $updateClause = rtrim($updateClause, ', ');

    // Здесь предполагается, что ваши данные о книгах хранятся в таблице 'books'
    $sql = "UPDATE book_title SET $updateClause WHERE title_id = $idBook";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating record: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No data to update']);
}

$conn->close();
?>
