<?php
header("Access-Control-Allow-Origin: *"); // Разрешить доступ с любого домена
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Разрешенные методы
header("Access-Control-Allow-Headers: *"); // Разрешенные заголовки
$servername = "localhost";
$username = "root";
$password = "";
$database = "diplom";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $database);

// Проверяем соединение
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}