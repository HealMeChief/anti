<?php 
   require_once __DIR__ . '/includes/config.php';
   require_once __DIR__ . '/includes/auth.php'; 
   require_once __DIR__ . '/includes/db.php';
   require_once __DIR__ . '/includes/cart-func.php';
   require "vendor/autoload.php";
   
   echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Проверяем, есть ли guest_session_id
if (!isset($_SESSION['guest_session_id'])) {
    die("Гостевая сессия не создана!");
}
   $db = new db\DB();
   $data = $db->get_all_prod();

    
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
 <?php



 include("header.php"); ?>

 <div id="page-content">

    <div class="m-main">
        
    <?php foreach($data as $product): ?>
        <?= (new db\Prod($product["product_id"], $product["name"], $product["price"], $product["image_url"], $product["hover_image_url"]))->get_product() ?>        
    <?php endforeach; ?>
</div>

<script src="script.js"></script>
</body>
</html>




