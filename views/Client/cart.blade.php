@extends('Client.layouts.main')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-primary">Giỏ Hàng</h2>

        {{-- Thông báo flash (thành công hoặc lỗi) --}}
        @if(isset($_SESSION['flash_success']))
            <div class="alert alert-success">
                {{ $_SESSION['flash_success'] }}
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        @endif

        @if(isset($_SESSION['flash_error']))
            <div class="alert alert-danger">
                {{ $_SESSION['flash_error'] }}
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        @endif

        {{-- Form voucher --}}
        <div class="mb-4">
            <form action="/apply-voucher" method="POST" class="d-flex align-items-center">
                @csrf
                <input type="text" name="voucher_code" class="form-control me-2" placeholder="Nhập mã voucher" style="max-width: 300px;">
                <button type="submit" class="btn btn-warning">Áp dụng Voucher</button>
            </form>
        </div>

        @if (empty($cartItems))
            <div class="alert alert-info">Giỏ hàng của bạn đang trống!</div>
        @else
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Hình</th>
                        <th>Sản phẩm</th>
                        <th>Màu / Size</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td width="100">
                                <img src="{{ file_url($item['img_thumbnail']) }}" alt="thumb" width="80" class="rounded">
                            </td>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ $item['color_name'] }} / {{ $item['size_name'] }}</td>
                            <td>{{ number_format($item['price'], 0, ',', '.') }} ₫</td>
                            <td>
                                <!-- Form cập nhật số lượng -->
                                <form action="/update-cart" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control me-2" style="width: 80px;">
                                    <input type="hidden" name="variant_id" value="{{ $item['id'] }}">
                                    <button type="submit" class="btn btn-primary btn-sm">Sửa</button>
                                </form>
                            </td>
                            <td>{{ number_format($item['total'], 0, ',', '.') }} ₫</td>
                            <td>
                                <!-- Form xóa sản phẩm khỏi giỏ hàng -->
                                <form action="/remove-from-cart" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    @csrf
                                    <input type="hidden" name="variant_id" value="{{ $item['id'] }}">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <?php
            // Tính tổng tiền giỏ hàng
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['total'];
            }
            $discount = 0;
            // Nếu có voucher, áp dụng giảm giá theo phần trăm (ví dụ 10%)
            if (isset($_SESSION['voucher']) && isset($_SESSION['voucher']['discount_percent'])) {
                $discount = ($subtotal * $_SESSION['voucher']['discount_percent']) / 100;
            }
            $total = $subtotal - $discount;
            ?>

            <div class="mb-3">
                <p>Subtotal: <strong>{{ number_format($subtotal, 0, ',', '.') }} ₫</strong></p>
                @if(isset($_SESSION['voucher']))
                    <p>Voucher ({{ $_SESSION['voucher']['code'] }}) - giảm {{ $_SESSION['voucher']['discount_percent'] }}%: <strong>-{{ number_format($discount, 0, ',', '.') }} ₫</strong></p>
                @endif
                <p>Total: <strong>{{ number_format($total, 0, ',', '.') }} ₫</strong></p>
            </div>

            <div class="text-end">
                <a href="/" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
                <a href="/checkout" class="btn btn-success">Thanh toán</a>
            </div>
        @endif
    </div>
</section>
@endsection
