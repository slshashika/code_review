<div class="dropdown ms-1">
    <button class="btn btn-link p-0 position-relative" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <picture>
            <img class="mini-cart-icon w-100" src="{{ asset('/images/frontend/home/user-icon.png') }}" alt="" srcset="">
        </picture>
    </button>
    <ul class="dropdown-menu dropdown-md dropdown-menu-end" aria-labelledby="profileDropdown">
        <li>
            <hr class="dropdown-divider">
        </li>
        <li class="d-flex py-2 align-items-start">
            <div class="flex-grow-1">
                <div>
                   <p class="lh-1 mb-2 fw-semibold text-body px-3">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p> 
                </div>
            </div>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item d-flex align-items-center" href="{{route('web.user.account')}}">My Account</a></li>
        <li><a class="dropdown-item d-flex align-items-center" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
    </ul>
</div> 
<!-- / Profile Menu-->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>