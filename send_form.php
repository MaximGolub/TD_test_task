<?php
require 'db.php';
header('Content-Type: application/json');

$logFile = 'requests.log';

function logRequest($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] " . mb_convert_encoding($message, 'UTF-8') . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $select_service = $_POST['select_service'] ?? '';
    $select_price = $_POST['select_price'] ?? '';
    $message = $_POST['comments'] ?? '';
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // Валидация данных
    $errors = [];
    if (empty($first_name)) {
        $errors[] = 'Поле "First Name" обязательно для заполнения.';
    }
    if (empty($last_name)) {
        $errors[] = 'Поле "Last Name" обязательно для заполнения.';
    }
    if (empty($select_service)) {
        $errors[] = 'Поле "Select Service" обязательно для заполнения.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    }
    if (!preg_match('/^\+?[0-9]{5,15}$/', $phone)) {
        $errors[] = 'Некорректный номер телефона.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode("\n", $errors),
        ]);
        exit;
    }

    try {
        // Сохранение данных в таблицу пользователей
        $db->beginTransaction();

        // Проверка наличия пользователя
        $stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $userId = $user['id'];
        } else {
            // Вставка нового пользователя
            $stmt = $db->prepare('INSERT INTO users (name, last_name, email, phone) VALUES (:name, :last_name, :email, :phone)');
            $stmt->execute([
                ':name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone' => $phone
            ]);
            $userId = $db->lastInsertId();
        }

        // Сохранение сообщения
        $stmt = $db->prepare('INSERT INTO messages (user_id, select_service, select_price, message) VALUES (:user_id, :select_service, :select_price, :message)');
        $stmt->execute([
            ':user_id' => $userId,
            ':select_service' => $select_service,
            ':select_price' => $select_price,
            ':message' => $message
        ]);

        $db->commit();
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
        exit;
    }

    logRequest("REQUEST: " . json_encode($_POST), $logFile);

    $geoData = getGeoData($ipAddress);

    $response = [
        'success' => true,
        'message' => 'The data is successfully sent!',
        'redirectUrl' => 'https://google.com.ua/',
        'geoData' => $geoData,
    ];

    logRequest("RESPONSE: " . json_encode($response), $logFile);

    echo json_encode($response);
    exit;
}

function getGeoData($ip) {
    $apiUrl = "http://ipwho.is/$ip";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
        ],
    ]);
    $geoResponse = @file_get_contents($apiUrl, false, $context);
    if ($geoResponse === false) {
        return ['error' => 'Unable to fetch geodata'];
    }
    return json_decode($geoResponse, true);
}
?>