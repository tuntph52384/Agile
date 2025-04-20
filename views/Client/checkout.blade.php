@extends('Client.layouts.main')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-primary">Thanh Toán</h2>

        {{-- Flash message --}}
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

        {{-- Form thanh toán --}}
        <form action="/checkout" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Họ và tên *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Số điện thoại *</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Địa chỉ giao hàng *</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Ghi chú đơn hàng</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
            </div>

            {{-- Bảng sản phẩm --}}
            <h4 class="mb-3">Đơn hàng của bạn</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Phân loại</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($cartItems as $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ $item['color_name'] }} / {{ $item['size_name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['price'], 0, ',', '.') }} ₫</td>
                            <td>{{ number_format($subtotal, 0, ',', '.') }} ₫</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">Tạm tính:</td>
                        <td>{{ number_format($total, 0, ',', '.') }} ₫</td>
                    </tr>
                    @php
                        $discount = 0;
                        $voucher = isset($_SESSION['voucher']) ? $_SESSION['voucher'] : null;
                        if ($voucher && isset($voucher['discount_percent'])) {
                            $discount = $total * ($voucher['discount_percent'] / 100);
                        }
                        $finalTotal = $total - $discount;
                    @endphp
                    @if($discount > 0)
                        <tr>
                            <td colspan="4" class="text-end">Giảm giá ({{ $voucher['discount_percent'] }}%):</td>
                            <td>-{{ number_format($discount, 0, ',', '.') }} ₫</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="text-end"><strong>Thành tiền:</strong></td>
                        <td><strong>{{ number_format($finalTotal, 0, ',', '.') }} ₫</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-end">
                <a href="/cart" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
                <button type="submit" class="btn btn-success">Đặt hàng</button>
            </div>
        </form>
    </div>
</section>
@endsection
