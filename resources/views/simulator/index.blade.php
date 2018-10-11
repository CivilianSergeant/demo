@extends('welcome')


@section('content')
    @include('doc.page-style')
    <div class="container">
        <h1 style="padding-top:10px;">Viewers Entertainment</h1>
        <br/>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3><a id="homeTab" href="{{url('/')}}"> Home</a><span class="seperator"> | </span><a id="simulatorTab" href="#"> API Request Simulator</a><span class="seperator"> | </span><a id="errorPageTab" href="#">Custom ErrorCode</a> <a class="pull-right" href="{{url('logout')}}">Logout</a></h3>
            </div>
            <div class="panel-body">
                <div id="homeContent">

                    @include('shared.home')


                </div>
                <div id="simulatorContent">
                    <form class="form-horizontal" id="simulateUrl">
                        <div class="form-group">
                            <label class="col-md-3">REQUESTED URL</label>
                            {{--<div class="col-md-9">
                                <input type="text" name="req_url" class="form-control"/>
                            </div>--}}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3">{{url('/').'/'}}</span>
                                    <select id="req_url" class="form-control" name="req_url">
                                        <option value="">SELECT API</option>
                                        @if(!empty($routes))
                                            @foreach($routes as $route => $name)
                                                <option value="{{$route}}">{{$name}} [{{$route}}]</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3">JSON STRING</label>
                            <div class="col-md-9">

                            <textarea name="req_json" class="form-control" style="
        margin: 0px -282px 0px 0px;
        height: 210px;
        overflow-y: scroll;
        resize:vertical;"></textarea>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-3">
                                <button type="submit" class="btn btn-success">SUBMIT</button>
                            </div>
                        </div>
                    </form>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Response</h3>
                        </div>
                        <div class="panel-body">
                            <div id="loading" class="text-center"><strong>Loading</strong></div>
                            <p id="noreseponse" style="text-align: center;"> No Response Yet</p>
                            <pre id="result" class="hidden"></pre>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3><a class="btn btn-info btn-xs view-expand-btn" id="showHideDoc">+</a> Documentation</h3>
                        </div>
                        <div id="documentation" class="panel-body hide-doc">

                        </div>
                    </div>
                </div>
                <div id="errorPageContent">
                    @include('shared.general-error-page')
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript">
        $("#loading").hide();

        $("#homeTab").css({color:'green'});
        $("#simulatorContent").hide();
        $("#errorPageContent").hide();

        $("#homeTab").click(function(){
            var obj = $(this);
            obj.css({color:'green'});
            $("#simulatorTab").css({color:'#337ab7'});
            $("#errorPageTab").css({color:'#337ab7'});
            $("#homeContent").show();
            $("#simulatorContent").hide();
            $("#errorPageContent").hide();
            return false;
        });

        $("#errorPageTab").click(function(){
            var obj = $(this);
            obj.css({color:'green'});
            $("#simulatorTab").css({color:'#337ab7'});
            $("#homeTab").css({color:'#337ab7'});
            $("#errorPageContent").show();
            $("#simulatorContent").hide();
            $("#homeContent").hide();
            return false;
        });

        $("#simulatorTab").click(function(){
            var obj = $(this);
            obj.css({color:'green'});
            $("#errorPageTab").css({color:'#337ab7'});
            $("#homeTab").css({color:'#337ab7'});
            $("#simulatorContent").show();
            $("#errorPageContent").hide();
            $("#homeContent").hide();
            return false;
        });

        $("#showHideDoc").click(function(){
            var obj = $(this);
            if($("#documentation").hasClass('hide-doc')){
                obj.html('-');
            }else{
                obj.html('+');
            }
            $("#documentation").toggleClass('hide-doc');
        });

        $("#req_url").change(function(){
            var obj = $(this);
            $.ajax({
                type:"POST",
                url:SITE_URL+'simulator/get-json-request',
                data:{route:obj.val()},
                beforeSend:function(){
                    $("textarea[name=req_json]").val('');
                    $("#documentation").html('');
                    $("#result").html('');
                },
                success:function(e){

                    $("textarea[name=req_json]").val(e);
                }
            });

            $("#documentation").load(SITE_URL+'simulator/get-documentation-page',{route:obj.val()});
        });

        $("#simulateUrl").submit(function(){
            var obj = $(this);
            $.ajax({
                type:"POST",
                url:SITE_URL+'simulate',
                data:obj.serialize(),
                beforeSend:function(){
                    $("#loading").show();
                    $("#result").hide();
                },
                success:function(e){
                    $("#loading").hide();
                    document.getElementById('result').innerHTML = JSON.stringify(JSON.parse(e), undefined, 3);
                    $("#noreseponse").hide();
                    $("#result").removeClass('hidden').show();
                }
            });

            return false;
        });
    </script>
@stop