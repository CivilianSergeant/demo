@extends('doc.main')

@section('body')
    @include('doc.page-style')
    <h2 class="left-align">Register Subscriber</h2>
        <hr style="border:1px solid #ccc"/>
        <p class="top">
            <span><strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/registration-subscriber</span>
        </p>
        <p class="top">
            <span ><strong>Request Type:</strong> POST</span>
        </p>
        <div class="headding">
            <p><strong>Request Example Json:</strong></p>
        </div>
        <pre class="json"></pre>

        <script type="text/javascript">
            var data = {
                "product": "PLAAS OTT API",
                "version": 1.01,
                "apiName": "registrationSubscriber",
                "name"   : "Sample Name",
                "email"  : "sample@email.com",
                "phoneNo": "+88017xxxxxxxx",
                "lat"    : "23.933383",
                "lon"    : "90.903930",
                "deviceType":1,
                "parentId":1
            };


            document.getElementsByClassName("json")[0].innerHTML = JSON.stringify(data, undefined, 3);
        </script>

        <div class="">
            <p><strong>Request Parameter Description:</strong></p>
            <table class="table">
                <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>product</td>
                    <td>attribute 'product' represents API Name , default value "PLAAS OTT API"</td>
                </tr>
                <tr>
                    <td>version</td>
                    <td>attribute 'version' represents API Version, default value "1.01"</td>
                </tr>
                <tr>
                    <td>apiName</td>
                    <td>attribute 'apiName' represents function that will call for requested url</td>
                </tr>
                <tr>
                    <td>name</td>
                    <td>attribute 'name' represents Subscriber Name</td>
                </tr>
                <tr>
                    <td>email</td>
                    <td>attribute 'email' represents Subscriber E-mail, it is Optional Attribute</td>
                </tr>
                <tr>
                    <td>phoneNo</td>
                    <td>attribute 'phoneNo' represents Subscriber Phone Number</td>
                </tr>
                <tr>
                    <td>lat</td>
                    <td>attribute 'lat' represents Subscriber's Phone latitude</td>
                </tr>
                <tr>
                    <td>lon</td>
                    <td>attribute 'lon' represents Subscriber's Phone longitude</td>
                </tr>
                <tr>
                    <td>deviceType</td>
                    <td>attribute 'deviceType' represents Requested Device Type , possible values are Android=1, IOS=2, WEB=3, STB=4</td>
                </tr>
                <tr>
                    <td>parentId</td>
                    <td>attribute 'parentId' represents Subscriber Parent user id</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="headding">
            <p><strong>Response Example Json:</strong></p>
        </div>
        <pre class="json"></pre>

        <script type="text/javascript">
            var data = {
                "status": 0, // success
                "product": "PLAAS OTT API",
                "version": 1.01,
                "apiName": "registerSubscriber",
                "response": {
                    'code':200,
                    'message': 'Check SMS/Email for confirmation Code',
                    'message_type' : 'NONE'
                },
                "errorCode": "",
                "errorMsg": "",
                "debugCode":"",
                "debugMsg":""
            };


            document.getElementsByClassName("json")[1].innerHTML = JSON.stringify(data, undefined, 3);
        </script>


        <div class="">
            <p ><strong>Response Parameter Description:</strong></p>
            <table class="table">
                <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>status</td>
                    <td>attribute 'status' represents api execution status, it will be either 0 on success or 1 for faliure</td>
                </tr>
                <tr>
                    <td>product</td>
                    <td>attribute 'product' represents API Name , default value "PLAAS OTT API"</td>
                </tr>
                <tr>
                    <td>version</td>
                    <td>attribute 'version' represents API Version, default value "1.01"</td>
                </tr>
                <tr>
                    <td>apiName</td>
                    <td>attribute 'apiName' represents function that will called for requested url</td>
                </tr>
                <tr>
                    <td style="vertical-align:top">response</td>
                    <td>attribute 'response' will hold actual response for the request url, it also contain
                        following parameters:
                        <br/>
                        <ul style='list-style:none;padding:0;'>
                            <li><strong>code</strong> - on success it will set 200</li>
                            <li><strong>message</strong> - It will sent a message to notify that system will sent a code for verification'</li>
                            <li><strong>message_type</strong> - It represents what kind of message system will sent <br/>to subscriber, Possible values are [NOTIFICATION | TOAST | DIALOG | VIEW | NONE]</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>errorCode</td>
                    <td>if <em>'status'</em> set with value 1 then it will set with error code for particular error that causes faliure of exection</td>
                </tr>
                <tr>
                    <td>errorMsg</td>
                    <td>if <em>'status'</em> set with value 1 then this attribute will contain error message</td>
                </tr>
                <tr>
                    <td>debugCode</td>
                    <td><em>(optional)</em> For Debug Purpose</td>
                </tr>
                <tr>
                    <td>debugMsg</td>
                    <td><em>(optional)</em> For Debug Purpose</td>
                </tr>

                </tbody>
            </table>
        </div>
@stop