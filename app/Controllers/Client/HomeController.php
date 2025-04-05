<?php

namespace App\Controllers\Client;

use App\Controller;
use App\Models\Banner;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $heading1 = 'Trang Chủ';
        $subHeading1 = 'TC';

        $bannerModel = new Banner();
        $banners = $bannerModel->getAllBanners(); // Lấy danh sách banner từ cơ sở dữ liệu

        $productModel = new Product();
        $products = $productModel->findAll();

        return view('Client.home', compact('heading1', 'subHeading1', 'products', 'banners'));
    }

    public function productList()
    {
        $heading1 = 'Danh Sách Sản Phẩm';
        $subHeading1 = 'Product';
    
        $productModel = new Product();
    
        // Lấy từ khóa tìm kiếm từ query string
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    
        $queryBuilder = $productModel->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($productModel->getTableName());
    
        // Nếu có từ khóa tìm kiếm, thêm điều kiện vào truy vấn
        if (!empty($keyword)) {
            $queryBuilder->where('name LIKE :keyword')
                         ->setParameter('keyword', "%$keyword%");
        }
    
        // Thực thi truy vấn
        $products = $queryBuilder->fetchAllAssociative();
    
        return view('Client.productList', compact('heading1', 'subHeading1', 'products', 'keyword'));
    }
    
    

    public function productDetail($id)
    {
        $productModel = new Product();
        $product = $productModel->find($id);

        if (empty($product)) {
            redirect404();
        }

        return view('Client.productDetail', compact('product'));
    }
}
