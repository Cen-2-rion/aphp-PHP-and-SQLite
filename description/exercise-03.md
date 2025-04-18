# Задание 3. PHP и SQLite

## Техническое задание
В этом задании будут использоваться таблицы из предыдущей задачи: магазины, заказы и клиенты.

Реализуйте для каждой таблицы свой класс с имплементацией следующего интерфейса:
```php
interface DatabaseWrapper
{
    // вставляет новую запись в таблицу, возвращает полученный объект как массив
    public function insert(array $tableColumns, array $values): array;
    // редактирует строку под конкретным id, возвращает результат после изменения
    public function update(int $id, array $values): array;
    // поиск по id
    public function find(int $id): array;
    // удаление по id
    public function delete(int $id): bool;
}
```

*Замечание: возможно, они будут очень похожи, и можно будет ввести базовый класс, в котором будет лежать вся логика.*

Добавьте работу с полученными классами — добавление или изменение каких либо строк и вывод результата в консоль.

## Задание со звёздочкой
Добавьте в интерфейс метод для получения фильтрованного списка, то есть

```php
public function get(array $filters): array;
```

Где `$filters` будет ассоциативным массивом, в котором:
* ключ — название поля для фильтрации;
* значение — значение, которое будут фильтровать.

Пока что считаем, что все параметры, которые могут быть, — это только сравнения на равенство.

Внимание! Для выполнения задания Вам, возможно, потребуется включить драйвер pdo_sqlite в настройках PHP. При сдаче задания через http://repl.it/ выберите шаблон PHP (PDO SQLite).

**Обратите внимание на** [**рекомендации по сдаче домашнего задания**](../homework.md). 
