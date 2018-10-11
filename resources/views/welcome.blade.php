<!DOCTYPE html>
<html>
    <head>
        <title>Simulator</title>
        <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" >

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <script   src="{{asset('js/jquery-1.10.2.min.js')}}"   ></script>
        <script src="{{asset('js/bootstrap.min.js')}}" ></script>
        <script type="text/javascript">
            var SITE_URL = "<?php echo url('/').'/';?>";
        </script>
        <style type="text/css">
            .no-corner{border-radius:0px; margin-bottom:0px;}
        </style>
        {{--https://code.jquery.com/jquery-2.2.4.min.js integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"
        https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js integrity="sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD" crossorigin="anonymous"
        https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"

        --}}
    </head>
    <body>
        <div class="container">
            <div class="content">
                @yield('content')
            </div>
        </div>
        <script>
            var sessionExist = 0;
            var intervalId = setInterval(function(){
                $.ajax({
                   type:'POST',
                   url : SITE_URL+'check-status',
                   success:function(e){
                        if(e==0 && sessionExist == 1){
                            window.location.reload();
                            sessionExist = e;
                        }
                        if(e == 1)
                            sessionExist = e;
                   }
                });
            },5000);

        </script>
    </body>
</html>
