<?php

namespace App\Controllers\Client;

use App\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {

        $heading1 = 'Trang Chủ';
        $subHeading1 = 'TC';

        $productModel = new Product();
        $products = $productModel->findAll();

        return view('Client.home', compact('heading1', 'subHeading1', 'products'));
    }

    public function productList()
    {
        $heading1 = 'Danh Sách Sản Phẩm';
        $subHeading1 = 'Product';

        $productModel = new Product();
        $products = $productModel->findAll();

        return view('Client.productList', compact('heading1', 'subHeading1', 'products'));
    }

    public function productDetail($id)
    {
        $productModel = new Product();
        $product = $productModel->find($id);

        if (empty($product)) {
            redierct404();
        }

        return view('Client.productDetail' ,compact('product'));
    }
}
