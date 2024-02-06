@if($bannerImg != null)
<div class="container-fluid  p-0 ">
    <img class="home-banner-img" src="{{ asset($bannerImg->src ?? 'images/default.png') }}" alt="" srcset="">
</div>
@endif
