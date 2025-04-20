<?php

namespace App\Models;

use App\Model;

class OrderItem extends Model
{
    // Khai báo bảng nếu tên bảng không theo chuẩn Laravel (số nhiều của tên model)
    protected $table = 'order_items';

    // Nếu bảng không có trường timestamps (created_at, updated_at), bạn có thể tắt chúng:
    public $timestamps = false;

    // Các thuộc tính mà bạn muốn cho phép gán hàng loạt (mass assignment)
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price'
    ];
}
