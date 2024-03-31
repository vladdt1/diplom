<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

$data = json_decode(file_get_contents("php://input"), true);
$sessionId = $data['sessionId'];
$updatedEmail = $data['updatedProfileData']['email'];
$updatedPhone = $data['updatedProfileData']['phone'];

// Проверка наличия данных перед выполнением запроса
if (!empty($updatedEmail) || !empty($updatedPhone)) {
    // Формирование части SQL-запроса для обновления данных
    $updateClause = '';
    if (!empty($updatedEmail)) {
        $updateClause .= "email = '$updatedEmail'";
    }
    if (!empty($updatedPhone)) {
        $updateClause .= (!empty($updateClause) ? ', ' : '') . "phone = '$updatedPhone'";
    }

    // Здесь предполагается, что ваши данные о пользователе хранятся в таблице 'users'
    $sql = "UPDATE customer SET $updateClause WHERE customer_id = $sessionId";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "No data to update";
}

// Закрытие соединения с базой данных
$conn->close();
?>
