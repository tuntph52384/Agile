@extends('Admin.layouts.main')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>
    <h1 class="h2">{{ $title }}</h1>

    <div class="row">
        <div class="col-12 mb-4 mb-lg-0">
            <div class="card">
                <a href="/admin/categories/create" class="btn btn-sm btn-success">Create</a>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Trường dữ liệu</th>
                                    <th scope="col">Giá trị</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($category as $field => $value)
                                    <tr>
                                        <td>{{ strtoupper($field) }}</td>
                                        <td>
                                            @switch($field)
                                                @case('img')
                                                    <img src="{{ file_url($value) }}" width="100px" alt="">
                                                @break

                                                @case('is_active')
                                                    @if ($value)
                                                        <span class="badge bg-info">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                @break

                                                @default
                                                    {{ $value }}
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <a href="/admin/categories" class="btn btn-warning">
                            Back to list
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection