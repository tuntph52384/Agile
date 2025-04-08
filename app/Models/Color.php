<?php

namespace App\Models;

use App\Model;

class Color extends Model
{
    protected $tableName = 'colors';

    public function findAll()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        return $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->fetchAllAssociative();
    }
}
