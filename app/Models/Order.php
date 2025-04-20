<?php

namespace App\Models;

use App\Model;

class Order extends Model
{
    // Khai báo bảng nếu tên bảng không tuân theo chuẩn Laravel (số nhiều của tên model)
    protected $table = 'orders';

    // Nếu bạn không muốn sử dụng timestamps (created_at, updated_at), bạn có thể tắt nó:
    public $timestamps = false;

    // Các thuộc tính mà bạn muốn cho phép gán hàng loạt (mass assignment)
    protected $fillable = [
        'user_id', 'total', 'status', 'created_at', 'updated_at'
    ];
}
