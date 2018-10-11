<!Doctype html>
<html>
    <head>
        <title>Documentation</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300italic,900italic,900,700italic,700,500italic,500,400italic,300,100italic,100' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style class="text/css">

            header{background: #546e7a; text-align: center; color:#fff;}
            body{
                font-family: "Roboto", sans-serif;
                background: #F5F8FA;
                font-weight: 100;
                color:#212121;
            }
            .sidebar{background: #F5F8FA; padding:0px; height: 100%;}
            .main-content{background: #FFFFFF;}
            ul{
                height: 100%;

            }

            ul,li{

                padding: 0;
                margin: 0;

            }

            li:first-child a{

            }
            li a{
                display: block;
                line-height: 20px;
                padding:5px 2px 5px 2px;
                text-decoration: none;
                color:#546e7a;
                font-size:12px;
                text-indent: 15px;
            }


            li a:hover:not(.active) {

                background-color: #555;
                color: white;

            }
            .logo{height:60px; padding-top:8px; margin-left:18px;}
            .logo a{color:#FFFFFF;text-decoration: none;}
            .top-menu{
                margin:20px;
            }
            .top-menu a{
                color:#fff;
                font-weight:500;


            }
        </style>
    </head>

    <body>
        <header>
            <div class="logo pull-left">
                <a href="{{url('/')}}"><img src="{{ asset('img/logo.png') }}" style="height: 44px;"> <strong class="logo-title">IPTV / OTT INTEGRATION  MANUAL</strong></a>
            </div>
            <span class="top-menu pull-right"><a href="{{url('logout')}}">Logout</a></span>
            <div style="clear:both;"></div>
        </header>
        <div class="content">
        <div vlas="row">

                <div class="col-md-10 col-md-offset-1 main-content">
                    @yield('body')
                </div>

        </div>
        </div>

    </body>
</html>