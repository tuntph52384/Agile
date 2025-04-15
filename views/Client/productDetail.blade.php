@extends('Client.layouts.main')

@section('content')
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row g-4 align-items-center">
                <!-- Product Image -->
                <div class="col-md-6 text-center">
                    <img class="img-fluid rounded shadow-sm border" src="{{ file_url($product['p_img_thumbnail']) }}"
                        alt="Product Image" style="max-width: 100%; height: auto;">
                </div>

                <!-- Product Details -->
                <div class="col-md-6">
                    <h1 class="display-5 fw-bolder text-primary">{{ $product['p_name'] }}</h1>

                    <p class="text-muted">Danh mục: <span class="fw-semibold">{{ $product['c_name'] }}</span></p>

                    <div class="mb-3">
                        <span class="h5">Giá:</span>
                        @if ($product['p_is_sale'])
                            <span class="text-muted text-decoration-line-through">
                                {{ number_format($product['p_price'], 0, ',', '.') }} ₫
                            </span>
                            <span class="text-danger fw-bold h4">
                                {{ number_format($product['p_price_sale'], 0, ',', '.') }} ₫
                            </span>
                        @else
                            <span class="text-success fw-bold h4">
                                {{ number_format($product['p_price'], 0, ',', '.') }} ₫
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h5 class="text-secondary">Mô tả ngắn:</h5>
                        <p class="text-dark">{{ $product['p_overview'] }}</p>
                    </div>

                    {{-- Danh sách biến thể --}}
                    {{-- Danh sách biến thể kèm form --}}
                    @if (!empty($variants))
                        <form action="/add-to-cart" method="POST">
                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                            @csrf

                            <div class="mb-4">
                                <h5 class="text-secondary">Chọn phiên bản:</h5>
                                <select class="form-select" name="variant_id" required>
                                    <option value="">-- Chọn màu / size --</option>
                                    @foreach ($variants as $v)
                                        <option value="{{ $v['id'] }}">
                                            {{ $v['color_name'] }} - {{ $v['size_name'] }} (Còn: {{ $v['stock'] }}) -
                                            {{ number_format($v['price'], 0, ',', '.') }} ₫
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex gap-3">
                                <input class="form-control text-center border" name="quantity" type="number" value="1"
                                    min="1" style="max-width: 4rem">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                                </button>
                            </div>
                        </form>
                    @endif


                    <!-- Product Description -->
                    <div class="row mt-5">
                        <div class="col-lg-12">
                            <h3 class="fw-bold text-dark">Mô tả sản phẩm</h3>
                            <p class="text-muted">{{ $product['p_content'] }}</p>
                        </div>
                    </div>
                </div>
    </section>
@endsection
