<?php

// use App\Controllers\Client\AboutController;
use App\Controllers\Client\HomeController;

$router->get('/', HomeController::class . '@index');

// $router->get('/about', AboutController::class . '@index');

$router->get('/products', HomeController::class . '@productList');

$router->get('/products/{id}', HomeController::class . '@productDetail');

// Giỏ hàng
$router->get('/cart', HomeController::class . '@cart');
$router->post('/add-to-cart', HomeController::class . '@addToCart');
$router->post('/update-cart', HomeController::class . '@updateCart');
$router->post('/remove-from-cart', HomeController::class . '@removeFromCart');
$router->post('/apply-voucher', HomeController::class . '@applyVoucher');

