<?php
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/auth.php'; 
    require_once __DIR__ . '/includes/db.php';
    require "vendor/autoload.php";
    $db = new db\DB();
    if (isset($_GET["error"])) {
        $error = "Неверный логин или пароль!";
    }
      if (isset($_GET["sign_error"])) {
        $sign_error = "Такой логин уже занят!";
    }
    if (isset($_GET["name_error"])) {
        $name_error = "Такое имя уже занято!";
    }
     if (isset($_GET["sign_success"])) {
        $sign_success = "Вы успешно зарегестрировались!";
    }
?>


    <!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="">
    <title>Антихайпики</title>
</head>
<body>
 <?php include("header.php"); ?>

    <div class="form">
    <div class="log">
    <h2>Вход</h2>
    <form id="loginForm">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="#register">Зарегистрироваться</a></p>
    </div>
</div>




<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    const response = await fetch('/includes/auth.php?action=login', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
        alert('Вы успешно вошли!');
        window.location.reload();
    } else {
        alert(result.error || 'Ошибка входа');
    }
});
</script>



 </body>
 </html>
