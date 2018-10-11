
@include('doc.page-style')
<h2 class="text-left">Get Packages</h2>
<p class="top">
    Get Contents API will fetch all packages of given type.
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/packages
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
"apiName": "getPackages",
"appId":"NexViewersentTV",
"appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
"type":"LIVE",
"deviceType":1,
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
            <td>type</td>
            <td>attribute 'type' could be one of (LIVE | VOD | CATCHUP | NULL) options, value should be in capital letter</td>
        </tr>
        <tr>
            <td>deviceType</td>
            <td>attribute 'deviceType' represents Requested Device Type , possible values are Android=1, IOS=2, WEB=3, STB=4</td>
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
        "apiName": "getPackages",
        "response": {
            "type": "LIVE",
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "count": 1,
            "totalCount":1,
            "packages": [
                {
                    "id": 1,
                    "package_name": "Free Package",
                    "package_type": "FREE",
                    "duration": 10,
                    "price": 0,
                    "programs": 1,
                    "package_mobile_logo": "",
                    "package_stb_logo": "",
                    "package_poster_stb": "",
                    "package_poster_mobile": ""
                }
            ]
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
                    <li><strong>type</strong> - Identify type of packages</li>
                    <li><strong>notification</strong> - possible value true or false</li>
                    <li><strong>notificationType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>ads</strong> - possible value true or false</li>
                    <li><strong>adsType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>count</strong> - number of package exist into the collection <em>'packages'</em></li>
                    <li><strong>totalCount</strong> - number of package exist into database</li>
                    <li><strong>packages</strong> - it will contain collection of package as object</li>
                    <li><strong>packages[index].id</strong> - it represents unique id for a package</li>
                    <li><strong>packages[index].package_name</strong> - it represents package name</li>
                    <li><strong>packages[index].package_type</strong> - it represents package type, possible values are [LIVE | VOD | CATCHUP]</li>
                    <li><strong>packages[index].duration</strong> - it represents duration of package</li>
                    <li><strong>packages[index].price</strong> - it represents package price</li>
                    <li><strong>packages[index].programs</strong> - it represents how many content or program included</li>
                    <li><strong>packages[index].package_mobile_logo</strong> - it represents logo url for mobile</li>
                    <li><strong>packages[index].package_poster_mobile</strong> - it represents poster url for mobile</li>
                    <li><strong>packages[index].package_stb_logo</strong> - it represents logo url for stb/web</li>
                    <li><strong>packages[index].package_poster_stb</strong> - it represents poster url for stb/web</li>
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
