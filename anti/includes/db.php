<?php
/**
 * Класс для работы с базой данных
 */

 namespace db;
 use mysqli;
class DB {
    

    static $host = "localhost";
    static $user = "root";
    static $password = "";
    static $database = "magaz";
    public $link;

    
    public function __construct() {
        $this->link = new mysqli(DB::$host, DB::$user, DB::$password, DB::$database);
        $this->link->set_charset("utf8");
    }

    /**
     * Выполнить SQL-запрос с параметрами
     * @param string $sql — SQL-запрос, например "SELECT * FROM users WHERE id = ?"
     * @param array $params — параметры для подстановки вместо ?, например [5]
     * @return PDOStatement — объект результата
     */
    // public function query($sql, $params = []) {
    //     $stmt = $this->pdo->prepare($sql); // Подготавливаем запрос
    //     $stmt->execute($params); // Выполняем с параметрами
    //     return $stmt; // Возвращаем результат
    // }

    /**
     * Получить одну строку из БД
     */
    // public function getRow($sql, $params = []) {
    //     return $this->query($sql, $params)->fetch();
    // }

    /**
     * Получить все строки из БД
     */
    // public function getAll($sql, $params = []) {
    //     return $this->query($sql, $params)->fetchAll();
    // }

    /**
     * Получить значение одной колонки (например, количество товаров)
     */
    // public function getValue($sql, $params = []) {
    //     return $this->query($sql, $params)->fetchColumn();
    // }

    /**
     * Вставить новую запись в таблицу
     * @return int — ID новой записи
     */
    // public function insert($table, $data) {
    //     $columns = implode(", ", array_keys($data));
    //     $placeholders = implode(", ", array_fill(0, count($data), "?"));
    //     $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    //     $this->query($sql, array_values($data));
    //     return $this->pdo->lastInsertId();
    // }


    public function get_all_prod() {
    $sql_result= $this->link->query("SELECT * FROM `products` ORDER BY `product_id` DESC");
    if ($sql_result->num_rows) {
        return $sql_result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

    private $cache = [];

    public function getProduct($id) {
        if (!isset($this->cache['products'][$id])) {
            $this->cache['products'][$id] = $this->link->query("SELECT * FROM products WHERE product_id = $id")->fetch_assoc();
        }
        return $this->cache['products'][$id];
    }

}






// 4. Как использовать класс DB?
// Пример в index.php:

// php
// require_once __DIR__ . '/includes/config.php';
// require_once __DIR__ . '/includes/db.php';

// $db = new DB(); // Создаём объект для работы с БД

// // Пример 1: Получить все товары
// $products = $db->getAll("SELECT * FROM products");

// // Пример 2: Добавить новый товар (админка)
// $newProductId = $db->insert('products', [
//     'name' => 'Новая футболка',
//     'price' => 1990,
//     'image_url' => 'image.jpg'
// ]);

// // Пример 3: Получить один товар
// $product = $db->getRow("SELECT * FROM products WHERE product_id = ?", [1]);