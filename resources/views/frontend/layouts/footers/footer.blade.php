<!-- Remove the container if you want to extend the Footer to full width. -->
<footer class="footer text-center text-lg-start text-white">
    <div class="container-fluid footer-bg">
        <div class="container-xxl">
            <div class="row py-md-5">
                <div class="col-lg-3 col-md-4 col-12 mt-lg-2 mt-3">
                    <img class="footer-logo w-100" src="{{ asset('/images/frontend/home/logo.png') }}" alt="" srcset="">
                </div>
                <div class="col-lg-3 col-md-4 col-7 mt-lg-2 mt-3">
                    <div class="row">
                        <div class="col-2 p-0">
                            <img class="footer-icon" src="{{ asset('/images/frontend/home/call_16.png') }}" alt=""
                                srcset="">
                        </div>
                        <div class="col-10 text-start">
                            <h4 class="text-uppercase font-color-2">Call Us</h4>
                            <p class="mb-0 fs-5"><a href="tel:+91452654443">(+91) XXXXXXXXX</a></p>
                            <p class="fs-5"><a href="tel:+91452654443">(+91) XXXXXXXXX</a></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-7 mt-lg-2 mt-3">
                    <div class="row">
                        <div class="col-2 p-1">
                            <img class="footer-icon" src="{{ asset('/images/frontend/home/email_19.png') }}" alt=""
                                srcset="">
                        </div>
                        <div class="col-10 text-start">
                            <h4 class="text-uppercase font-color-2">Mail Us</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-7 mt-lg-2 mt-3">
                    <div class="row">
                        <div class="col-2 p-0">
                            <img class="footer-icon" src="{{ asset('/images/frontend/home/follow_22.png') }}" alt=""
                                srcset="">
                        </div>
                        <div class="col-10 font-color-2">
                            <h4 class="text-uppercase text-start font-color-2">Follow Us</h4>
                            <div class="row mt-3">
                                <div class="col-2 p-0">
                                    <a href="#" target="_blank">
                                        <img src="{{ asset('/images/frontend/home/icon-01.png') }}" alt="" class="w-75">
                                    </a>
                                </div>
                                <div class="col-2 p-0">
                                    <a href="#" target="_blank">
                                        <img src="{{ asset('/images/frontend/home/icon-02.png') }}" alt="" class="w-75">
                                    </a>
                                </div>
                                <div class="col-2 p-0">
                                    <a href="#" target="_blank">
                                        <img src="{{ asset('/images/frontend/home/icon-03.png') }}" alt="" class="w-75">
                                    </a>
                                </div>
                                <div class="col-2 p-0">
                                    <a href="#" target="_blank">
                                        <img src="{{ asset('/images/frontend/home/icon-04.png') }}" alt="" class="w-75">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-8 col-12 mt-lg-5 mt-4">
                    <div class="row text-start">
                        <div class="col-md-4 col-6 d-flex align-items-end">
                            <img class="footer-qr " src="{{ asset('/images/frontend/home/qr_03.png') }}" alt=""
                                srcset="">
                        </div>
                        <div class="col-md-8 col-12 mt-lg-0 mt-4">
                            <h4 class="text-uppercase  font-color-2 fw-bold">About us</h4>
                            <p class="">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12 mt-lg-5 mt-2">

                    <div class="row text-start">
                        <div class="col-md-1 col-0"></div>
                        <div class="col-11 px-lg-4">
                            <ul class="p-0 text-start">
                                <h4 class="fw-bold text-uppercase font-color-2 ">Our Products</h4>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12 mt-lg-5 mt-2">
                    <div class="row text-start">
                        <div class="col-md-1 col-0"></div>
                        <div class="col-11">
                            <ul class="p-0">
                                <h4 class="fw-bold text-uppercase font-color-2 ">Quick Links</h4>
                                <li><a href="{{ route('web.home')}}">Home</a></li>
                                <li><a href="{{ route('web.shop')}}">Our Products</a></li>
                                <li><a href="{{ route('web.about.us')}}">About Us</a></li>
                                <li><a href="{{ route('web.blog')}}">Our Blog</a></li>
                                <li><a href="{{ route('web.contact.us')}}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12 mt-lg-5 mt-2">
                    <div class="col-12 text-start">
                        <h4 class="fw-bold text-uppercase font-color-2">Visit Us</h4>
                        
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-12 text-end">
                <p class="font-color-2 fs-6 pb-0">
                    <a href="{{ route('web.terms.conditions')}}" class="font-color-2">Terms and Conditions </a> |
                    <a href="{{ route('web.privacy.policy')}}" class="font-color-2">Privacy Policy </a>|
                    
                </p>
            </div>
        </div>
        </div>
    </div>
    <div class="col-12 mt-3 footer-social-icon">
        <a href="https://wa.me/+947XXXXXXXX" target="_blank" rel="noopener">
            <img src="{{ asset('/images/frontend/home/whatsapp.png') }}" class="icon-social d-block w-25"
                alt="social media">
        </a>
    </div>
    <div class="container-fluid copyright-bg">
        <div class="container-xxl">
            <div class="row">
                <div class="col-md-6">
                    <p class="mt-3 font-color-2 mb-0">
                        Copyright © <script>
                        document.write(new Date().getFullYear())
                        </script> 
                    </p>
                </div>
                <div class="col-md-6">
                    <p class=" font-color-2 mt-3 text-lg-end text-center">
                        Design & Developed by 
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

