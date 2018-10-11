@extends('doc.main')

@section('body')
    @include('doc.page-style')
    <h2 class="text-left">Get Programs</h2>
    <hr style="border:1px solid #ccc"/>
    <p class="top">
        <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/programs
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
   "apiName": "getPrograms",
   "type": "LIVE",
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
                <td>type</td>
                <td>attribute 'type' could be one of (LIVE,VOD,CATCHUP) options, value should be in capital letter</td>
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
            "apiName": "getPrograms",
            "response": {
                "type":"LIVE",
                "count": 2,
                "programs": [
                    {
                        "id": 3,
                        "program_name": "Free Program",
                        "description": "PHA+PHN0cm9uZz5zZGZzZjwvc3Ryb25nPjwvcD4=",
                        "logo_url": "public/uploads/program/3/logo/160716111706.jpeg",
                        "poster_url_mobile": "public/uploads/program/3/poster-mobile/160614135638.jpeg",
                        "poster_url_stb": "public/uploads/program/3/poster-stb/160614135639.jpeg",
                        "water_mark_url": "public/uploads/program/3/water-mark/160614135650.jpeg",
                        "lcn": 0,
                        "type": "FREE",
                        "individual_price": "0"
                    },
                    {
                        "id": 23,
                        "program_name": "Test",
                        "description": null,
                        "logo_url": "public/uploads/program/23/logo/160614140842.jpeg",
                        "poster_url_mobile": "public/uploads/program/23/poster-mobile/160614140842.jpeg",
                        "poster_url_stb": "public/uploads/program/23/poster-stb/160614140842.jpeg",
                        "water_mark_url": "public/uploads/program/23/water-mark/160614141214.jpeg",
                        "lcn": 0,
                        "type": "LIVE",
                        "individual_price": "400"
                    }

                ]
            },
            "errorCode": "",
            "errorMsg": "",
            "debugCode": "",
            "debugMsg":""
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
                        <li><strong>type</strong> - Identify type of programs</li>
                        <li><strong>count</strong> - number of program exist into the collection <em>'programs'</em></li>
                        <li><strong>programs</strong> - it will contain collection of program as object</li>
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