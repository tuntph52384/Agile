<?php

namespace App\Models;

use App\Model;

class Color extends Model
{
    protected $tableName = 'colors';

    public function getAllColors()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($this->tableName);
        
        return $queryBuilder->fetchAllAssociative();
    }
}
