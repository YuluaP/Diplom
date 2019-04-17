<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WAY-24</title>
    <!-- includes Bootstrap -->
    <link rel="stylesheet" href="{{asset('library/bootstrap-4.0/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('library/bootstrap-4.0/css/bootstrap-grid.min.css')}}">
    <!-- includes fontAwesome -->
    <link rel="stylesheet" href="{{asset('library/font-awesome-4.7/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.css')}}">

    @yield('head')

</head>
<body>
<div class="main_wrapper">
    <!-- Container for header of admin panel -->
    <div class="head_container">
        <div class="col">
            <div class="logo_block">
                <h1>
                    <a href="{{url('/')}}">Way-24</a>
                </h1>
            </div>
            <div class="burger_block">
            </div>
        </div>

    </div>
    <!-- Container for menu -->
    <div id="menu_container" class="left_container">
        <div class="container">
            <div class="row">
                <div class="col">

                </div>
            </div>
            <div id="menu_block" class="row"> <!-- Left menu block -->
                <div class="col">
                    <div id="left_menu_box" class="left_menu_box">
                        <ul id="left_menu_content" class="menu_level_1">
                            <li id="seller_btn_sign" class="left_menu_item">
                                <a href="{{url('/places')}}" title="Места">
                                            <span class="btn_icon">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                            </span>
                                    <span class="btn_text">Места</span>
                                </a>
                            </li>
                            <li id="seller_btn_sign" class="left_menu_item">
                                <a href="{{url('/directions')}}" title="Маршруты">
                                            <span class="btn_icon">
                                                <i class="fa fa-truck" aria-hidden="true"></i>
                                            </span>
                                    <span class="btn_text">Маршруты</span>
                                </a>
                            </li>
                            <li id="buyer_btn_sign" class="left_menu_item">
                                <a href="#" title="О нас">
                                            <span class="btn_icon">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                            </span>
                                    <span class="btn_text">О нас</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container for main content -->
    <div class="main_container" id="main_container_back">
    
        <div class="container">

            <div class="row">
                <div class="col"> <!-- Block for main content -->

                    <div class="main_content">
                        @yield('content')

                    </div>
                </div>
            </div>
            <footer>
                <!-- There may be content for the footer -->
            </footer>
        </div>
    </div>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('library/bootstrap-4.0/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('library/bootstrap-4.0/js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/scripts.js')}}"></script>

@yield('script')

</body>
</html>
