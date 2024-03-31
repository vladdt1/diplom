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

    // Подготовка запроса для обновления данных в таблице book_instance
    $sql = "";
    if ($status == '1') {
        $book_id = "";
        $sql_select_book_id = "SELECT book_id FROM book_istance WHERE customer_id = $id AND title_id = $bookid";
        $result_select_book_id = $conn->query($sql_select_book_id);
        if ($result_select_book_id->num_rows > 0) {
            $row = $result_select_book_id->fetch_assoc();
            $book_id = $row["book_id"];
        } else {
            echo json_encode(["error" => "Book instance not found for the given customer_id and title_id"]);
            $conn->close();
            exit;
        }

        // Вставка данных в таблицу returned_books
        $today = date("Y-m-d");
        $sql_insert_returned_books = "INSERT INTO returnted_books (customer_id, book_id, returned_date, status) VALUES ('$id', '$book_id', '$today', '2')";
        if ($conn->query($sql_insert_returned_books) === TRUE) {
            // Обновление данных в таблице book_instance
            $sql_update_book_instance = "UPDATE book_istance SET customer_id = NULL, date_booking = NULL, date_create = NULL, status = '0' WHERE customer_id = $id AND title_id = $bookid";
            if ($conn->query($sql_update_book_instance) === TRUE) {
                echo json_encode(["success" => "Data updated successfully"]);
            } else {
                echo json_encode(["error" => "Error updating data in book_instance: " . $conn->error]);
            }
        } else {
            echo json_encode(["error" => "Error inserting data into returnted_books: " . $conn->error]);
        }
    } elseif ($status == '2') {
        $today = date("Y-m-d");
        $sql = "UPDATE book_istance 
                SET date_booking = '$today', status = '3' 
                WHERE customer_id = $id AND title_id = $bookid";
                
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => "Data updated successfully"]);
        } else {
            echo json_encode(["error" => "Error updating data: " . $conn->error]);
        }
    }
} else {
    // В случае отсутствия поискового запроса возвращаем ошибку
    echo json_encode(["error" => "Search term not provided"]);
}

$conn->close();
?>
