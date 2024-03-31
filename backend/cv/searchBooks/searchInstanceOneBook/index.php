<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->titleId)) {
    $titleId = $postData->titleId;
    // Подготовка SQL-запроса
    $sql = "SELECT book_id FROM book_istance WHERE inventory_number = ?";

    // Подготовка выражения
    $stmt = $conn->prepare($sql);

    // Привязка параметров
    $stmt->bind_param("i", $titleId);

    // Выполнение запроса
    if ($stmt->execute()) {
        // Получение результатов запроса
        $result = $stmt->get_result();

        // Получение данных из результатов запроса
        $row = $result->fetch_assoc();
        $bookId = $row['book_id'];
        $stmt->close();
        if ($bookId !== '') {
            $sql = "SELECT returnted_books.customer_id, returnted_books.returned_date, customer.login FROM returnted_books 
                    LEFT JOIN customer ON returnted_books.customer_id = customer.customer_id
                    WHERE returnted_books.book_id = ? AND (returnted_books.status = '3' OR returnted_books.status = '4') ORDER BY returnted_books.returned_date DESC";
            // Подготовка выражения
            $stmt = $conn->prepare($sql);
            // Привязка параметров
            $stmt->bind_param("i", $bookId);
            if ($stmt->execute()) {
                // Получение результатов запроса
                $result = $stmt->get_result();
                $inventoryNumbers = [];
                // Получение данных из результатов запроса
                while ($row = $result->fetch_assoc()) {
                    // Добавляем информацию о книге в массив
                    $inventoryNumbers[] = array(
                        'customer_id' => $row['customer_id'],
                        'returned_date' => $row['returned_date'],
                        'login' => $row['login']
                    );
                }
                echo json_encode($inventoryNumbers);
            } else {
                // Если произошла ошибка выполнения запроса
                echo json_encode(["error" => "Ошибка выполнения запроса"]);
            }
        } else {
            echo json_encode('Данный экземпляр никто не читал');
        }
    } else {
        // Если произошла ошибка выполнения запроса
        echo json_encode(["error" => "Ошибка выполнения запроса"]);
    }

    // Закрытие выражения

} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
