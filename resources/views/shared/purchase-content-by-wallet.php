<h2 class="left-align">Purchase Content By Wallet</h2>
<p class="top">
    This API will be used for purchase content using wallet payment method.
    
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <span><strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/purchase-content-by-wallet</span>
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
        "apiName": "purchaseContentByWallet",
        "appId":"NexViewersentTV",
        "appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
        "customerId":28,
        "password":"81dc9bdb52d04dc20036dbd8313ed055",
        "contentId":2,
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
            <td>attribute 'customerId' represents an identity for user</td>
        </tr>
        <tr>
            <td>password</td>
            <td>attribute 'password' represents user's password in hashing format</td>
        </tr>
        <tr>
            <td>contentId</td>
            <td>attribute 'contentId' represents id of content that will be purchase</td>
        </tr>
        <tr>
            <td>paymentMethod</td>
            <td>attribute 'paymentMethod' represents payment method of purchase process, possible values are [WALLET|VISACARD|MASTERCARD|MTCHARGE]</td>
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
        "apiName": "purchaseContentByWallet",
        "response": {
           "code":200,
           "notification": true,
           "notificationType": 1,
           "ads": true,
           "adsType": 1,
           "balance": "260",
           "transaction": {
                "id": 307,
                "contentId": 17,
                "price": "50",
                "programStartDate": "2017-02-14 15:38:54",
                "programExpireDate": "2017-02-14 17:39:00",
                "duration": "02:00:06",
                "transactionDate": "2017-02-14 15:38:54"
            },
           "message":"Content was sucssfully subscribed",
           "messageType":"TOAST"
        },
        "errorCode": 0,
        "errorMsg": "",
        "debugCode": 0,
        "debugMsg": ""
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
                    <li><strong>balance</strong> - It represents subscriber's available balance</li>
                    <li>
                        <strong>transaction</strong> - transaction object which will be sent just after purchase process on success or null on failure
                        <br/>
                        <ol>
                            <li><strong><em>transaction.id</em></strong> - unique id of transaction</li>
                            <li><strong><em>transaction.contentId</em></strong> - purchased package id</li>
                            <li><strong><em>transaction.price</em></strong> - price debit from user account</li>
                            <li><strong><em>transaction.programStartDate</em></strong> - package subscription start date</li>
                            <li><strong><em>transaction.programExpireDate</em></strong> -  package subscription expire date </li>
                            <li><strong><em>transaction.duration</em></strong> -  total duration of subscription </li>
                            <li><strong><em>transaction.transactionDate</em></strong> -  date of transaction </li>
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
