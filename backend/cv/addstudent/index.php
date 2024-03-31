<?php
include __DIR__ . '/../../db.php';
header('Content-Type: application/json'); // Устанавливаем Content-Type для JSON

// Путь для сохранения файлов
$uploadPath = '../../../frontend/src/assets/avatar/';

// Проверяем, был ли файл передан
if (isset($_FILES['image']) && isset($_POST['email']) && isset($_POST['login']) && isset($_POST['password'])) {
    $fio = $_POST['fio'];
    $phone = $_POST['phone'];
    $sitizenship = $_POST['sitizenship'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $customer_group = $_POST['customer_group'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = hash('sha256', $_POST['password']);
    $role = '0';

    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = date('YmdHis') . '.' . $extension;
    $targetPath = $uploadPath . $filename;

    // Перемещаем файл в указанную директорию
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        // SQL-запрос для вставки данных в таблицу
        $sql = "INSERT INTO customer (
                customer_fio, gender, email, phone, sitizenship, birth_date, role, path, password, login, customer_group
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $fio, $gender, $email, $phone, $sitizenship, $birthdate, $role, $filename, $password, $login, $customer_group);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении данных пользователя.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при загрузке файла.']);
    }
} else {
    echo json_encode(['error' => 'Отсутствуют необходимые данные или файл.']);
}

$conn->close();
?>
