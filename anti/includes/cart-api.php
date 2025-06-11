<?php
namespace db;
use Exception;

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/prod.php';
require_once __DIR__ . '/cart-func.php';
require_once __DIR__ . '/../vendor/autoload.php';

use db\DB;
use function db\isLoggedIn;
use function db\handleAddToCart;
use function db\handleGetCart;
use function db\handleRemoveFromCart;

// Добавь функцию handleUpdateQuantity, если её нет
use function db\handleUpdateQuantity;

header('Content-Type: application/json');

$db = new DB();

// Сначала пытаемся получить action из GET
$action = $_GET['action'] ?? null;

// Если action нет в GET, пробуем получить из JSON-тела запроса
if (!$action) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action'])) {
        $action = $input['action'];
    }
}

$response = ['success' => false];

try {
    switch ($action) {
        case 'add':
            $productId = (int)($_GET['product_id'] ?? 0);
            if ($productId <= 0) throw new Exception('Неверный product_id для добавления');
            $response = handleAddToCart($db, $productId);
            break;

        case 'get':
            $response = handleGetCart($db);
            break;

        case 'remove':
            $productId = (int)($_GET['product_id'] ?? 0);
            if ($productId <= 0) throw new Exception('Неверный product_id для удаления');
            $response = handleRemoveFromCart($db, $productId);
            break;

        case 'update':
            // Если $input не определён, читаем тело (на случай, если не читали ранее)
            if (!isset($input)) {
                $input = json_decode(file_get_contents('php://input'), true);
            }

            if (!isset($input['product_id'], $input['quantity'])) {
                throw new Exception('Недостаточно данных для обновления');
            }

            $productId = (int)$input['product_id'];
            $quantity = (int)$input['quantity'];

            if ($productId <= 0 || $quantity < 0) {
                throw new Exception('Неверные данные для обновления количества');
            }

            $response = handleUpdateQuantity($db, $productId, $quantity);
            break;

        default:
            $response['error'] = 'Invalid action';
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
