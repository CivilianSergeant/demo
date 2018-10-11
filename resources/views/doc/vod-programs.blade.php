@extends('doc.main')

@section('body')
    @include('doc.page-style')
    <h2 class="text-left">Get VoD Programs</h2>
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
   "type": "VOD"
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
                "count": 2,
                "programs": [
                    {
                        "id": 7,
                        "program_name": "VOD Program",
                        "description": "VOD Program",
                        "logo_url": "Logo URL",
                        "poster_url_mobile": "Poster URL (Mobile)",
                        "poster_url_stb": "Poster URL (STB)",
                        "water_mark_url": "",
                        "lcn": 0,
                        "type": "VOD",
                        "individual_price": "4000"
                    },
                    {
                        "id": 9,
                        "program_name": "Vod 2",
                        "description": "Vod 2",
                        "logo_url": "",
                        "poster_url_mobile": "",
                        "poster_url_stb": "",
                        "water_mark_url": "",
                        "lcn": 0,
                        "type": "VOD",
                        "individual_price": "400"
                    }

                ]
            },
            "errorCode": "",
            "errorMsg": "",
            "displayMsg": ""
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
                        <li><strong>programs</strong> - it will contain collection of program as object</li>
                        <li><strong>count</strong> - number of program exist into the collection <em>'programs'</em></li>
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
                <td>displayMsg</td>
                <td><em>(optional)</em></td>
            </tr>
            </tbody>
        </table>
    </div>
@stop