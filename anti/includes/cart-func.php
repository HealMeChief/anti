<?php
// File: includes/cart_handlers.php

namespace db;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/prod.php';
require_once __DIR__ . '/cart-func.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Exception;
use db\DB;

use function db\getGuestSessionId;
function handleAddToCart(DB $db, int $productId): array {
    $cartIdentifier = isLoggedIn() 
        ? ['user_id' => $_SESSION['user_id']] 
        : ['session_id' => "'{$_SESSION['guest_session_id']}'"];

    $product = $db->link->query("SELECT * FROM products WHERE product_id = $productId")->fetch_assoc();

    if (!$product) {
        throw new Exception('Product not found');
    }

    $whereClause = isLoggedIn() 
        ? "user_id = {$_SESSION['user_id']}" 
        : "session_id = '{$_SESSION['guest_session_id']}'";

    $existingItem = $db->link->query(
        "SELECT * FROM cart_items WHERE $whereClause AND product_id = $productId"
    )->fetch_assoc();

    if ($existingItem) {
        $db->link->query(
            "UPDATE cart_items SET quantity = quantity + 1 
             WHERE cart_item_id = {$existingItem['cart_item_id']}"
        );
    } else {
        $column = isLoggedIn() ? 'user_id' : 'session_id';
        $value = isLoggedIn() ? $_SESSION['user_id'] : "'{$_SESSION['guest_session_id']}'";

        $db->link->query(
            "INSERT INTO cart_items ($column, product_id, quantity) 
             VALUES ($value, $productId, 1)"
        );
    }

    return ['success' => true];
}



function handleGetCart(DB $db): array {
    $whereClause = isLoggedIn() 
        ? "user_id = {$_SESSION['user_id']}" 
        : "session_id = '{$_SESSION['guest_session_id']}'";

    $result = $db->link->query(
        "SELECT p.product_id, p.name, p.price, p.image_url, ci.quantity 
         FROM cart_items ci
         JOIN products p ON ci.product_id = p.product_id
         WHERE $whereClause"
    );

    return [
        'success' => true,
        'items' => $result->fetch_all(MYSQLI_ASSOC)
    ];
}

function handleRemoveFromCart(DB $db, int $productId): array {
    $whereClause = isLoggedIn() 
        ? "user_id = {$_SESSION['user_id']}" 
        : "session_id = '{$_SESSION['guest_session_id']}'";

    $db->link->query(
        "DELETE FROM cart_items 
         WHERE $whereClause AND product_id = $productId"
    );

    return ['success' => true];
}
function handleUpdateQuantity(DB $db, int $productId, int $quantity): array {
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
       $stmt = $db->link->prepare('UPDATE ...');('UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$quantity, $userId, $productId]);
    } else {
        $sessionId = getGuestSessionId();
        $stmt = $db->link->prepare('UPDATE ...');('UPDATE cart_items SET quantity = ? WHERE session_id = ? AND product_id = ?');
        $stmt->execute([$quantity, $sessionId, $productId]);
    }

    return ['success' => true];
}
