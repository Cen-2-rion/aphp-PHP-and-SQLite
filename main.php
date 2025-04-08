<?php

require_once __DIR__ . '/database/shop.php';
require_once __DIR__ . '/models/BaseModel.php';

$pdo = new PDO('sqlite:shop.db');

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
