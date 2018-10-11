@include('doc.page-style')
<h2 class="text-left">Get Favorite Contents</h2>
<p class="top">
    Get Favorite Contents API will fetch all favorite contents for a user.
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/get-favorites
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
"apiName": "getFavoriteContents",
"appId":"NexViewersentTV",
"appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
"customerId":9,
"password":"d22b45af79e9341cc4851e10b393b187",
"parentId":1,
"limit":5,
"offset":0
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
            <td>customerId</td>
            <td>attribute 'customerId' represents subscribers unique Id</td>
        </tr>
        <tr>
            <td>password</td>
            <td>attribute 'password' represents user's password in hashing format</td>
        </tr>
        <tr>
            <td>parentId</td>
            <td>attribute 'parentId' represents Subscriber Parent user id</td>
        </tr>
        <tr>
            <td>limit</td>
            <td>attribute 'limit' represents number of items will show at a time</td>
        </tr>
        <tr>
            <td>offset</td>
            <td>attribute 'offset' represents number of items will skip</td>
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
        "apiName": "getContents",
        "response": {
            "code": 200,
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "balance": "0",
            "count": 1,
            "totalCount":1,
            "channels": [
                {
                    "id": "1",
                    "program_name": "BTV World",
                    "video_share_url": "Test",
                    "video_trailer_url": "",
                    "description": "PHA+Jm5ic3A7PC9wPg==",
                    "water_mark_url": "",
                    "type": "LIVE",
                    "view_count": "8",
                    "lcn": "101",
                    "individual_price": "0",
                    "video_tags": "btv",
                    "duration": "0",
                    "age_restriction": "0",
                    "service_operator_id": "",
                    "logo_mobile_url": "https://cms.viewersent.com.bd/public/uploads/program/1/logo/161022123156.png",
                    "poster_url_mobile": "https://cms.viewersent.com.bd/pages-of-google-plus-115x115.png",
                    "hlsLinks": [
                        {
                            "hls_url_mobile": "default-expire-hls-mobilelink"
                        }
                    ],
                    "subscription": true,
                    "expireTime": "2016-11-19 23:59:59"
                }
            ],
            "systemTime":"20161025171400"
        },
        "errorCode": 0,
        "errorMsg": '',
        "debugCode": 0,
        "debugMsg":''
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
                    <li><strong>count</strong> - number of channel exist into the collection <em>'channels'</em></li>
                    <li><strong>totalCount</strong> - number of channel exist into database</li>
                    <li><strong>channels</strong> - it will contain collection of channel as object. channel data(LIVE only) should be store in app storage</li>
                    <li><strong>channels[index].id</strong> - it will contain content id</li>
                    <li><strong>channels[index].program_name</strong> - it will contain content/program name</li>
                    <li><strong>channels[index].video_share_url</strong> - it will contain url for sharing content</li>
                    <li><strong>channels[index].video_trailer_url</strong> - it will contain url for video trailer</li>
                    <li><strong>channels[index].description</strong> - it will contain content's description which is base64 encoded, to get original text developer should decode value of this attr</li>
                    <li><strong>channels[index].water_mark_url</strong> - it will contain water mark image url</li>
                    <li><strong>channels[index].type</strong> - it will represents content type</li>
                    <li><strong>channesl[index].view_count</strong> - it will represents number views</li>
                    <li><strong>channels[index].lcn</strong> - it will contain local channel number</li>
                    <li><strong>channels[index].individual_price</strong> - it will contain individual price for a content/program</li>
                    <li><strong>channels[index].age_restriction</strong> - it will contain 0 or 1 for age restriction option for a content/program</li>
                    <li><strong>channels[index].video_tags</strong> - it will help to find out relative content/program</li>
                    <li><strong>channels[index].duration</strong> - it represents duration of a content, especially for VoD and CATCHUP type content</li>
                    <li><strong>channels[index].service_operator_id</strong> - it will contain service operator id. you can check is this content show able to any operator base on these id</li>
                    <li><strong>channels[index].logo_mobile_url</strong> - it will contain content's logo url for mobile version</li>
                    <li><strong>channels[index].poster_url_mobile</strong> - it will contain content's poster url for mobile version</li>
                    <li><strong>channels[index].logo_url</strong> - it will contain content's logo url for web/stb version</li>
                    <li><strong>channels[index].poster_url_stb</strong> - it will contain content's poster url for stb version</li>
                    <li><strong>channels[index].hlsLinks</strong> - which contains all possible hls link (device specific)</li>
                    <li><strong>channels[index].subscription</strong> - possible value for this attribute is either true or false whether customer subscribed or not respectively</li>
                    <li><strong>channels[index].expireTime</strong> - this attribute can be exist(if subscription true) or not based on subscription value</li>
                    <li><strong>systemTime</strong> - It represents system time, it will be used for validate subscription</li>
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
