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
    // Hiển thị form thanh toán
    public function checkoutForm()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $_SESSION['flash_error'] = "Giỏ hàng đang trống, không thể thanh toán!";
            return redirect('/cart');
        }

        // --- Bắt đầu build $cartItems giống như trong cart() ---
        $cartItems = [];
        $db = (new Product())->getConnection();
        $qb = $db->createQueryBuilder();

        // Lấy danh sách variant IDs
        $variantIds = array_keys($cart);
        $variantIds = array_map('intval', $variantIds);
        if (!empty($variantIds)) {
            $placeholders = [];
            foreach ($variantIds as $i => $id) {
                $ph = ':id' . $i;
                $placeholders[] = $ph;
                $qb->setParameter('id' . $i, $id, \Doctrine\DBAL\ParameterType::INTEGER);
            }
            $in = implode(',', $placeholders);

            $qb->select('pv.*', 'p.name AS product_name', 'p.img_thumbnail', 'c.name AS color_name', 's.name AS size_name')
                ->from('product_variants', 'pv')
                ->join('pv', 'products', 'p', 'pv.product_id = p.id')
                ->join('pv', 'colors', 'c', 'pv.color_id = c.id')
                ->join('pv', 'sizes', 's', 'pv.size_id = s.id')
                ->where($qb->expr()->in('pv.id', $in));

            $variants = $qb->executeQuery()->fetchAllAssociative();

            foreach ($variants as $variant) {
                $vid = $variant['id'];
                $variant['quantity'] = $cart[$vid] ?? 0;
                $variant['total']    = $variant['quantity'] * $variant['price'];
                $cartItems[] = $variant;
            }
        }
        // --- Kết thúc build $cartItems ---

        // Truyền $cartItems vào view để view có biến này
        return view('Client.checkout', compact('cartItems'));
    }


    // Xử lý thanh toán
    public function checkout()
    {
        session_start();

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash_error'] = "Vui lòng đăng nhập để thanh toán!";
            return redirect('/auth');
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $_SESSION['flash_error'] = "Giỏ hàng trống, không thể thanh toán!";
            return redirect('/cart');
        }

        $db = (new Product())->getConnection();
        $queryBuilder = $db->createQueryBuilder();

        // Tính tổng tiền từ giỏ hàng
        $variantIds = array_keys($cart);
        $variantIds = array_map('intval', $variantIds);
        $placeholders = [];
        foreach ($variantIds as $index => $id) {
            $placeholders[] = ':id' . $index;
            $queryBuilder->setParameter('id' . $index, $id, \Doctrine\DBAL\ParameterType::INTEGER);
        }
        $inClause = implode(',', $placeholders);

        $queryBuilder
            ->select('id', 'price')
            ->from('product_variants')
            ->where($queryBuilder->expr()->in('id', $inClause));
        $variants = $queryBuilder->executeQuery()->fetchAllAssociative();

        $totalPrice = 0;
        foreach ($variants as $variant) {
            $id = $variant['id'];
            $quantity = $cart[$id];
            $totalPrice += $quantity * $variant['price'];
        }

        // Áp dụng voucher nếu có
        $discount = 0;
        if (!empty($_SESSION['voucher']['discount_percent'])) {
            $percent = $_SESSION['voucher']['discount_percent'];
            $discount = $totalPrice * ($percent / 100);
        }

        $finalPrice = $totalPrice - $discount;

        // Lưu vào bảng orders
        $orderData = [
            'user_id' => $userId,
            'status' => 'pending',
            'total_price' => $finalPrice,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $db->insert('orders', $orderData);
        $orderId = $db->lastInsertId();

        // Lưu vào bảng order_items
        foreach ($variants as $variant) {
            $id = $variant['id'];
            $price = $variant['price'];
            $quantity = $cart[$id];

            $item = [
                'order_id' => $orderId,
                'product_variant_id' => $id,
                'quantity' => $quantity,
                'price' => $price,
            ];
            $db->insert('order_items', $item);
        }

        // Xoá giỏ hàng và voucher
        unset($_SESSION['cart']);
        unset($_SESSION['voucher']);
        $_SESSION['flash_success'] = "Thanh toán thành công! Mã đơn hàng: $orderId";

        // Thay vì return redirect('/');
        return redirect('/order-success'); // Chuyển hướng về trang chủ hoặc trang cảm ơn
    }
    public function orderSuccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Đảm bảo đã có flash_success từ checkout()
        return view('Client.orderSuccess');
    }
    // Hủy đơn hàng
    public function cancelOrder($id)
    {
        session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash_error'] = "Vui lòng đăng nhập!";
            return redirect('/login');
        }

        $db = (new Product())->getConnection();
        $qb = $db->createQueryBuilder();

        // Chỉ cho hủy khi status = 'pending' hoặc 'processing'
        $allowed = ['pending', 'processing'];
        $placeholders = [];
        foreach ($allowed as $i => $st) {
            $ph = ':st' . $i;
            $placeholders[] = $ph;
            $qb->setParameter('st' . $i, $st);
        }
        $inClause = implode(', ', $placeholders);

        $affected = $qb
            ->update('orders')
            ->set('status', ':newStatus')
            ->where('id = :id')
            ->andWhere('user_id = :uid')
            ->andWhere("status IN ($inClause)")
            ->setParameter('newStatus', 'canceled')
            ->setParameter('id', $id, \Doctrine\DBAL\ParameterType::INTEGER)
            ->setParameter('uid', $userId, \Doctrine\DBAL\ParameterType::INTEGER)
            ->executeStatement();  // trả về số bản ghi bị ảnh hưởng

        if ($affected > 0) {
            $_SESSION['flash_success'] = "Đơn #{$id} đã được hủy thành công.";
        } else {
            $_SESSION['flash_error'] = "Không thể hủy đơn này (có thể đã xử lý hoặc không thuộc về bạn).";
        }

        return redirect('/orders');
    }



    public function returnForm($id)
    {
        // Lấy thông tin người dùng từ session
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) return redirect('/login');

        // Lấy thông tin đơn hàng từ cơ sở dữ liệu
        $order = (new Product())->getConnection()
            ->createQueryBuilder()
            ->select('*')->from('orders')
            ->where('id = :id')->andWhere('user_id = :uid')
            ->setParameter('id', $id)
            ->setParameter('uid', $userId)
            ->executeQuery()->fetchAssociative();

        // Kiểm tra trạng thái đơn hàng
        if (!$order || $order['status'] != 'completed') {
            $_SESSION['flash_error'] = 'Không thể yêu cầu hoàn đơn này.';
            return redirect('/orders');
        }

        // Trả về view với thông tin đơn hàng
        return view('Client.returnForm', compact('order'));
    }
    public function submitReturn($id)
    {
        // Lấy thông tin người dùng từ session
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            return redirect('/login');
        }
    
        // Kiểm tra lý do hoàn hàng
        $reason = trim($_POST['reason'] ?? '');
        if (!$reason) {
            $_SESSION['flash_error'] = 'Vui lòng nhập lý do hoàn hàng.';
            return redirect("/order/{$id}/return");
        }
    
        // Kết nối DB
        $db = (new Product())->getConnection();
    
        // Kiểm tra trạng thái đơn hàng
        $order = $db->fetchAssociative('SELECT status, user_id FROM orders WHERE id = ?', [$id]);
        if (!$order) {
            $_SESSION['flash_error'] = 'Đơn hàng không tồn tại.';
            return redirect('/orders');
        }
    
        // Kiểm tra nếu đơn hàng không phải của người dùng này hoặc không phải trạng thái 'completed'
        if ($order['user_id'] != $userId) {
            $_SESSION['flash_error'] = 'Đơn hàng không thuộc về bạn.';
            return redirect('/orders');
        }
    
        if ($order['status'] != 'completed') {
            $_SESSION['flash_error'] = 'Không thể yêu cầu hoàn hàng khi đơn hàng chưa hoàn thành.';
            return redirect("/order/{$id}/return");
        }
    
        // Cập nhật trạng thái đơn hàng và lý do hoàn
        $data = [
            'status'        => 'returned',
            'return_reason' => $reason,
        ];
    
        // Thực hiện update đơn hàng
        $affected = $db->update(
            'orders',
            $data,
            [
                'id'      => $id,
                'user_id' => $userId,
                'status'  => 'completed'
            ]
        );
    
        // Kiểm tra kết quả cập nhật
        if ($affected > 0) {
            $_SESSION['flash_success'] = 'Yêu cầu hoàn hàng đã được gửi.';
        } else {
            $_SESSION['flash_error'] = 'Không thể gửi yêu cầu hoàn (có thể đơn chưa hoàn thành hoặc không thuộc về bạn).';
        }
    
        return redirect('/orders');
    }
    
    /**
     * Hiển thị chi tiết một đơn đã đặt
     */
    public function orderDetail($orderId)
    {
        // đảm bảo session đã khởi
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash_error'] = "Vui lòng đăng nhập!";
            return redirect('/login');
        }

        $db = (new Product())->getConnection();

        // Lấy thông tin order
        $order = $db->createQueryBuilder()
            ->select('*')
            ->from('orders')
            ->where('id = :oid')
            ->andWhere('user_id = :uid')
            ->setParameter('oid', $orderId, \Doctrine\DBAL\ParameterType::INTEGER)
            ->setParameter('uid', $userId, \Doctrine\DBAL\ParameterType::INTEGER)
            ->executeQuery()
            ->fetchAssociative();

        if (!$order) {
            $_SESSION['flash_error'] = "Không tìm thấy đơn #{$orderId}.";
            return redirect('/orders');
        }

        // Lấy items của order
        $items = $db->createQueryBuilder()
            ->select('oi.*', 'pv.price', 'p.name AS product_name', 'c.name AS color_name', 's.name AS size_name')
            ->from('order_items', 'oi')
            ->join('oi', 'product_variants', 'pv', 'oi.product_variant_id = pv.id')
            ->join('pv', 'products', 'p', 'pv.product_id = p.id')
            ->join('pv', 'colors', 'c', 'pv.color_id = c.id')
            ->join('pv', 'sizes', 's', 'pv.size_id = s.id')
            ->where('oi.order_id = :oid')
            ->setParameter('oid', $orderId, \Doctrine\DBAL\ParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        return view('Client.orderDetail', compact('order', 'items'));
    }

    // Lấy tất cả đơn hàng của người dùng
    public function orderList()
    {
        // Kiểm tra session người dùng
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            $_SESSION['flash_error'] = "Vui lòng đăng nhập để xem đơn hàng!";
            return redirect('/auth');
        }

        $db = (new Product())->getConnection();
        $queryBuilder = $db->createQueryBuilder();

        // Lấy tất cả đơn hàng của người dùng
        $queryBuilder
            ->select('o.id', 'o.total_price', 'o.status', 'o.created_at')
            ->from('orders', 'o')
            ->where('o.user_id = :user_id')
            ->setParameter('user_id', $userId);

        $ordersRaw = $queryBuilder->executeQuery()->fetchAllAssociative();

        // Convert về dạng object và xử lý ngày giờ
        $orders = array_map(function ($order) {
            return (object)[
                'id' => $order['id'],
                'total_price' => $order['total_price'] ?? 0,
                'status' => $order['status'],
                'created_at' => !empty($order['created_at']) ? new \DateTime($order['created_at']) : null,
            ];
        }, $ordersRaw);

        return view('Client.orders', compact('orders'));
    }
}
