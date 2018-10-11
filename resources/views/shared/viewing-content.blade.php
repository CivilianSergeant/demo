
@include('doc.page-style')
<h2 class="text-left">Get Viewing Content</h2>
<p class="top">
    Get Viewing Content API should use to track which content is viewing by a subscriber, as response of this API request will get
    db version status.
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/viewing-content
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
"apiName": "viewingContent",
"appId":"NexViewersentTV",
"appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
"contentId":1,
"categoryId":4,
"subCategoryId":8,
"type":"LIVE",
"customerId": 22,
"password":"d22b45af79e9341cc4851e10b393b187",
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
            <td>appId</td>
            <td>attribute 'appId' represents app id to send api request</td>
        </tr>
        <tr>
            <td>appSecurityCode</td>
            <td>attribute 'appSecurityCode' represents app security code to send api request</td>
        </tr>
        <tr>
            <td>contentId</td>
            <td>attribute 'contentId' represents id of a channel or program</td>
        </tr>
        <tr>
            <td>categoryId</td>
            <td>attribute 'categoryId' represents category id of program</td>
        </tr>
        <tr>
            <td>subCategoryId</td>
            <td>attribute 'subCategoryId' represents sub category id of program</td>
        </tr>
        <tr>
            <td>type</td>
            <td>attribute 'type' represents viewing content type, possible values are [LIVE | VOD | CATCHUP] </td>
        </tr>
        <tr>
            <td>customerId</td>
            <td>attribute 'customerId' is subscriber's unique id that is sent after successful logged-in</td>
        </tr>
        <tr>
            <td>password</td>
            <td>attribute 'password' represents user's password in hashing format</td>
        </tr>
        <tr>
            <td>lat</td>
            <td>attribute 'lat' represents latitude of Subscriber's Phone</td>
        </tr>
        <tr>
            <td>lon</td>
            <td>attribute 'lon' represents longitude of Subscriber's Phone</td>
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
        "apiName": "viewingContent",
        "response": {
            "code": 200,
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "balance":0,
            "dbVersion": {
                "channel_db_version": 0,
                "vod_db_version": 0,
                "notification_db_version": 0,
                "catchup_db_version": 0,
                "package_db_version": 0,
                "category_db_version":0
            },
            "breadcrumbs":{
                "category_name": "Bangla",
                "sub_category_name": "Cinema"
            },
            "systemTime":"20161015163315",
            "message":"Response OK",
            "messageType":"NONE"
        },
        "errorCode": 0,
        "errorMsg": '',
        "debugCode": 0,
        "debugMsg": ''
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
                    <li><strong>notification</strong> - possible value true or false</li>
                    <li><strong>notificationType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>ads</strong> - possible value true or false</li>
                    <li><strong>adsType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>balance</strong> - It represents subscriber's available balance</li>
                    <li>
                        <strong>dbVersion</strong> - dbVersion contains a JSONObject with following attribute
                        <br/>
                        <ol>
                            <li><strong><em>channel_db_version</em></strong> - will have incremented value for any event(insert , update, delete) occur at back end with Channel Content</li>
                            <li><strong><em>vod_db_version</em></strong> - will have incremented value for any event(insert , update, delete) occur at back end with VoD Content</li>
                            <li><strong><em>notification_db_version</em></strong> - will have incremented value for any event(insert , update) occur at back end with Client Notification</li>
                            <li><strong><em>catchup_db_version</em></strong> - will have incremented value for any event(insert , update, delete) occur at back end with Catchup Content</li>
                            <li><strong><em>package_db_version</em></strong> -  will have incremented value for any event(insert , update, delete) occur at back end with Package </li>
                            <li><strong><em>category_db_version</em></strong> -  will have incremented value for any event(insert , update, delete) occur at back end with Category </li>
                        </ol>
                    </li>
                    <li>
                        <strong>breadcrumbs</strong> - breadcrumb used to identify category/sub-category of content in detail view
                        <br/>
                        <ol>
                            <li><strong><em>category_name</em></strong> - In which content is assigned</li>
                            <li><strong><em>sub_category_name</em></strong> - In which content is assigned</li>
                        </ol>
                    </li>
                    <li><strong>systemTime</strong> - It represents system time, it will be used for validate subscription</li>
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
