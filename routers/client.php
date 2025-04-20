<?php

use App\Controllers\Client\HomeController;

$router->get('/', 'App\Controllers\Client\HomeController@index');

$router->get('/products', 'App\Controllers\Client\HomeController@productList');
$router->get('/products/{id}', 'App\Controllers\Client\HomeController@productDetail');

// Giỏ hàng
$router->get('/cart', 'App\Controllers\Client\HomeController@cart');
$router->post('/add-to-cart', 'App\Controllers\Client\HomeController@addToCart');
$router->post('/update-cart', 'App\Controllers\Client\HomeController@updateCart');
$router->post('/remove-from-cart', 'App\Controllers\Client\HomeController@removeFromCart');
$router->post('/apply-voucher', 'App\Controllers\Client\HomeController@applyVoucher');

// Thanh toán
$router->get('/checkout', 'App\Controllers\Client\HomeController@checkoutForm');
$router->post('/checkout', 'App\Controllers\Client\HomeController@checkout');

// Đơn hàng
$router->get('/orders', 'App\Controllers\Client\HomeController@orderList');

$router->get('/orders/{id}', 'App\Controllers\Client\HomeController@orderDetail');

// Trang thành công
$router->get('/success', function () {
    return view('Client.success');
});
$router->get('/order-success', 'App\Controllers\Client\HomeController@orderSuccess');
// Hủy đơn hàng
$router->post('/order/{id}/cancel', 'App\Controllers\Client\HomeController@cancelOrder');

// Hiển thị form yêu cầu hoàn
$router->get('/order/{id}/return', 'App\Controllers\Client\HomeController@returnForm');

// Xử lý submit yêu cầu hoàn
$router->post('/order/{id}/return', 'App\Controllers\Client\HomeController@submitReturn');
