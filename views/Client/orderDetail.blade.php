@extends('Client.layouts.main')
@section('content')
<section class="py-5">
  <div class="container">
    <h2>Chi tiết đơn #{{ $order['id'] }}</h2>
    <p>Ngày đặt: {{ $order['created_at'] }} | Trạng thái: {{ ucfirst(str_replace('_',' ',$order['status'])) }}</p>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Sản phẩm</th><th>Phân loại</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $it)
        @php
          $sub = $it['price'] * $it['quantity'];
        @endphp
        <tr>
          <td>{{ $it['product_name'] }}</td>
          <td>{{ $it['color_name'] }} / {{ $it['size_name'] }}</td>
          <td>{{ $it['quantity'] }}</td>
          <td>{{ number_format($it['price'],0,',','.') }} ₫</td>
          <td>{{ number_format($sub,0,',','.') }} ₫</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" class="text-end"><strong>Tổng:</strong></td>
          <td><strong>{{ number_format($order['total_price'],0,',','.') }} ₫</strong></td>
        </tr>
      </tfoot>
    </table>

    <a href="/orders" class="btn btn-secondary">Quay lại danh sách đơn</a>
  </div>
</section>
@endsection
