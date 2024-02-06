<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
    <title>Server Error</title>

    <style>

/*======================
    404 page
=======================*/


.page_404{ padding:40px 0; background:#fff; font-family: 'Arvo', serif;
}

.page_404  img{ width:100%;}

.four_zero_four_bg{
 
 background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
    height: 400px;
    background-position: center;
 }
 
 
 .four_zero_four_bg h1{
 font-size:80px;
 }
 
  .four_zero_four_bg h3{
			 font-size:80px;
			 }
			 
			 .link_404{			 
	color: #fff!important;
    padding: 10px 20px;
    background: #39ac31;
    margin: 20px 0;
    display: inline-block;}
	.contant_box_404{ margin-top:-50px;}

</style>

    <!-- Styles -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    <!-- Google Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>


<section class="page_404">
	<div class="container">
		<div class="row">	
		<div class="col-sm-12 ">
		<div class="col-sm-12 text-center">
		<div class="four_zero_four_bg">
			<h1 class="text-center ">500</h1>
		
		
		</div>
		
		<div class="contant_box_404">
		<h3 class="h2">
		Something went wrong
		</h3>
		
		<h5>Error - {{$error}}</h5>
		
		<a href="{{url('/')}}" class="link_404">Go to Home</a>
	</div>
		</div>
		</div>
		</div>
	</div>
</section>

</body>
</html>


