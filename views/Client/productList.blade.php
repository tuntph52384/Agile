@extends('Client.layouts.main')

@section('content')
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
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
                        <div class="card h-100 shadow-sm border-light rounded">
                            @if ($product['is_sale'])
                                <div class="badge bg-danger text-white position-absolute"
                                    style="top: 0.5rem; right: 0.5rem">Sale</div>
                            @endif

                            <img class="card-img-top" src="{{ $product['img_thumbnail'] }}" alt="{{ $product['name'] }}"
                                style="height: 250px; object-fit: cover">

                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder">{{ $product['name'] }}</h5>

                                    @if ($product['is_sale'])
                                        <span class="text-muted text-decoration-line-through">
                                            {{ number_format($product['price'], 0, ',', '.') }} ₫
                                        </span>
                                        <span class="text-danger fw-bolder">
                                            {{ number_format($product['price_sale'], 0, ',', '.') }} ₫
                                        </span>
                                    @else
                                        <span class="text-success fw-bolder">
                                            {{ number_format($product['price'], 0, ',', '.') }} ₫
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center">
                                    <a class="btn btn-outline-primary mt-auto" href="/products/{{ $product['id'] }}">
                                        <i class="bi bi-info-circle"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
@endsection
