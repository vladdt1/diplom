<?php
include __DIR__ . '/../../db.php';
// Включаем заголовок для доступа по CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Получение периода из GET-запроса
$period = isset($_GET['period']) ? (int)$_GET['period'] : 1;

// Генерация массива дат за выбранный период
function generateDates($period) {
    $dates = [];
    $currentDate = date("Y-m-d");
    $startDate = date("Y-m-d", strtotime("-$period month"));

    for($date = $startDate; $date <= $currentDate; $date = date("Y-m-d", strtotime($date . "+1 day"))) {
        $dates[$date] = 0; // Инициализация каждой даты с нулевым количеством
    }

    return $dates;
}

// Подготовка массивов
$dates = generateDates($period);
$returned = $dates;
$booked = $dates;

// Запросы к базе данных для подсчета возвращенных книг
$sqlReturned = "SELECT returned_date, COUNT(*) as count FROM returnted_books WHERE returned_date BETWEEN DATE_SUB(NOW(), INTERVAL $period MONTH) AND NOW() GROUP BY returned_date";
$resultReturned = $conn->query($sqlReturned);

if ($resultReturned) {
    while ($row = $resultReturned->fetch_assoc()) {
        if (array_key_exists($row['returned_date'], $returned)) {
            $returned[$row['returned_date']] = (int)$row['count'];
        }
    }
}

// Запросы к базе данных для подсчета забронированных книг
$sqlBooked = "SELECT date_booking, COUNT(*) as count FROM book_istance WHERE date_booking BETWEEN DATE_SUB(NOW(), INTERVAL $period MONTH) AND NOW() GROUP BY date_booking";
$resultBooked = $conn->query($sqlBooked);

if ($resultBooked) {
    while ($row = $resultBooked->fetch_assoc()) {
        if (array_key_exists($row['date_booking'], $booked)) {
            $booked[$row['date_booking']] = (int)$row['count'];
        }
    }
}

// Подсчет общего количества возвратов и выдач за период
$totalReturned = array_sum($returned);
$totalBooked = array_sum($booked);

// Возвращаем данные в формате JSON
echo json_encode([
    'dates' => array_keys($dates),
    'returned' => array_values($returned),
    'booked' => array_values($booked),
    'totalReturned' => $totalReturned,
    'totalBooked' => $totalBooked
]);
?>
