@include('doc.page-style')
<h2 class="left-align">Get Epgs</h2>
<p class="top">
    This API will be used for fetch epgs for a program

</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <span><strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/epgs</span>
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
        "apiName": "getEpgs",
        "appId":"NexViewersentTV",
        "appSecurityCode":"eee80f834a6e15b47db06fb70e75bada",
        "contentId":1,
        "timezone":"+06:00"
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
            <td>contentId</td>
            <td>attribute 'contentId' represents id of a channel or program</td>
        </tr>
        <tr>
            <td>timezone</td>
            <td>attribute 'timezone' represents timezone of subscriber's device</td>
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
        "apiName": "getEpgs",
        "response": {
            "code": 200,
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "epgs": [
                {
                    "id": "1",
                    "program_id": "1",
                    "program_name": "News at 10PM",
                    "program_description": "PHA+VGhpcyBpcyBhIGRlbGFjaW91cyBGb29kIFByb2dyYW08L3A+",
                    "program_logo":"logo_path",
                    "duration": "30",
                    "start_time": "2016-10-24 00:00:00",
                    "end_time": "2016-10-24 00:30:00",
                    "repeat_times": [
                        "2016-10-27 22:00:00",
                        "2016-10-28 23:00:00"
                    ]
                }
            ]
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
                    <li><strong>epgs</strong> - collection of epg</li>
                    <li><strong>epgs[index].id</strong> - unique id for epg</li>
                    <li><strong>epgs[index].program_id</strong> - associated with program id</li>
                    <li><strong>epgs[index].program_name</strong> - program name</li>
                    <li><strong>epgs[index].program_description</strong> - program description is a html type text , the value given by response need to decode by base64 to get actual content</li>
                    <li><strong>epgs[index].program_logo</strong> - this attribute contains url for program logo</li>
                    <li><strong>epgs[index].duration</strong> - how long program will show</li>
                    <li><strong>epgs[index].start_time</strong> - program start time</li>
                    <li><strong>epgs[index].end_time</strong> - program end time</li>
                    <li><strong>epgs[index].repeat_times</strong> - program repeat date times</li>
                    <li><strong>epgs[index].expandable</strong> - it returns true or false</li>
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
