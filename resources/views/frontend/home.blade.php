@extends('frontend.app')

@section('content')

    @include('frontend.layouts.sliders.slider')
    @include('frontend.layouts.components.our_products')
    @include('frontend.layouts.components.home_banner')
    @include('frontend.layouts.components.brand_carousel')
    @include('frontend.layouts.components.subscribe')

@endsection