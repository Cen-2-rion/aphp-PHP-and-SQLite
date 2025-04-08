<?php

if (file_exists('shop.db')) unlink('shop.db'); // удаляем старую базу данных

$pdo = new PDO('sqlite:shop.db');

// таблица магазинов
$pdo->exec("
    CREATE TABLE shop (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        address TEXT
    )
");

// таблица клиентов
$pdo->exec("
    CREATE TABLE client (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT NOT NULL UNIQUE
    )
");

// таблица заказов
$pdo->exec("
    CREATE TABLE 'order' (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        shop_id INTEGER,
        client_id INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (shop_id) REFERENCES shop(id),
        FOREIGN KEY (client_id) REFERENCES client(id)
    )
");

interface DatabaseWrapper
{
    public function insert(array $columns, array $values): array;
    public function update(int $id, array $values): array;
    public function find(int $id): array;
    public function delete(int $id): bool;
}

class BaseModel implements DatabaseWrapper
{
    protected PDO $pdo;
    protected string $table;

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function insert(array $columns, array $values): array
    {
        $columnsString = implode(', ', $columns); // объединяем имена столбцов в строку
        $placeholders = rtrim(str_repeat('?, ', count($values)), ', '); // создаём необходимое кол-во плейсхолдеров
        $sql = "INSERT INTO $this->table ($columnsString) VALUES ($placeholders)"; // формируем SQL-запрос
        $stmt = $this->pdo->prepare($sql); // подготавливаем SQL-запрос
        $stmt->execute($values); // выполняем с подстановкой значений
        $id = $this->pdo->lastInsertId(); // получаем id последней записи
        return $this->find($id);
    }

    public function update(int $id, array $values): array
    {
        $setParts = [];

        // создаём пары: ключ = ?
        foreach ($values as $column => $value) {
            $setParts[] = "$column = ?";
        }

        $setPartsString = implode(', ', $setParts); // объединяем пары в строку
        $sql = "UPDATE $this->table SET $setPartsString WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge(array_values($values), [$id])); // приводим к виду формируемого SQL-запроса
        return $this->find($id);
    }

    public function find(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC) ? : []; // возвращаем массив, иначе []

        if (isset($result['created_at'])) {
            $date = new DateTime($result['created_at']);
            $date->setTimezone(new DateTimeZone('Europe/Moscow'));
            $result['created_at'] = $date->format('d.m.Y H:i:s');
        }
        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

$shop = new BaseModel($pdo, 'shop');
$client = new BaseModel($pdo, 'client');
$order = new BaseModel($pdo, '"order"');

// добавляем магазин
$addShop = $shop->insert(['name', 'address'], ['Магнит', 'ул. Ленина, 15']);
echo "Магазин добавлен:\n";
print_r($addShop);

// добавляем клиента
$addClient = $client->insert(['name', 'phone'], ['Александр Владимирович', '+79481356922']);
echo "Клиент добавлен:\n";
print_r($addClient);

// добавляем заказ
$addOrder = $order->insert(['shop_id', 'client_id'], [$addShop['id'], $addClient['id']]);
echo "Заказ добавлен:\n";
print_r($addOrder);

// обновляем магазин
$updatedShop = $shop->update(1, ['name' => 'Хлебный', 'address' => 'пер. Романовых, 3']);
echo "Обновлённый магазин:\n";
print_r($updatedShop);

// находим клиента
$foundClient = $client->find(1);
echo "Найден клиент:\n";
print_r($foundClient);

// удаляем заказ
$orderDeleted = $order->delete(1);
echo "Удаление заказа: " . ($orderDeleted ? 'успешно' : 'неудачно') . "\n";

// проверяем заказ после удаления
$checkOrder = $order->find(1);
echo "Состояние заказа после удаления:\n";
print_r($checkOrder);
