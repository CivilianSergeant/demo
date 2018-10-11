@extends('doc.main')

@section('body')
    @include('doc.page-style')
    {{--<table  cellspacing="0" cellpadding="0" class="table version" style="width: 50%; background: unset;">
        <tr>
            <td width="217" valign="top"><strong>Document    Title: </strong></td>
            <td width="340" valign="top">PLAAS TECH SMART IPTV / OTT API</td>
        </tr>
        <tr>
            <td width="217" valign="top"><strong>Version: </strong></td>
            <td width="340" valign="top">1.01</td>
        </tr>
        <tr>
            <td width="217" valign="top"><strong>Date: </strong></td>
            <td width="340" valign="top"><strong>2016-06-01</strong></td>
        </tr>
        <tr>
            <td width="217" height="21" valign="top"><strong>Status: </strong></td>
            <td width="340" valign="top"><strong>Release </strong></td>
        </tr>
    </table>

    <div class="title">
        <strong>General  Notes </strong>
    </div>
    <div class="content">
        <p>
            PLAAS TECH offers this information as a service to  its customers, to support application and engineering efforts that use the  products designed by PLAAS TECH. The information provided is based upon  requirements specifically provided to PLAAS TECH by the customers. PLAAS TECH  has not undertaken any independent search for additional relevant information,  including any information that may be in the customer&rsquo;s possession.  Furthermore, system validation of this product designed by PLAAS TECH within a  larger electronic system remains the responsibility of the customer or the  customer&rsquo;s system integrator. All specifications supplied herein are subject to  change. </p>
    </div>--}}
    {{--<div class="title"><strong>API Index</strong></div>
    <br/>
    <table class="table api">
        <thead>
        <tr>
            <th><strong>SL#</strong></th>
            <th><strong>API</strong></th>
            <th><strong>Description</strong></th>
            <th><strong>Statuts</strong></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td><a  href="{{url('doc/register')}}">Register Subscriber</a></td>
            <td></td>
            <td>Done</td>
        </tr>
        <tr>
            <td>1</td>
            <td><a  href="{{url('doc/confirm')}}">Confirm Registration</a></td>
            <td></td>
            <td>Done</td>
        </tr>
        <tr>
            <td>1</td>
            <td><a  href="{{url('doc/live-delay-programs')}}">Get Live/Delay Progarms</a></td>
            <td></td>
            <td>Done</td>
        </tr>

        </tbody>
    </table>--}}
    <h1>Welcome To API Documentation</h1>


    <div class="title">
        <strong>Custom Error Code Description</strong>
    </div>
    <br/>
    <table class="table api">
        <thead>
        <tr>
            <th><strong>Code</strong></th>
            <th><strong>Description</strong></th>
            <th><strong>Type</strong></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>100</td>
            <td>Any kind of Debug Message</td>
            <td>Debug</td>
        </tr>
        <tr>
            <td>101</td>
            <td>Name Property is required</td>
            <td></td>
        </tr>
        <tr>
            <td>102</td>
            <td>Phone Number Property is required</td>
            <td></td>
        </tr>
        <tr>
        <tr>
            <td>103</td>
            <td>Phone Number is Invalid</td>
            <td></td>
        </tr>
        <tr>
            <td>104</td>
            <td>E-mail is Invalid</td>
            <td></td>
        </tr>
        <tr>
            <td>105</td>
            <td>Phone No and Email is doesn't match</td>
            <td></td>
        </tr>
        <tr>
            <td>106</td>
            <td>Code is Required</td>
            <td></td>
        </tr>
        <tr>
            <td>107</td>
            <td>Confirmation Code Expired</td>
            <td></td>
        </tr>
        <tr>
            <td>108</td>
            <td>Invalid Code</td>
            <td></td>
        </tr>
        <tr>
            <td>109</td>
            <td>No Account found</td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <div class="title">
        <strong>General Error JSON</strong>
    </div>


    <pre class="json"></pre>

    <script type="text/javascript">
        var data = {
            "status": 1,
            //"request_uri": "/route-name",
            "api_name": "api name",
            "errorCode": 101,
            "errorMsg": "Error message",
            "debugCode": 100,
            "debugMsg": "Debug Message"
        };


        document.getElementsByClassName("json")[0].innerHTML = JSON.stringify(data, undefined, 3);
    </script>


    @include('shared.copyright')
@stop