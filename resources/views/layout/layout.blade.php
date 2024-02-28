@include('include.header')
<body class="">
    <div id="preloader">
        <div id="status"></div>
    </div>
    @include('include.nabar_header')

    <div class="page-container row-fluid ">
        @include('include.page_sidebar')
        <div class="page-content">
            <div class="clearfix"></div>
            <div class="content sm-gutter">
                <!--@include('include.breadcrumb')-->
                @yield('content')
            </div>
        </div>
    </div>
    @include('include.footer')
</body>

</html>
