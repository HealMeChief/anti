<?php

namespace db;

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'magaz');
define('DB_USER', 'root');
define('DB_PASS', '');

// Настройки сессии
session_start();

// Базовые настройки
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL); // Выводим все ошибки для разработки
ini_set('display_errors', 1);

// Автозагрузка классов (если будем добавлять другие классы)
spl_autoload_register(function ($class) {
    include __DIR__ . '/' . $class . '.php';
});