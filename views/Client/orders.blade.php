@extends('Client.layouts.main')

@section('content')
    <section class="py-5">
        <div class="container">
            <h2>Đơn hàng của bạn</h2>

            @if (session('flash_success'))
                <div class="alert alert-success">{{ session('flash_success') }}</div>
            @endif

            {{-- @if (session('flash_error'))
      <div class="alert alert-danger">{{ session('flash_error') }}</div>
    @endif --}}

            @if (count($orders) > 0)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Đơn Hàng</th>
                            <th>Ngày Đặt</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    @if ($order->created_at)
                                        {{ $order->created_at->format('d/m/Y') }}
                                    @else
                                        <span>Không có thông tin ngày</span>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($order->total_price ?? 0) }} VNĐ
                                </td>
                                <td>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                        @break

                                        @case('processing')
                                            <span class="badge bg-info text-dark">Đang xử lý</span>
                                        @break

                                        @case('completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @break

                                        @case('canceled')
                                            <span class="badge bg-danger">Đã huỷ</span>
                                        @break

                                        @case('returned')
                                            <span class="badge bg-danger">Đã hoàn hàng</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">Không rõ</span>
                                    @endswitch
                                </td>

                                <td>
                                    <a href="/orders/{{ $order->id }}" class="btn btn-sm btn-info">Xem</a>

                                    @if ($order->status == 'pending')
                                        <form method="POST" action="/order/{{ $order->id }}/cancel"
                                            onsubmit="return confirm('Bạn chắc chắn muốn huỷ đơn này?')">
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm">Huỷ đơn</button>
                                        </form>
                                    @elseif($order->status == 'completed')
                                        <form method="POST" action="/order/{{ $order->id }}/return"
                                            onsubmit="return confirm('Bạn chắc chắn muốn hoàn hàng cho đơn này?')">
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <button type="submit" class="btn btn-warning btn-sm">Hoàn hàng</button>
                                        </form>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Bạn chưa có đơn hàng nào.</p>
            @endif
        </div>
    </section>
@endsection
