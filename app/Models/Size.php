<?php

namespace App\Models;

use App\Model;

class Size extends Model
{
    protected $tableName = 'sizes';

    public function findAll()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        return $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->fetchAllAssociative();
    }
}
