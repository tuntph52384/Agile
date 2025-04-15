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
    // Phương thức khởi tạo để kiểm tra session
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

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

        // Lấy dữ liệu lọc từ query string và sanitize
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

        // Lấy dữ liệu lọc từ query string và sanitize
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

    // Giỏ hàng
    public function cart()
{
    $cart = $_SESSION['cart'] ?? [];
    $cartItems = [];

    if (!empty($cart)) {
        $db = (new Product())->getConnection();
        $queryBuilder = $db->createQueryBuilder();

        // Lấy mảng các ID từ giỏ hàng và chuyển chúng thành số nguyên
        $variantIds = array_keys($cart);
        $variantIds = array_map('intval', $variantIds); // Đảm bảo rằng variantIds chỉ chứa số nguyên

        if (!empty($variantIds)) {
            // Tạo các placeholder cho từng giá trị trong mảng variantIds
            $placeholders = [];
            foreach ($variantIds as $index => $id) {
                $placeholder = ':id' . $index;
                $placeholders[] = $placeholder;
                // Sử dụng hằng số kiểu DBAL để bind từng giá trị số nguyên
                $queryBuilder->setParameter('id' . $index, $id, \Doctrine\DBAL\ParameterType::INTEGER);
            }

            // Nối các placeholder thành chuỗi, ví dụ: ":id0, :id1, :id2"
            $inClause = implode(',', $placeholders);

            $queryBuilder
                ->select('pv.*', 'p.name AS product_name', 'p.img_thumbnail', 'c.name AS color_name', 's.name AS size_name')
                ->from('product_variants', 'pv')
                ->join('pv', 'products', 'p', 'pv.product_id = p.id')
                ->join('pv', 'colors', 'c', 'pv.color_id = c.id')
                ->join('pv', 'sizes', 's', 'pv.size_id = s.id')
                ->where($queryBuilder->expr()->in('pv.id', $inClause));
        }

        // Thực thi câu truy vấn và lấy dữ liệu
        $variants = $queryBuilder->executeQuery()->fetchAllAssociative();

        foreach ($variants as $variant) {
            $id = $variant['id'];
            $variant['quantity'] = $cart[$id] ?? 0;
            $variant['total'] = $variant['quantity'] * $variant['price'];
            $cartItems[] = $variant;
        }
    }

    return view('Client.cart', compact('cartItems'));
}

    

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart()
    {
        session_start();

        $variantId = $_POST['variant_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!$variantId || $quantity <= 0) {
            return redirect('/cart');
        }

        $cart = $_SESSION['cart'] ?? [];

        if (isset($cart[$variantId])) {
            $cart[$variantId] += $quantity;
        } else {
            $cart[$variantId] = $quantity;
        }

        $_SESSION['cart'] = $cart;

        return redirect('/cart');
    }

    // Cập nhật giỏ hàng (giảm số lượng)
   public function updateCart()
{
    session_start();

    $variantId = $_POST['variant_id'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($variantId && $quantity > 0) {
        $_SESSION['cart'][$variantId] = $quantity;
        // Thiết lập flash message cho việc cập nhật thành công
        $_SESSION['flash_success'] = "Cập nhật số lượng sản phẩm thành công!";
    } else {
        unset($_SESSION['cart'][$variantId]);
    }

    return redirect('/cart');
}


    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart()
    {
        session_start();

        $variantId = $_POST['variant_id'] ?? null;

        if ($variantId) {
            unset($_SESSION['cart'][$variantId]);
        }

        return redirect('/cart');
    }
    public function applyVoucher()
    {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $voucherCode = $_POST['voucher_code'] ?? null;

        // Nếu không nhập voucher, chuyển hướng lại
        if (!$voucherCode) {
            $_SESSION['flash_error'] = "Vui lòng nhập mã voucher!";
            return redirect('/cart');
        }

        // Danh sách các voucher có thể áp dụng cùng với tỷ lệ giảm (phần trăm)
        $vouchers = [
            'SALE10' => 10,   // Giảm 10%
            'SALE20' => 20,   // Giảm 20%
            'VIP'    => 15,   // Giảm 15%
            // Bạn có thể thêm nhiều voucher khác tại đây
        ];

        $upperCode = strtoupper($voucherCode); // chuẩn hóa mã voucher thành chữ in hoa

        if (array_key_exists($upperCode, $vouchers)) {
            // Nếu voucher hợp lệ, lưu thông tin vào session
            $_SESSION['voucher'] = [
                'code'             => $upperCode,
                'discount_percent' => $vouchers[$upperCode],
            ];
            $_SESSION['flash_success'] = "Voucher $upperCode áp dụng thành công: Giảm {$vouchers[$upperCode]}%!";
        } else {
            // Nếu voucher không hợp lệ, xoá voucher cũ (nếu có) và báo lỗi
            unset($_SESSION['voucher']);
            $_SESSION['flash_error'] = "Voucher không hợp lệ hoặc đã hết hạn!";
        }

        return redirect('/cart');
    }
}
