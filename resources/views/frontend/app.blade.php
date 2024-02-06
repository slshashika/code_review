<!doctype html>
<html lang="en">

<head>
    <!-- Analytics tag -->
    @if (isset($commonContent))
    @if ($commonContent['analytics'] != null)
    {!!$commonContent['analytics']['description']!!}
    @endif
    @endif
    <title>@yield('title', $commonContent['siteName']['description'])</title>
    <meta name="description" content="@yield('description','')">
    <meta name="keywords" content="@yield('keywords','')">

    <link rel="canonical" href="@yield('canonical_url','')">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset($commonContent['siteLogo']['description']) }}">

    <!-- Bootstrap CDN  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <link href="{{ asset('assets/css/swiper.min.css') }}" rel="stylesheet">
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{asset('/assets/css/libs.bundle.css')}}" />

    <!-- Custom Styles -->
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
   
    <!-- Owl Stylesheets -->
    <link rel="stylesheet" href="{{ asset('/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/owl.theme.default.min.css') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- toastr styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Red+Hat+Display:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Select 2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- slick slider styles -->
    <link rel="stylesheet" href="{{ asset('/assets/slickslider/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/slickslider/css/slick-theme.css') }}">
    
    <!-- fonts -->
    <script src="{{url('/js/vendor.bundle.js')}}"></script>

    @yield('css')
</head>

<body class="">

    @include('frontend.layouts.headers.header')
    <!-- Page Content -->

    @yield('content')

    <!-- / Content-->

    @include('frontend.layouts.footers.footer')
   
    <!-- Vendor JS -->
    <script src="{{url('/js/vendor.bundle.js')}}"></script>

    <!-- jQuery -->
    <script src="{{url('/plugins/jquery/jquery.min.js')}}"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.4.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Owl Carousel -->
    <script src="jquery.min.js"></script>
    <script src="{{ asset('/assets/js/owl.carousel.min.js') }}"></script>

    <!-- CKEditor -->
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

    <!-- Bootstraps JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- slick JS -->
    <script src="{{ asset('/assets/slickslider/js/slick.js') }}"></script>

    <!-- Custom JS  -->
    <script src="{{ asset('/js/app.js') }}"></script>
    <script src="{{ asset('/js/customValidation.js') }}"></script>
   
    @yield('scripts')

    <!-- back page reload -->
    <script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            location.reload(true);
        }
    });
    </script>


    <!-- owl carousel homepg -->
    <script>
    $('.brand-carousel').ready(function() {
        var owl = $('.owl-carousel');
        owl.owlCarousel({
            loop: true,
            nav: false, // Disable default navigation
            margin: 10,
            autoplay: true,
            dots: false,
            autoPlaySpeed: 5000,
            navRewind: false,
            lazyLoad: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        })

        // Custom navigation controls
        $('.owl-prev').click(function() {
            $('.owl-carousel').trigger('prev.owl.carousel');
        });

        $('.owl-next').click(function() {
            $('.owl-carousel').trigger('next.owl.carousel');
        });


    })
    </script>
    <script>
    $(document).ready(function() {
        jQuery('.brand-carousel').owlCarousel({
            loop: true,
            margin: 10,
            autoplay: true,
            autoplayTimeout: 2000,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 3,
                },
                1000: {
                    items: 3,
                    margin: 20
                }
            }
        })
    });
    </script>

</body>

</html>