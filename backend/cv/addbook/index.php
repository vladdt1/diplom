<?php
include __DIR__ . '/../../db.php';

// Путь для сохранения файлов
$uploadPath = __DIR__ . '/../../../frontend/src/assets/book/';

// Получаем данные из POST-запроса
$title = $_POST['title'];
$author = $_POST['author'];
$publisher = $_POST['publisher'];
$description = $_POST['description'];
$article = $_POST['article'];
$counter = $_POST['counter'];
$page = $_POST['page'];
$ydk = $_POST['ydk'];
$bbk = $_POST['bbk'];
$recommended = $_POST['recommended'];

// Получаем информацию о файле
$extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$fileName = date('YmdHis') . '.' . $extension;
$targetPath = $uploadPath . $fileName;

// Перемещаем загруженный файл в нужное место
if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
    // Файл успешно загружен

    // SQL-запрос для вставки данных в таблицу books
    $sql = "INSERT INTO book_title (title_id, title, autor, annotation, count, path, publishing, page, ydk, bbk, recommended) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    // Подготавливаем запрос
    $stmt = $conn->prepare($sql);
    // Привязываем параметры
    $stmt->bind_param("sssssssssss", $article, $title, $author, $description, $counter, $fileName, $publisher, $page, $ydk, $bbk, $recommended);
    // Выполняем запрос
    $stmt->execute();

    $inventory = '';
    $number = 1;
    $inventory_number = $article . '/';
    // SQL-запрос для вставки данных в таблицу
    $sql = "INSERT INTO book_istance (title_id, status, inventory_number) VALUES (?, ?, ?)";
    // Подготавливаем запрос
    $stmt = $conn->prepare($sql);
    // Привязываем параметры
    $status = '0';
    $date = date('Y-m-d');
    for ($i = 0; $i < $counter; $i++) {
        // Выполняем запрос в цикле
        $inventory = $inventory_number . $number;
        $number +=1;
        $stmt->bind_param("sss", $article, $status, $inventory);
        $stmt->execute();
    }

    // Закрываем запрос и соединение
    $stmt->close();
    $conn->close();

    $response = ['success' => true, 'message' => 'Книга успешно добавлена', 'fileName' => $fileName];
} else {
    $response = ['success' => false, 'message' => 'Ошибка при загрузке изображения'];
}

// Возвращаем ответ в формате JSON
echo json_encode($response);
?>
