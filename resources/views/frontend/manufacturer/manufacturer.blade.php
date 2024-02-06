@extends('frontend.app')

@section('content')

<div id="" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('/images/frontend/about-us/page-banner.png') }}" class="d-block w-100" alt="Page Banner">
        </div>
    </div>
</div>

<section class="">
    <div class="container-xxl py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="subscribe-heading">Manufacturer</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 p-md-3 rounded-0">
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-lg-6 col-12 text-center">
                                <h2 class="card-title">{{$brand->brand_name}}</h2>
                                <img src="{{ asset($brand->brand_logo) }}" class="w-50" alt="...">
                            </div>
                            <div class="col-lg-6 col-12">
                                <p class="card-text fs-6">{{$brand->description}}</p>
                            </div>
                        </div>
                        <!-- inner row -->
                        <div class="row">
                            <div class="col-12">
                                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($brandImages as $index => $brandImage)
                                        <div class="carousel-item @if($index === 0) active @endif"">
                                            <!-- inner row -->
                                            <div class="row">
                                                @foreach($brandImage as $image)
                                                <div class="col-3">
                                                    <img src="{{ asset($image->src ?? '/images/default.png') }}"
                                                        class="w-100" alt="manufactures">
                                                </div>
                                                @endforeach
                                            </div>
                                         </div>
                                         @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

