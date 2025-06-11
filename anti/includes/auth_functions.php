<?php
namespace db;
require_once __DIR__ . '/db.php';

function generateSessionId() {
    return bin2hex(random_bytes(32));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function registerUser($email, $password, $phone) {
    $db = new DB();
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->link->prepare("INSERT INTO users (email, password_hash, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $passwordHash, $phone);
    return $stmt->execute();
}

function loginUser($email, $password) {
    $db = new DB();
    $user = $db->link->query("SELECT * FROM users WHERE email = '{$db->link->real_escape_string($email)}'")->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];

        if (isset($_SESSION['guest_session_id'])) {
            $db->link->query(
                "UPDATE cart_items 
                 SET user_id = {$user['user_id']}, session_id = NULL 
                 WHERE session_id = '{$db->link->real_escape_string($_SESSION['guest_session_id'])}'"
            );
        }

        return true;
    }

    return false;
}

function logoutUser() {
    session_destroy();
    session_start();
    $_SESSION['guest_session_id'] = generateSessionId();
}