<?php
include __DIR__ . '/../../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Assuming your books are stored in a table named 'books'
$sql = "SELECT * FROM book_title";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $books = array();

    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    echo json_encode($books);
} else {
    echo json_encode(array()); // Return an empty array if there are no books
}

$conn->close();
?>
