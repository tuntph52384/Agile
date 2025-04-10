<?php

namespace App\Models;

use App\Model;

class Product extends Model
{
    protected $tableName = 'products';

    public function getConnection()
    {
        return $this->connection;  // Kết nối này được thiết lập trong lớp Model cha
    }
    public function getTableName()
    {
        return $this->tableName;
    }



    public function paginate($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                'p.id                       p_id',
                'p.name                     p_name',
                'c.name                     c_name',
                'p.img_thumbnail            p_img_thumbnail',
                'p.price                    p_price',
                'p.price_sale               p_price_sale',
                'p.is_sale                  p_is_sale',
                'p.is_active                p_is_active',
                'p.is_show_home             p_is_show_home',
                'p.created_at               p_created_at',
                'p.updated_at               p_updated_at'
            )
            ->from($this->tableName, 'p')
            ->innerJoin('p', 'categories', 'c', 'c.id = p.category_id')
            ->orderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $data = $queryBuilder->fetchAllAssociative();
        $totalPage = ceil($this->count() / $limit);

        return [
            'data' => $data,
            'page' => $page,
            'limit' => $limit,
            'totalPage' => $totalPage
        ];
    }

    public function find($id)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                'p.id                       p_id',
                'p.category_id              p_category_id',
                'c.name                     c_name',
                'p.name                     p_name',
                'p.slug                     p_slug',
                'p.img_thumbnail            p_img_thumbnail',
                'p.overview                 p_overview',
                'p.content                  p_content',
                'p.price                    p_price',
                'p.price_sale               p_price_sale',
                'p.is_sale                  p_is_sale',
                'p.is_active                p_is_active',
                'p.is_show_home             p_is_show_home',
                'p.created_at               p_created_at',
                'p.updated_at               p_updated_at'
            )
            ->from($this->tableName, 'p')
            ->innerJoin('p', 'categories', 'c', 'c.id = p.category_id')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->fetchAssociative();
    }

    public function searchByName($keyword)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                'p.id                       p_id',
                'p.name                     p_name',
                'c.name                     c_name',
                'p.img_thumbnail            p_img_thumbnail',
                'p.price                    p_price',
                'p.price_sale               p_price_sale',
                'p.is_sale                  p_is_sale',
                'p.is_active                p_is_active',
                'p.is_show_home             p_is_show_home',
                'p.created_at               p_created_at',
                'p.updated_at               p_updated_at'
            )
            ->from($this->tableName, 'p')
            ->innerJoin('p', 'categories', 'c', 'c.id = p.category_id')
            ->where('p.name LIKE :keyword')
            ->setParameter('keyword', "%$keyword%")
            ->orderBy('p.id', 'DESC');

        return $queryBuilder->fetchAllAssociative();
    }
    public function searchAdvanced($keyword = '', $size = '', $color = '', $category = '')
    {
        $db = $this->getConnection();
        $qb = $db->createQueryBuilder();

        $qb->select('p.*')
            ->from('products', 'p')
            ->where('p.is_active = 1'); // Chỉ lấy sản phẩm đang hoạt động

        // Nếu có lọc size hoặc màu thì mới join bảng biến thể
        if (!empty($size) || !empty($color)) {
            $qb->leftJoin('p', 'product_variants', 'pv', 'p.id = pv.product_id');
        }

        // Lọc theo từ khóa tên sản phẩm
        if (!empty($keyword)) {
            $qb->andWhere('p.name LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        // Lọc theo size
        if (!empty($size)) {
            $qb->andWhere('pv.size_id = :size')
                ->setParameter('size', $size);
        }

        // Lọc theo màu
        if (!empty($color)) {
            $qb->andWhere('pv.color_id = :color')
                ->setParameter('color', $color);
        }
        if(!empty($category)){
            $qb->andWhere('p.category_id = :category')
                ->setParameter('category', $category);
        }

        // Tránh trùng sản phẩm khi có nhiều biến thể
        $qb->groupBy('p.id')
            ->orderBy('p.id', 'DESC');

        return $qb->fetchAllAssociative();
    }
}
