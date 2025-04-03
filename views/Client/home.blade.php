@extends('Client.layouts.main')

@section('content')
    @include('Client.layouts.partials.header')

    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                @foreach ($products as $product)
                    <div class="col mb-5">
                        <div class="card h-100">
                            @if (isset($product['is_sale']) && $product['price_sale'])
                                <div class="card bg-drak text-black position-absolute" style="top: 0.5rem; right: 0.5rem">Sale
                                </div>
                            @endif

                            <img class="card-img-top" src="{{ $product['img_thumbnail'] }}" alt="{{ $product['name'] }}"
                                style="height: 200px; object-fit: cover">

                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder">{{ $product['name'] }}</h5>

                                    @if ($product['is_sale'])
                                        <span
                                            class="text-muted text-decoration-line-through">{{ number_format($product['price'], 0, ',', '.') }}
                                            ₫</span>
                                        <span
                                            class="text-danger fw-bolder">{{ number_format($product['price_sale'], 0, ',', '.') }}
                                            ₫</span>
                                    @else
                                        <span
                                            class="text-success fw-bolder">{{ number_format($product['price'], 0, ',', '.') }}
                                            ₫</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto"
                                        href="/products/{{ $product['id'] }}">Xem chi tiết</a></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
