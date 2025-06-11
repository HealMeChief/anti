<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php'; 
require_once __DIR__ . '/includes/db.php';
require "vendor/autoload.php";




$db = new db\DB();
?>


<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Антихайпики</title>
</head>
<body>
 <?php include("header.php"); ?>

<div id="page-content">
    <div id="cart-container" class="cart">
        <h1 style="color: gray;">Кажется корзина пуста:(</h1>
    </div>
    <button id="order-button" onclick="placeOrder()" disabled>Заказать</button>

</div>
<script src="script.js"></script>
</body>
</html>
