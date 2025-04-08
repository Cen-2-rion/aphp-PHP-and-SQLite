<?php

require_once __DIR__ . '/../interfaces/DatabaseWrapper.php';

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
