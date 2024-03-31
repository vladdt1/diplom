<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->UserId)) {
    // Используйте подготовленные запросы для защиты от SQL-инъекций
    $stmt = $conn->prepare("SELECT * FROM returnted_books WHERE customer_id = ? AND (status = '0' OR status = '1' OR status = '2') ORDER BY returned_date DESC");
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
                if ($row['status'] === '3' || $row['status'] === '4'){
                    $book_id = $row['book_id'];

                    // Выполнение запроса к таблице book_instance для получения title_id
                    $booksStmt = $conn->prepare("SELECT title_id FROM book_istance WHERE book_id = ?");
                    $booksStmt->bind_param("s", $book_id);
                    $booksStmt->execute();
                    $booksResult = $booksStmt->get_result();

                    if ($booksResult->num_rows > 0) {
                        $book_instance_row = $booksResult->fetch_assoc();
                        $title_id = $book_instance_row['title_id'];
                        $row['title_id'] = $title_id;

                        // Выполнение запроса к таблице book_title для получения названия книги
                        $bookTitleStmt = $conn->prepare("SELECT title FROM book_title WHERE title_id = ?");
                        $bookTitleStmt->bind_param("s", $title_id);
                        $bookTitleStmt->execute();
                        $bookTitleResult = $bookTitleStmt->get_result();

                        if ($bookTitleResult->num_rows > 0) {
                            $bookTitleRow = $bookTitleResult->fetch_assoc();
                            $row['title'] = $bookTitleRow['title'];
                        } else {
                            $row['title'] = "Название не найдено";
                        }

                        // Закрытие запроса к таблице book_title
                        $bookTitleStmt->close();
                    }

                    // Добавление строки к профилю
                    $profileData[] = $row;

                    // Закрытие запроса к таблице book_instance
                    $booksStmt->close();
                }else if ($row['status'] === '0' || $row['status'] === '1' || $row['status'] === '2'){
                    $title_id = $row['title_id'];                

                    // Выполнение запроса к таблице book_title для получения названия книги
                    $bookTitleStmt = $conn->prepare("SELECT title FROM book_title WHERE title_id = ?");
                    $bookTitleStmt->bind_param("s", $title_id);
                    $bookTitleStmt->execute();
                    $bookTitleResult = $bookTitleStmt->get_result();

                    if ($bookTitleResult->num_rows > 0) {
                            $bookTitleRow = $bookTitleResult->fetch_assoc();
                        $row['title'] = $bookTitleRow['title'];
                    } else {
                        $row['title'] = "Название не найдено";
                    }

                    // Закрытие запроса к таблице book_title
                    $bookTitleStmt->close();

                    // Добавление строки к профилю
                    $profileData[] = $row;
                }
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