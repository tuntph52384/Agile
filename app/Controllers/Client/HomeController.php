<?php

namespace App\Controllers\Client;

use App\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;

class HomeController extends Controller
{
    // Trang chủ
    public function index()
    {
        $heading1 = 'Trang Chủ';
        $subHeading1 = 'TC';
    
        $bannerModel = new Banner();
        $banners = $bannerModel->getAllBanners();
    
        $productModel = new Product();
        $sizeModel = new Size();
        $colorModel = new Color();
        $categoryModel = new Category();
    
        $sizes = $sizeModel->findAll();
        $colors = $colorModel->findAll();
        $categories = $categoryModel->findAll();
    
        // Lấy dữ liệu lọc từ query string
        $keyword = $_GET['keyword'] ?? '';
        $size = $_GET['size'] ?? '';
        $color = $_GET['color'] ?? '';
        $category = $_GET['category'] ?? '';
    
        // Lọc sản phẩm
        $products = $productModel->searchAdvanced($keyword, $size, $color, $category);
    
        return view('Client.home', compact(
            'heading1',
            'subHeading1',
            'products',
            'banners',
            'keyword',
            'sizes',
            'colors',
            'categories',
            'size',
            'color',
            'category'
        ));
    }
    

    // Danh sách sản phẩm có bộ lọc
    public function productList()
    {
        $heading1 = 'Danh Sách Sản Phẩm';
        $subHeading1 = 'Product';
    
        $productModel = new Product();
        $sizeModel = new Size();
        $colorModel = new Color();
        $categoryModel = new Category();
    
        $sizes = $sizeModel->findAll();
        $colors = $colorModel->findAll();
        $categories = $categoryModel->findAll();
    
        $keyword = $_GET['keyword'] ?? '';
        $size = $_GET['size'] ?? '';
        $color = $_GET['color'] ?? '';
        $category = $_GET['category'] ?? '';
    
        $products = $productModel->searchAdvanced($keyword, $size, $color, $category);
    
        return view('Client.productList', compact(
            'heading1',
            'subHeading1',
            'products',
            'keyword',
            'sizes',
            'colors',
            'categories',
            'size',
            'color',
            'category'
        ));
    }
    

    // Chi tiết sản phẩm
    public function productDetail($id)
    {
        $productModel = new Product();
        $product = $productModel->find($id);

        if (empty($product)) {
            redirect404(); // Hiển thị trang 404 nếu không có sản phẩm
        }

        $db = $productModel->getConnection();
        $queryBuilder = $db->createQueryBuilder();

        // Lấy biến thể sản phẩm (kèm tên màu và size)
        $queryBuilder
            ->select('pv.*', 'c.name AS color_name', 's.name AS size_name')
            ->from('product_variants', 'pv')
            ->join('pv', 'colors', 'c', 'pv.color_id = c.id')
            ->join('pv', 'sizes', 's', 'pv.size_id = s.id')
            ->where('pv.product_id = :id')
            ->setParameter('id', $id);

        $variants = $queryBuilder->fetchAllAssociative();

        return view('Client.productDetail', compact('product', 'variants'));
    }
}
