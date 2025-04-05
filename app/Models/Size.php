<?php

namespace App\Models;

use App\Model;

class Size extends Model
{
    protected $tableName = 'sizes';

    public function getAllSizes()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($this->tableName);
        
        return $queryBuilder->fetchAllAssociative();
    }
}
