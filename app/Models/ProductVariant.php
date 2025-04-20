<?php

namespace App\Models;

use App\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    // Nếu bảng không có created_at và updated_at thì thêm dòng dưới
    public $timestamps = false;

    // Gợi ý thêm: nếu mày muốn dễ thao tác hơn
    protected $fillable = [
        'product_id', 'color', 'size', 'stock', 'price'
    ];
}
