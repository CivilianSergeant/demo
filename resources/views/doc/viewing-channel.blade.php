@extends('doc.main')

@section('body')
    @include('doc.page-style')
    <h2 class="text-left">Get Viewing Channel</h2>
    <hr style="border:1px solid #ccc"/>
    <p class="top">
        <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/viewing-channel
    </p>
    <p class="top">
        <span ><strong>Request Type:</strong> POST</span>
    </p>
    <div class="headding">
        <p><strong>Request Example Json:</strong></p>
    </div>
    <pre class="json">
{
   "product": "PLAAS OTT API",
   "version": 1.01,
   "apiName": "viewingChannel",
   "contentId":1,
   "type":"CHANNEL",
   "customerId": 22,
   "lat":23.938383,
   "lon":90.093833
}
</pre>


    <div class="">
        <p><strong>Response Parameter Description:</strong></p>
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
                <td>contentId</td>
                <td>attribute 'contentId' represents id of a channel or program</td>
            </tr>
            <tr>
                <td>type</td>
                <td>attribute 'type' represents viewing content type, possible values are [CHANNEL | VOD | CATCHUP] </td>
            </tr>
            <tr>
                <td>customerId</td>
                <td>attribute 'customerId' is subscriber's unique id that is sent after successful logged-in</td>
            </tr>
            <tr>
                <td>lat</td>
                <td>attribute 'lat' represents Subscriber's Phone latitude</td>
            </tr>
            <tr>
                <td>lon</td>
                <td>attribute 'lon' represents Subscriber's Phone longitude</td>
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
            "status": 0,
            "product": "PLAAS OTT API",
            "version": "1.01",
            "apiName": "viewingChannel",
            "response": {
                "code": 200,
                "message":"Response OK",
                "messageType":"NONE"
            },
            "errorCode": "",
            "errorMsg": "",
            "debugCode": "",
            "debugMsg": ""
        };

        document.getElementsByClassName("json")[1].innerHTML = JSON.stringify(data, undefined, 3);
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
                        <li><strong>message</strong> - It will sent a message to notify that subscriber registration confirmed'</li>
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