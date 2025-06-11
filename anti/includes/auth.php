<?php
namespace db;
require "vendor/autoload.php";

function generateSessionId() {
    return bin2hex(random_bytes(32));
}

// Инициализация гостевой сессии
if (!isset($_SESSION['guest_session_id'])) {
    $_SESSION['guest_session_id'] = generateSessionId();
    
    // Сохраняем сессию в БД
    $db = new DB();
    $db->link->query(
        "INSERT INTO guest_sessions (session_id) VALUES ('{$_SESSION['guest_session_id']}')"
    );
}

// Проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Регистрация пользователя
function registerUser($email, $password, $phone) {
    $db = new DB();
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $db->link->prepare(
        "INSERT INTO users (email, password_hash, phone) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $email, $passwordHash, $phone);
    
    return $stmt->execute();
}

// Вход пользователя
function loginUser($email, $password) {
    $db = new DB();
    $user = $db->link->query(
        "SELECT * FROM users WHERE email = '{$db->link->real_escape_string($email)}'"
    )->fetch_assoc();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        
        // Переносим корзину из гостевой в пользовательскую
        if (isset($_SESSION['guest_session_id'])) {
            $db->link->query(
                "UPDATE cart_items 
                 SET user_id = {$user['user_id']}, session_id = NULL 
                 WHERE session_id = '{$_SESSION['guest_session_id']}'"
            );
        }
        
        return true;
    }
    
    return false;
}

// Выход пользователя
function logoutUser() {
    session_destroy();
    session_start();
    $_SESSION['guest_session_id'] = generateSessionId();
}

// 👇 Обработка действия login
if (isset($_GET['action']) && $_GET['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (loginUser($email, $password)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Неверный email или пароль']);
    }
    exit;
}

// Если нет action
