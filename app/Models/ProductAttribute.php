<?php

namespace App\Models;

use App\Model;

class ProductAttribute extends Model
{
    protected $tableName = 'product_attributes';

    // Lấy các thuộc tính của sản phẩm (size, color)
    public function getAttributesByProductId($productId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('pa.size_id', 'pa.color_id', 's.name AS size_name', 'cl.name AS color_name')
            ->from($this->tableName, 'pa')
            ->leftJoin('pa', 'sizes', 's', 's.id = pa.size_id')
            ->leftJoin('pa', 'colors', 'cl', 'cl.id = pa.color_id')
            ->where('pa.product_id = :product_id')
            ->setParameter('product_id', $productId);
        
        return $queryBuilder->fetchAllAssociative();
    }
}
