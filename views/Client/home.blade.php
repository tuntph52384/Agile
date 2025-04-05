@extends('Client.layouts.main')

@section('content')
    @include('Client.layouts.partials.header')

    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">

            {{-- Form tìm kiếm --}}
            <form action="/products" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm sản phẩm..."
                        value="{{ $keyword ?? '' }}">
                    <button class="btn btn-outline-dark" type="submit">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>

            {{-- Danh sách sản phẩm --}}
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center gap-4">
                @foreach ($products as $product)
                <div class="col mb-5">
                    <div class="card h-100">
                        {{-- Kiểm tra nếu có thông tin giảm giá --}}
                        @if (isset($product['is_sale']) && $product['is_sale'] && isset($product['price_sale']) && $product['price_sale'])
                            <div class="card bg-danger text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                        @endif
            
                        <img class="card-img-top" src="{{ $product['img_thumbnail'] ?? 'default_image_path.jpg' }}" alt="{{ $product['name'] }}" style="height: 200px; object-fit: cover">
            
                        <div class="card-body p-4">
                            <div class="text-center">
                                <h5 class="fw-bolder">{{ $product['name'] }}</h5>
            
                                {{-- Kiểm tra giá sản phẩm --}}
                                @if (isset($product['is_sale']) && $product['is_sale'] && isset($product['price'], $product['price_sale']))
                                    <span class="text-muted text-decoration-line-through">{{ number_format($product['price'], 0, ',', '.') }} ₫</span>
                                    <span class="text-danger fw-bolder">{{ number_format($product['price_sale'], 0, ',', '.') }} ₫</span>
                                @elseif (isset($product['price']))
                                    <span class="text-success fw-bolder">{{ number_format($product['price'], 0, ',', '.') }} ₫</span>
                                @else
                                    <span class="text-muted">Liên hệ để biết giá</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center">
                                <a class="btn btn-outline-dark mt-auto" href="/products/{{ $product['id'] }}">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            </div>
        </div>
    </section>
@endsection
