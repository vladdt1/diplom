<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

// Проверка наличия поискового запроса
if (isset($postData->id) && isset($postData->status) && isset($postData->bookid)) {
    $id = $postData->id;
    $status = $postData->status;
    $bookid = $postData->bookid;
    $inventory_number = $postData->inventory_number;
    $date_return = $postData->date_return;

    // Проверка статуса
    switch ($status) {
        case 1:
            // Создание строки в таблице returnted_books
            $today = date("Y-m-d");
            $stmt = $conn->prepare("INSERT INTO returnted_books (customer_id, title_id, returned_date, status) VALUES (?, ?, ?, '2')");
            $stmt->bind_param("iss", $id, $bookid, $today);
            $stmt->execute();
            $stmt->close();

            // Удаление строки из таблицы request
            $stmt = $conn->prepare("DELETE FROM request WHERE customer_id = ? AND title_id = ? AND status = '2'");
            $stmt->bind_param("ii", $id, $bookid);
            $stmt->execute();
            $stmt->close();
            break;

        case 2:
            // Определение текущей даты
            $currentDate = date('Y-m-d');

            // Определение значения для добавления к текущей дате в зависимости от значения date_return
            switch ($date_return) {
                case "Неделя":
                    $interval = '1 week';
                    break;
                case "2 недели":
                    $interval = '2 weeks';
                    break;
                case "Месяц":
                    $interval = '1 month';
                    break;
                case "6 месяцев":
                    $interval = '6 months';
                    break;
            }

            // Добавление интервала к текущей дате
            $date_return_plus = date('Y-m-d', strtotime($currentDate . ' + ' . $interval));

            // Изменение строки в таблице book_istance
            $date_booking = $currentDate;

            $stmt = $conn->prepare("UPDATE book_istance SET title_id = ?, customer_id = ?, status = '3', date_booking = ?, date_return = ? WHERE inventory_number = ? LIMIT 1");
            $stmt->bind_param("iisss", $bookid, $id, $date_booking, $date_return_plus, $inventory_number);
            $stmt->execute();
            $stmt->close();

            // Удаление строки из таблицы request
            $stmt = $conn->prepare("DELETE FROM request WHERE customer_id = ? AND title_id = ? AND status = '2'");
            $stmt->bind_param("ii", $id, $bookid);
            $stmt->execute();
            $stmt->close();
            break;

        case 3:
            // Получение данных из таблицы book_istance
            $stmt = $conn->prepare("SELECT book_id, date_return, date_booking FROM book_istance WHERE title_id = ? AND customer_id = ? AND status = '3'");
            $stmt->bind_param("ii", $bookid, $id);
            $stmt->execute();
            $stmt->bind_result($book_id, $date_retur, $date_booking);
            $stmt->fetch();
            $stmt->close();

            // Обнуление значений в таблице book_istance
            $stmt = $conn->prepare("UPDATE book_istance SET customer_id = NULL, status = '0', date_return = NULL WHERE title_id = ? AND customer_id = ? AND status = '3'");
            $stmt->bind_param("ii", $bookid, $id);
            $stmt->execute();
            $stmt->close();

            // Добавление строки в таблицу returnted_books
            $today = date("Y-m-d");
            
            $stmt = $conn->prepare("INSERT INTO returnted_books (book_id, customer_id, returned_date, status, delay, date_booking) VALUES (?, ?, ?, ?, ?, ?)");
            if ($today <= $date_retur) {
                $status = '3';
                $delay = '0';
            } else {
                $status = '4';
                $delay = date_diff(date_create($today), date_create($date_retur))->format('%a') - 1;
            }
            $stmt->bind_param("iisiss", $book_id, $id, $today, $status, $delay, $date_booking);
            $stmt->execute();
            $stmt->close();
            break;

        default:
            echo json_encode(["error" => "Invalid status"]);
            break;
    }
} else {
    // В случае отсутствия необходимых данных возвращаем ошибку
    echo json_encode(["error" => "Required data not provided"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
