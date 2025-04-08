<?php

interface DatabaseWrapper
{
    public function insert(array $columns, array $values): array;

    public function update(int $id, array $values): array;

    public function find(int $id): array;

    public function delete(int $id): bool;
}
