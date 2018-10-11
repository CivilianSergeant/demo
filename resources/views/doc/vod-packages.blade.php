@extends('doc.main')

@section('body')
    @include('doc.page-style')
    <h2 class="text-left">Get VoD Packages</h2>
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
  "type":"VOD",
  "isCommercial": "true"
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
            "apiName": "getPackages",
            "response": {
                "type": "VOD",
                "count": 1,
                "packages": [
                    {
                        "id": 4,
                        "package_name": "VOD Package",
                        "package_type": "VOD",
                        "duration": 1,
                        "price": 100,
                        "package_mobile_logo": "Package Logo (Mobile)",
                        "package_stb_logo": "Package Logo (STB)",
                        "package_poster_stb": "Package Poster (STB)",
                        "package_poster_mobile": "Package Poster (Mobile)",
                        "programs": 1
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