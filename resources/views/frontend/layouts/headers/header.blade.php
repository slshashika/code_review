<!-- Navigation -->
<style>
    .right-corner {
    position: absolute;
    top: 20;
    right: 0;
}

.active-link {
        text-decoration: underline;
        font-weight: bold;
    }
</style>
<div class="container navigation-row">
    <div class="row fixed-top d-flex align-items-center navigation-menu px-md-0 px-3">
        <div class="col-lg-3 col-md-4 col-6 ps-md-5 ms-md-5">
            <a class="navbar-brand p-0" href="/"><img class="nav-logo w-75"
                    src="{{ asset('/images/frontend/home/urumaya-logo.png') }}" alt="" srcset=""></a>
        </div>
        <div class="col-lg-6 col-md-5 col-2">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <button class="navbar-toggler resposnive-nav-menu border-0 p-0 nav-mobile-nav" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon icon-mobile-nav"></span>
                </button>
                <div class="collapse navbar-collapse responsive-nav" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto nav-ul">
                        <li class="nav-item">
                            <a class="nav-link text-uppercase font-color-1 fw-bold fs-6 @if (\Request::is('shop*')) active active-link @endif"
                                href="{{ route('web.shop')}}">our
                                products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase font-color-1 fw-bold fs-6 @if (\Request::is('about-us')) active active-link @endif"
                                href="{{ route('web.about.us')}}">About
                                Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase font-color-1 fw-bold fs-6 @if (\Request::is('blog')) active active-link @endif"
                                href="{{ route('web.blog')}}">Our
                                blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase font-color-1 fw-bold fs-6 @if (\Request::is('contact-us')) active active-link @endif"
                                href="{{ route('web.contact.us')}}">Contact
                                Us</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="col-lg-2 col-md-2 col-4">
            <div class="row d-flex align-items-center justify-content-end">
                <div class="col-lg-4 col-6 px-0">
                    <!-- products count in cart -->
                    @if(session()->has('cart') && count(session()->get('cart')) > 0)
                    <span class="badge cart_count_badge text-danger header-cart-count">{{ count(session()->get('cart'))  }}</span>
                    @else
                    <span class="badge cart_count_badge text-danger header-cart-count"> 0 </span>
                    @endif
                    <span id="sidecart"> @include('frontend.cart.minicart')</span>

                </div>
                <div class="col-lg-4 col-6 px-0">
                    @if (Auth::user())
                    @include('frontend.login_registration.logout')
                    @else
                    @include('frontend.login_registration.login_registration')
                    @endif
                </div>
                @if (Auth::user())
                <div class="col-lg-4 col-6 px-0">
                    <b>Hi {{Auth::user()->first_name}}</b>
                </div>
                @endif
            </div>
        </div>
        <div class="container">
    <div class="col-md-2 right-corner">
        @include('frontend.common.alerts')
    </div>
</div>
    </div>
</div>