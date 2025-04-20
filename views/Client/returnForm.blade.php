@extends('Client.layouts.main')
@section('content')
<section class="py-5">
  <div class="container">
    <h2>Yêu cầu hoàn hàng – Đơn #{{ $order['id'] }}</h2>

    @if(isset($_SESSION['flash_error']))
      <div class="alert alert-danger">{{ $_SESSION['flash_error'] }}</div><?php unset($_SESSION['flash_error']); ?>
    @endif

    <form action="/order/{{ $order['id'] }}/return" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label">Lý do hoàn hàng *</label>
        <textarea name="reason" class="form-control" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
      <a href="/orders" class="btn btn-secondary">Quay lại</a>
    </form>
  </div>
</section>
@endsection
