<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия поискового запроса
if (isset($postData->titleId)) {
    $titleId = $postData->titleId;

    $stmt = $conn->prepare("UPDATE book_istance SET status = 4 WHERE inventory_number = ?");
    $stmt->bind_param("s", $titleId);

    if ($stmt->execute()) {
        // Проверяем, были ли изменены какие-либо строки
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => "Status updated successfully"]);
        } else {
            echo json_encode(["warning" => "No rows updated, possibly because the title ID was not found or the status was already 0"]);
        }
    } else {
        // В случае ошибки выполнения запроса
        echo json_encode(["error" => "Error updating status: " . $stmt->error]);
    }

    $stmt->close();
}