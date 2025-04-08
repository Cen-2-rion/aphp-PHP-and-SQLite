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
