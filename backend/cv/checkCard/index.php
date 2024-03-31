<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Получение данных из POST-запроса
$postData = json_decode(file_get_contents("php://input"));

if ($postData && isset($postData->dateType)) {
    $dateType = $postData->dateType;

    // Получение текущей даты
    $currentDate = date('Y-m-d');

    // SQL-запрос по умолчанию
    $sql = "";

    // Определение целевой даты в зависимости от выбора пользователя
    switch ($dateType) {
        case 'today':
            $targetDate = $currentDate;
            // SQL-запрос для выбора книг с соответствующей датой и статусом
            $sql = "SELECT title_id FROM request WHERE request_date = ? AND status = '2' ORDER BY request_date ASC";
            break;
        case 'tomorrow':
            $targetDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            // SQL-запрос для выбора книг с соответствующей датой и статусом
            $sql = "SELECT title_id FROM request WHERE request_date = ? AND status = '2' ORDER BY request_date ASC";
            break;
        case 'all':
            // SQL-запрос для выбора всех книг с определенным статусом
            $sql = "SELECT title_id FROM request WHERE status = '2' ORDER BY request_date ASC";
            break;
        default:
            $targetDate = $currentDate;
    }

    if ($sql !== "") {
        // Подготовка запроса
        $stmt = $conn->prepare($sql);
    
        if ($dateType !== 'all') {
            // Привязываем параметры, если запрос не для всех бронирований
            $stmt->bind_param("s", $targetDate);
        }
    
        // Выполнение запроса
        $stmt->execute();
    
        // Получение результатов запроса
        $result = $stmt->get_result();
    
        // Массив для хранения данных
        $bookInstances = [];
    
        // Извлечение данных из результата запроса
        while ($row = $result->fetch_assoc()) {
            $titleId = $row['title_id'];
    
            // Запрос к таблице book_title по title_id
            $titleSql = "SELECT * FROM book_title WHERE title_id = ?";
            $titleStmt = $conn->prepare($titleSql);
            $titleStmt->bind_param("s", $titleId);
            $titleStmt->execute();
            $titleResult = $titleStmt->get_result();
            $titleData = $titleResult->fetch_assoc();
            $titleStmt->close();
    
            if ($titleData) {
                // Запрос для подсчета книг с определенным title_id и статусом 0 или 1
                $countSql = "SELECT COUNT(*) AS count FROM book_istance WHERE title_id = ? AND status = '0'";

                $countStmt = $conn->prepare($countSql);
                $countStmt->bind_param("s", $titleId);
                $countStmt->execute();
                $countResult = $countStmt->get_result();
                $countData = $countResult->fetch_assoc();
                $titleData['status_0_count'] = $countData['count'];
                
                $bookInstances[] = $titleData;
            }
        }
    
        // Закрытие запросов
        $stmt->close();
    
        // Отправка данных в формате JSON
        echo json_encode($bookInstances);
    } else {
        // Если отсутствуют необходимые данные
        echo json_encode(["error" => "Отсутствуют необходимые данные"]);
    }
    
} else {
    // Если отсутствуют необходимые данные
    echo json_encode(["error" => "Отсутствуют необходимые данные"]);
}

// Закрытие соединения с базой данных
$conn->close();
?>
