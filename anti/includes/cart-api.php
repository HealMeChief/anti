<?php
namespace db;
use Exception;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/prod.php';
require "vendor/autoload.php";

use db\DB;
use function db\isLoggedIn;
use function db\handleAddToCart;
use function db\handleGetCart;
use function db\handleRemoveFromCart;

header('Content-Type: application/json');

$db = new DB();
$action = $_GET['action'] ?? '';
$response = ['success' => false];

try {
    switch ($action) {
        case 'add':
            $productId = (int)$_GET['product_id'];
            $response = handleAddToCart($db, $productId);
            break;

        case 'get':
            $response = handleGetCart($db);
            break;

        case 'remove':
            $productId = (int)$_GET['product_id'];
            $response = handleRemoveFromCart($db, $productId);
            break;

        default:
            $response['error'] = 'Invalid action';
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
