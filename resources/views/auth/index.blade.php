@extends('welcome')


@section('content')

    <div class="container">
        <br/>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>Login to API Documentation</h3>
            </div>
            <div class="panel-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        <span>{{session('error')}}</span>
                    </div>
                @endif
                <form class="form-horizontal" action="{{url('authenticate')}}" method="post">
                    <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                    <div class="form-group">
                        <label class="control-label col-md-4">Username</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="username"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Password</label>
                        <div class="col-md-3">
                            <input type="password" class="form-control" name="password"/>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-4">
                        <button type="submit" class="btn btn-success">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop