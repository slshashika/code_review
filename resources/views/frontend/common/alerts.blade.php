@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <p><strong>Something went wrong</strong></p>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
       
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        {{session('success')}}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        {{session('error')}}
    </div>
    
@endif

@if(session('warning'))
    <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        {{session('warning')}}
    </div>
   
@endif


