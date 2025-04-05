<header class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
    <div class="carousel-inner">
        @foreach ($banners as $index => $banner)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ $banner['img'] }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                <div class="carousel-caption d-none d-md-block">
                    <h1 class="display-4 fw-bolder text-white">{{ $banner['title'] }}</h1>
                    <p class="lead fw-normal text-white-50 mb-0">{{ $banner['description'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target=".carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target=".carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</header>
<style>
    .carousel-item img {
    object-fit: cover; /* Đảm bảo ảnh không bị méo */
    height: 400px; /* Điều chỉnh chiều cao banner */
    width: 100%; /* Chiều rộng 100% */
}

</style>