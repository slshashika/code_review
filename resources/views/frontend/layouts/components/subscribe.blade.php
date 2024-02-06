<div class="container-xxl py-5">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-lg-4 col-md-6 col-12">
            <h3 class="subscribe-heading">
                Please Subscribe To Get Updates
            </h3>
            <h6 class="text-secondary">Signup With Your Email To Get Latest Updates</h6>
        </div>
        <div class="col-lg-4 col-md-6 col-12">
            <div class="row mt-2">
                
            </div>
            <!-- Subscribe form  -->
                <form action="{{ route('web.subscribe') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control rounded-0 border-0 subscribe-border p-2 fs-5"
                            id="email" placeholder="Enter Your Email" required>
                    </div>
                    
                    <div class="mb-3 mt-1 text-end">
                        <div class="g-recaptcha" data-sitekey="6LdiXFQoAAAAALu89h0aQZN6xo7accXTAb0qr30N">
                        </div>
                    </div>

                    <button type="submit" class="btn w-100 subscribe-bg-color text-white p-2 fs-5">Subscribe</button>
                </form>
                <!-- End Subscribe form  -->
            <div class="row mt-2">
                <div class="col-md-12">
                </div>
            </div>
        </div>
    </div>
</div>