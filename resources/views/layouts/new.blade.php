<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="csrf_token" content="{{ csrf_token() }}" />

    @yield('styles', '')
    <link rel="stylesheet" href="/css/app.css">
    {{--<link href="/vendor/ubold/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>--}}
    <link rel="stylesheet" href="/backend/css/bootstrap.min.css">
    <link href="/vendor/ubold/assets/css/core.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/css/components.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    @yield('styles_pages', '<link href="/vendor/ubold/assets/css/pages.css" rel="stylesheet" type="text/css"/>')

    <link href="/vendor/ubold/assets/css/menu.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/css/admin/sweetalert2.min.css" rel="stylesheet" type="text/css"/>

    @yield('inline_styles', '')
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="/vendor/ubold/assets/js/modernizr.min.js"></script>

</head>

<body>

<!-- Navigation Bar-->
@include('common.header')
<div class="wrapper">
    <div class="container py32">

    @yield('content')
    <!-- Footer -->
    <!-- End Footer -->
    </div> <!-- end container -->
</div>

<!-- jQuery  -->
<script src="/vendor/ubold/assets/js/jquery.min.js"></script>
<script src="/vendor/ubold/assets/js/bootstrap.min.js"></script>
<script src="/vendor/ubold/assets/js/detect.js"></script>
<script src="/vendor/ubold/assets/js/fastclick.js"></script>
<script src="/vendor/ubold/assets/js/jquery.slimscroll.js"></script>
<script src="/vendor/ubold/assets/js/jquery.blockUI.js"></script>
<script src="/vendor/ubold/assets/js/waves.js"></script>
<script src="/vendor/ubold/assets/js/wow.min.js"></script>
<script src="/vendor/ubold/assets/js/jquery.nicescroll.js"></script>
<script src="/vendor/ubold/assets/js/jquery.scrollTo.min.js"></script>
<script src="/js/admin/sweetalert2.min.js"></script>

@yield('scripts', '')

<!-- App core js -->
<script src="/vendor/ubold/assets/js/jquery.core.js"></script>
<script src="/vendor/ubold/assets/js/jquery.app.js"></script>
<script type="text/javascript">
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=csrf_token]').attr('content') }
});

</script>
@yield('inline_scripts', '')

@include('flash-message::sweetalert')

</body>
</html>
