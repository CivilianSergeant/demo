@include('doc.page-style')
<h2 class="left-align">Get Subscriber Profile</h2>
<p class="top">
    This API will be used to get subscriber.

</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <span><strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/subscriber-profile</span>
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
        "apiName": "getSubscriberProfile",
        "appId":"NexViewersentTV",
        "appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
        "customerId":13,
        "password":"d22b45af79e9341cc4851e10b393b187"
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
            <td>attribute 'customerId' represents subscribers unique Id</td>
        </tr>
        <tr>
            <td>password</td>
            <td>attribute 'password' represents subscribers credentials value which is a md5 hasing value</td>
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
        "apiName": "apiLogin",
        "response": {
            "code": 200,
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "customer": {
                "customer_id": "18",
                "username": "+8801714112912",
                "profile_id": "23",
                "email": "info@nexdecade.com",
                "lat": "23.938383",
                "lon": "90.093833",
                "telco_id": "1",
                "service_operator_type": "TELCO",
                "registration_type": "PHONE",
                "get_profile": {
                    "id": 23,
                    "subscriber_name": "Demo Name",
                    "address1": "address",
                    "address2": null,
                    "country_id": null,
                    "division_id": null,
                    "district_id": null,
                    "area_id": null,
                    "post_code": null,
                    "sub_area_id": null,
                    "road_id": null,
                    "contact": "+8801710190181",
                    "is_same_as_contact": "0",
                    "billing_contact": null,
                    "photo": null,
                    "identity_type": null,
                    "identity_number": null,
                    "identity_attachment": null,
                    "subscription_copy": null
                }
            },
            "balance":0

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
                    <li><strong>customer</strong> - It represents subscriber's a object content with subscriber's basic info </li>
                    <li><strong>customer.customer_id</strong> - It represents subscriber's a unique id. should persist at client app </li>
                    <li><strong>customer.username</strong> - It represents subscriber's username</li>
                    <li><strong>customer.profile_id</strong> - It represents subscriber's profile id , it is associate with customer.get_profile.id</li>
                    <li><strong>customer.email</strong> - It represents subscriber's email</li>
                    <li><strong>customer.lat</strong> - It represents Subscriber's Phone latitude</li>
                    <li><strong>customer.lon</strong> - It represents Subscriber's Phone longitude</li>
                    <li><strong>customer.telco_id</strong> - It is service operator id of subscriber which is associate with subscriber</li>
                    <li><strong>customer.service_operator_type</strong> - It represents Service Operator Type, possible values are [TELCO | ISP] </li>
                    <li><strong>customer.registration_type</strong> - It represents Registration Type, possible values are [PHONE | EMAIL | BOTH], by this value developer should restrict on some field is editable or not to  subscriber profile page of app  </li>
                    <li><strong>customer.get_profile</strong> -  This attribute contains subscriber's details information, attribute under get_profile object is self explanatory</li>
                    <li><strong>customerName</strong> -  It represents name of subscriber/customer</li>
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
