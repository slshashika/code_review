<h2 class="text-center fs-1 mt-5">Our Brand Manufacturers</h2>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <section class="regular slider">
                @foreach($brands as $brand)
                <a href="{{ route('web.manufacturer', ['id' => $brand->id])}}">
                    <div class="single-logo px-3">
                        <img class="w-100" src="@if($brand->brand_logo != null){{ $brand->brand_logo }} @endif"
                            alt="Brand Logo">
                    </div>
                </a>
                @endforeach
            </section>
        </div>
    </div>
</div>