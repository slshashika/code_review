@extends('frontend.app')
@section('content')


<div id="" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('/images/frontend/about-us/page-banner.png') }}" class="d-block w-100" alt="Page Banner">
        </div>
    </div>
</div>

<div class="container-fluid profile-section py-5">
    <div class="container font-red-hat">
        <div class="row">
            <div class="col-12 pb-2">
                <h2 class="haeding-h2 fw-bold">My Account</h2>


            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('frontend.common.alerts')
            </div>
        </div>
        <div class="row">
            <div class="d-lg-flex align-items-start shadow-lg px-2 py-4 bg-body rounded border">
                <div class="col-lg-2 col-12">

                    <div class="nav flex-column nav-pills me-3 profile-tab" id="v-pills-tab" role="tablist"
                        aria-orientation="vertical">
                        <button class="nav-link text-start fs-6 mb-2  fw-bolder user-profile-tabs" id="v-pills-dashboard-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-dashboard" type="button" role="tab"
                            aria-controls="v-pills-dashboard" onClick="setSelectedTab('v-pills-dashboard-tab')"
                            aria-selected="true">Dashboard</button>
                        <button class="nav-link active text-start fs-6 mb-2  fw-bolder user-profile-tabs" id="v-pills-order-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-order" type="button" role="tab"
                            aria-controls="v-pills-order" onClick="setSelectedTab('v-pills-order-tab')"
                            aria-selected="false">My Orders</button>
                        <button class="nav-link text-start fs-6 mb-2  fw-bolder user-profile-tabs" id="v-pills-address-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-address" type="button" role="tab"
                            aria-controls="v-pills-address" onClick="setSelectedTab('v-pills-address-tab')"
                            aria-selected="false">Addresses</button>
                        <button class="nav-link text-start fs-6 mb-2  fw-bolder user-profile-tabs" id="v-pills-account-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-account" type="button" role="tab"
                            aria-controls="v-pills-account" onClick="setSelectedTab('v-pills-account-tab')"
                            aria-selected="false">Account Details</button>
                        <button class="nav-link text-start fs-6 mb-2  fw-bolder user-profile-tabs" id="v-pills-wishlist-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-wishlist" type="button" role="tab"
                            aria-controls="v-pills-wishlist" onClick="setSelectedTab('v-pills-wishlist-tab')"
                            aria-selected="false">Wishlist</button>
                        <form action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <button class="nav-link text-start fs-6 mb-2  fw-bolder user-profile-tabs" id="v-pills-logout-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-logout" type="button" role="tab"
                            aria-controls="v-pills-logout" aria-selected="false"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</button>
                    </div>
                </div>
                <div class="col-lg-10 col-12">

                    <div class="tab-content pt-2" id="v-pills-tabContent">
                        <div class="tab-pane fade" id="v-pills-dashboard" role="tabpanel"
                            aria-labelledby="v-pills-dashboard-tab">
                            <p class="profile-p">
                                Hello <strong>{{$customer->first_name}}</strong></p>

                            <p class="profile-p">
                                From your account dashboard you can view your <strong>recent orders</strong>, manage
                                your <strong>shipping and billing addresses</strong>, and <strong>edit your password and
                                    account details</strong>.</p>

                        </div>
                        <div class="tab-pane fade show active" id="v-pills-order" role="tabpanel"
                            aria-labelledby="v-pills-order-tab">

                            @include('frontend.user.components.orders')

                        </div>
                        <div class="tab-pane fade" id="v-pills-address" role="tabpanel"
                            aria-labelledby="v-pills-address-tab">
                            @include('frontend.user.components.addresses')

                        </div>
                        <div class="tab-pane fade" id="v-pills-account" role="tabpanel"
                            aria-labelledby="v-pills-account-tab">

                            @include('frontend.user.components.account_details')


                        </div>
                        <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel"
                            aria-labelledby="v-pills-wishlist-tab">

                            @include('frontend.user.components.wishlist')


                        </div>
                        <div class="tab-pane fade" id="v-pills-logout" role="tabpanel"
                            aria-labelledby="v-pills-logout-tab"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


@section('scripts')

<script>
    
$(document).ready(function() {
     setActiveTab();
    removeCountryCode("customer-phone");
});

function setSelectedTab(tabId) {
    localStorage.setItem('tab', tabId);
}

function setActiveTab() {
    let tabId = localStorage.getItem('tab');

    if (tabId !== null) {
        document.getElementById(tabId).click();
    }
}


</script>

@endsection