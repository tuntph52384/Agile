@extends('Client.layouts.main')

@section('content')
    @include('Client.layouts.partials.header')

    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-4">

            {{-- Form lọc sản phẩm --}}
            <form method="GET" action="" class="row g-3 align-items-end mb-4 bg-light p-4 rounded shadow-sm">
                {{-- Ô tìm kiếm --}}
                <div class="col-md-4">
                    <label for="keyword" class="form-label fw-semibold">Tìm kiếm</label>
                    <input type="text" name="keyword" class="form-control" placeholder="Nhập từ khóa..."
                        value="{{ $keyword }}">
                </div>

                {{-- Dropdown chọn Size --}}
                <div class="col-md-3">
                    <label for="size" class="form-label fw-semibold">Size</label>
                    <select name="size" class="form-select">
                        <option value="">-- Tất cả Size --</option>
                        @foreach ($sizes as $s)
                            <option value="{{ $s['id'] }}" {{ $s['id'] == $size ? 'selected' : '' }}>
                                {{ $s['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown chọn Màu --}}
                <div class="col-md-3">
                    <label for="color" class="form-label fw-semibold">Màu sắc</label>
                    <select name="color" class="form-select">
                        <option value="">-- Tất cả Màu --</option>
                        @foreach ($colors as $c)
                            <option value="{{ $c['id'] }}" {{ $c['id'] == $color ? 'selected' : '' }}>
                                {{ $c['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Dropdown chọn Loại sản phẩm --}}
                <div class="col-md-3">
                    <label for="category" class="form-label fw-semibold">Loại sản phẩm</label>
                    <select name="category" class="form-select">
                        <option value="">-- Tất cả loại --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}" {{ $cat['id'] == $category ? 'selected' : '' }}>
                                {{ $cat['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- Nút lọc --}}
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill me-1"></i> Lọc</button>
                </div>
            </form>

            {{-- Danh sách sản phẩm --}}
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                @foreach ($products as $product)
                    <div class="col mb-5">
                        <div class="card h-100 shadow-sm">
                            @if (!empty($product['is_sale']) && !empty($product['price_sale']))
                                <div class="badge bg-danger position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                            @endif

                            <img class="card-img-top" src="{{ $product['img_thumbnail'] ?? 'default.jpg' }}" alt="{{ $product['name'] }}" style="height: 200px; object-fit: cover">

                            <div class="card-body text-center">
                                <h5 class="fw-bold">{{ $product['name'] }}</h5>

                                @if (!empty($product['is_sale']) && isset($product['price_sale']))
                                    <div>
                                        <span class="text-muted text-decoration-line-through">{{ number_format($product['price'], 0, ',', '.') }} ₫</span>
                                        <br>
                                        <span class="text-danger fw-bold">{{ number_format($product['price_sale'], 0, ',', '.') }} ₫</span>
                                    </div>
                                @elseif (isset($product['price']))
                                    <span class="text-success fw-bold">{{ number_format($product['price'], 0, ',', '.') }} ₫</span>
                                @else
                                    <span class="text-muted">Liên hệ để biết giá</span>
                                @endif
                            </div>

                            <div class="card-footer bg-transparent border-0 text-center">
                                <a class="btn btn-outline-dark btn-sm" href="/products/{{ $product['id'] }}">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
@endsection
