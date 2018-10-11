@include('doc.page-style')
<h2 class="left-align">Content Order ID</h2>
<p class="top">
    This API will be used to generate order id using customer id, password and content id
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <span><strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/content-order-id</span>
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
        "apiName": "getContentOrderId",
        "appId":"NexViewersentTV",
        "appSecurityCode":"80f834a6e15b4ebc7db06fb70adae75b",
        "customerId":13,
        "password":"d22b45af79e9341cc4851e10b393b187",
        "contentId":1,
        "paymentMethod":"WALLET"
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
            <td>appId</td>
            <td>attribute 'appId' represents app id to send api request</td>
        </tr>
        <tr>
            <td>appSecurityCode</td>
            <td>attribute 'appSecurityCode' represents app security code to send api request</td>
        </tr>
        <tr>
            <td>customerId</td>
            <td>attribute 'customerId' represents user's unique id</td>
        </tr>
        <tr>
            <td>password</td>
            <td>attribute 'password' represents user's password</td>
        </tr>
        <tr>
            <td>contentId</td>
            <td>attribute 'contentId' represents id of content that will be purchase</td>
        </tr>
        <tr>
            <td>paymentMethod</td>
            <td>attribute 'paymentMethod' represents payment method choose for purchase process</td>
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
        "apiName": "getContentOrderId",
        "response": {
            "code": 200,
            "notification"     => true,
            "notificationType" => 1,
            "ads"              => true,
            "adsType"          => 1,
            "balance"          => 0, 
            "transactionId"    => "TRS-2817031617105020",
            "chargedAmount"    => "50",
            "success_url"      => null,
            "cancel_url"       => null,
            "fail_url"         => null  
        },
        "errorCode": 0,
        "errorMsg": '',
        "debugCode":0,
        "debugMsg":''
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
                    <li><strong>notification</strong> - possible value true or false</li>
                    <li><strong>notificationType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>ads</strong> - possible value true or false</li>
                    <li><strong>adsType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>balance</strong> - It will be customer available balance</li>
                    <li><strong>transactionId</strong> - transactionId represents order id for requested item</li>
                    <li><strong>chargedAmount</strong> - chargeAmount represents item amount that will be debited from user balance</li>
                    <li><strong>success_url</strong> - success url that will use by payment gateway</li>
                    <li><strong>cancel_url</strong> - cancel url that will use by payment gateway</li>
                    <li><strong>fail_url</strong> - fail url that will use by payment gateway</li>
                    
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
