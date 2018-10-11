@include('doc.page-style')
<h2 class="left-align">Add Amount By Scratch Card</h2>
<p class="top">
    This API will be used to add amount to subscriber wallet using scratch card
    
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <span><strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/add-amount-by-scratch-card</span>
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
        "apiName": "addAmountByScratchCard",
        "appId":"NexViewersentTV",
        "appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
        "customerId":28,
        "password":"81dc9bdb52d04dc20036dbd8313ed055",
        "paymentMethod":"SCRATCHCARD",
        "serialNo":"00000010",
        "cardNo":"2311446697274319"
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
            <td>attribute 'customerId' represents an identity for user</td>
        </tr>
        <tr>
            <td>password</td>
            <td>attribute 'password' represents user's password in hashing format</td>
        </tr>
        <tr>
            <td>paymentMethod</td>
            <td>attribute 'paymentMethod' represents payment method of purchase process, possible values are [SCRATCHCARD|MASTERCARD|VISACARD|MTCHARGE|BKASH]</td>
        </tr>
        <tr>
            <td>serialNo</td>
            <td>attribute 'serialNo' represents serial number of scratch card</td>
        </tr>
        <tr>
            <td>cardNo</td>
            <td>attribute 'cardNo' represents card number of scratch card.</td>
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
        "apiName": "addAmountByScratchCard",
        "response": {
            "code":200,
            "notification":true,
            "notificationType":1,
            "ads":true,
            "adsType":1,
            "balance":260,
            "transaction": {
              "pairing_id": 0,
              "subscriber_id": 28,
              "lco_id": "1",
              "package_id": 2,
              "item_type": "PACKAGE",
              "payment_method_id": 8,
              "transaction_types": "D",
              "credit": 0,
              "debit": "10",
              "balance": 260,
              "user_package_assign_type_id": 1,
              "transaction_date": "2017-01-18 08:50:27",
              "parent_id": "1",
              "updated_at": "2017-01-18 08:50:27",
              "created_at": "2017-01-18 08:50:27",
              "id": 95
           },
           "message":"Amount added Successfully ",
           "messageType":"TOAST"
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
                    <li><strong>customerId</strong> - It represents subscriber's unique id.  store this value as persistent data to app storage </li>
                    <li><strong>authorize</strong> - It means subscriber authorized/unauthorized, possible values are respectively true or false</li>
                    <li><strong>password</strong> - It represents subscriber's password. store this value as persistent data to app storage</li>
                    <li><strong>sessionToken</strong> - It represents subscriber's session token, it will change by time to time or next each login, this will be used for stream protection</li>
                    <li><strong>customerName</strong> -  It represents name of subscriber/customer</li>
                    <li><strong>systemTime</strong> - It represents system time, it will be used for validate subscription</li>
                    <li><strong>balance</strong> - It represents subscriber's available balance</li>
                    <li>
                        <strong>transaction</strong> - transaction object which will be sent just after purchase process on success or null on failure
                        <br/>
                        <ol>
                            <li><strong><em>transaction.subscriber_id</em></strong> - user or subscriber id of transaction</li>
                            <li><strong><em>transaction.credit</em></strong> - credit amount of transaction</li>
                            <li><strong><em>transaction.debit</em></strong> - debit amount of transaction</li>
                            <li><strong><em>transaction.balance</em></strong> - balance of transaction</li>
                            <li><strong><em>transaction.transaction_date</em></strong> -  date of transaction </li>
                            
                        </ol>
                    </li>
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
