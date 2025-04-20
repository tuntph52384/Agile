@extends('Client.layouts.main')

@section('content')
<section class="py-5">
  <div class="container text-center">
    <?php
      if (session_status() === PHP_SESSION_NONE) {
          session_start();
      }
    ?>
    @if(isset($_SESSION['flash_success']))
      <div class="alert alert-success">
        {{ $_SESSION['flash_success'] }}
      </div>
      <?php unset($_SESSION['flash_success']); ?>
    @endif

    <h2 class="mt-4">Cảm ơn bạn đã đặt hàng!</h2>
    <p>Chúng tôi đã nhận được đơn hàng của bạn. Vui lòng chờ xác nhận.</p>
    <a href="/" class="btn btn-primary mt-3">Về trang chủ</a>
  </div>
</section>
@endsection
