<?php

namespace App\Models;

use App\Model;

class Banner extends Model
{
    protected $tableName = 'banners'; // Tên bảng chứa thông tin banner

    public function getAllBanners()
    {
        // Lấy tất cả các banner
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->tableName)
            ->fetchAllAssociative();
    }
}
