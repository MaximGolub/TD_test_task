<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$select_service = $_POST['select_service'] ?? '';

// Валидация данных
$errors = [];

if (empty($first_name)) {
    $errors[] = 'Поле "First Name" обязательно для заполнения.';
}
if (empty($last_name)) {
    $errors[] = 'Поле "Last Name" обязательно для заполнения.';
}
if(empty($select_service))
{
    $errors[] = 'Поле "Select Time" обязательно для заполнения.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email.';
}
if (!preg_match('/^\+?[0-9]{5,15}$/', $phone)) {
    $errors[] = 'Некорректный номер телефона.';
}

// Если есть ошибки, отправляем их клиенту
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode("\n", $errors),
    ]);
    exit;
}

// Если всё прошло успешно, отправляем успешный ответ
echo json_encode([
    'success' => true,
    'message' => 'The data is successfully sent!',
    'redirectUrl' => 'https://google.com.ua/'
]);
exit;
}
?>