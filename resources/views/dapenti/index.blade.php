<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Title of the page -->
    <title>@yield('title')</title>
    <!-- Mobi.css -->
    <link rel="stylesheet" href="https://unpkg.com/mobi.css/dist/mobi.min.css">
  </head>
  <body>
    <!-- Your content here -->
	<div class="flex-center">
	  <div class="container-wider">
	  	@yield('content')
	  </div>
	</div>
  </body>
</html>
