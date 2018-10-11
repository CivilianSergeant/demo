
@include('doc.page-style')
<h2 class="text-left">Get Package Details</h2>
<p class="top">
    This API will fetch details information about a packages.
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/package-details
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
"apiName": "getPackageDetails",
"appId":"NexViewersentTV",
"appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
"packageId":1,
"deviceId":1
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
            <td>packageId</td>
            <td>attribute 'packageId' represents id of a package.</td>
        </tr>
        <tr>
            <td>deviceType</td>
            <td>attribute 'deviceType' represents Requested Device Type , possible values are Android=1, IOS=2, WEB=3, STB=4</td>
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
            "code": 200,
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "package": {
                "id": 1,
                "package_name": "Free Channels",
                "package_type": "LIVE",
                "duration": "30",
                "price": "0",
                "package_mobile_logo": null,
                "package_stb_logo": null,
                "package_poster_stb": null,
                "package_poster_mobile": null,
                "programs": [
                    {
                        "id": 1,
                        "program_name": "BTV World"
                    },
                    {
                        "id": 2,
                        "program_name": "প্রোগ্রামের নাম"
                    }
                ]
            }
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

                    <li><strong>count</strong> - number of program exist into the collection <em>'programs'</em></li>
                    <li><strong>notification</strong> - possible value true or false</li>
                    <li><strong>notificationType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>ads</strong> - possible value true or false</li>
                    <li><strong>adsType</strong> - if notification false, by default its value should be 0</li>
                    <li><strong>package</strong> - it will contain detail information about package including program list</li>
                    <li><strong>packages[index].id</strong> - it represents unique id for a package</li>
                    <li><strong>packages[index].package_name</strong> - it represents package name</li>
                    <li><strong>packages[index].package_type</strong> - it represents package type, possible values are [LIVE | VOD | CATCHUP]</li>
                    <li><strong>packages[index].duration</strong> - it represents duration of package</li>
                    <li><strong>packages[index].price</strong> - it represents package price</li>
                    <li><strong>packages[index].package_mobile_logo</strong> - it represents logo url for mobile</li>
                    <li><strong>packages[index].package_poster_mobile</strong> - it represents poster url for mobile</li>
                    <li><strong>packages[index].package_stb_logo</strong> - it represents logo url for stb/web</li>
                    <li><strong>packages[index].package_poster_stb</strong> - it represents poster url for stb/web</li>
                    <li><strong>package.programs</strong> - it will contain collection available program in a package</li>
                    <li><strong>package.programs[index].id</strong> - it will contain unique id of program in a package</li>
                    <li><strong>package.programs[index].program_name</strong> - it will contain name of a program in a package</li>
                    <li><strong>package.programs[index].individual_price</strong> - it will contain price of a program in a package</li>
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
