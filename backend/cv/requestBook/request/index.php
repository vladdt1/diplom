<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->UserId)) {

    // Используйте подготовленные запросы для защиты от SQL-инъекций
    $stmt = $conn->prepare("SELECT * FROM request WHERE customer_id = ? ORDER BY request_date DESC");
    $stmt->bind_param("s", $postData->UserId);
    $stmt->execute();

    // Обработка ошибок
    if ($stmt->errno) {
        echo json_encode(["error" => "Ошибка выполнения запроса: " . $stmt->error]);
    } else {
        // Получение результатов запроса
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Преобразование результата в массив строк
            $profileData = array();

            while ($row = $result->fetch_assoc()) {
                $id_books = $row['title_id'];

                // Выполнение запроса к таблице books
                $booksStmt = $conn->prepare("SELECT title FROM book_title WHERE title_id = ?");
                $booksStmt->bind_param("s", $id_books);
                $booksStmt->execute();

                // Обработка ошибок
                if ($booksStmt->errno) {
                    echo json_encode(["error" => "Ошибка выполнения запроса: " . $booksStmt->error]);
                } else {
                    // Получение результата запроса
                    $booksResult = $booksStmt->get_result();

                    if ($booksResult->num_rows > 0) {
                        // Получение данных о книге и добавление их к профилю
                        $booksRow = $booksResult->fetch_assoc();
                        $row['title'] = $booksRow['title'];
                    }

                    // Закрытие запроса к таблице books
                    $booksStmt->close();
                }

                // Добавление строки к профилю
                $profileData[] = $row;
            }

            // Отправка данных в формате JSON
            header('Content-Type: application/json');
            echo json_encode($profileData);
        } else {
            // Если профиль не найден
            echo json_encode(["error" => "Профили не найдены"]);
        }
    }

    // Закрытие подготовленного запроса
    $stmt->close();
} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
