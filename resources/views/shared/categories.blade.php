
@include('doc.page-style')
<h2 class="text-left">Get Categories</h2>
<p class="top">
    Get Contents API will fetch all categories of given type.
</p>
<hr style="border:1px solid #ccc"/>
<p class="top">
    <strong>Request URL:</strong> http:// [ ServerIp]: [serverPort]/categories
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
"apiName": "getCategories",
"appId":"NexViewersentTV",
"appSecurityCode":"eee80f834a6e15b47db06fb70e75bada"
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
        {{--<tr>
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
        </tr>--}}

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
        "apiName": "getCategories",
        "response": {
            "code":200,
            "notification": true,
            "notificationType": 1,
            "ads": true,
            "adsType": 1,
            "categories":
            {
                "channels": [
                    {
                        "id": 1,
                        "category_name": "International",
                        "sub_categories": [
                            {
                                "id": 2,
                                "category_id": "1",
                                "sub_category_name": "News"
                            },
                            {
                                "id": 3,
                                "category_id": "1",
                                "sub_category_name": "Sports"
                            },
                            {
                                "id": 7,
                                "category_id": "1",
                                "sub_category_name": "Life Style"
                            }
                        ]
                    },

                    {
                        "id": 5,
                        "category_name": "Sports",
                        "sub_categories": null
                    }


                ],
                "vod": null,
                "catchup": null
            }

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
                    {{--<li><strong>count</strong> - number of category exist into the collection <em>'categories'</em></li>
                    <li><strong>totalCount</strong> - number of category exist into database</li>--}}
                    <li><strong>categories</strong> - it will contain collection of category  as object of different types, possible types are [channels | catchup | vod] within category object</li>
                    <li><strong>categories[type][index].id</strong> - it represnts unique id of a category</li>
                    <li><strong>categories[type][index].category_name</strong> - it represents category name</li>
                    <li><strong>categories[type][index].sub_categories</strong> - it represents a collection of associate subcategory with the category</li>
                    <li><strong>categories[type][index].sub_categories[index].id</strong> - sub category id</li>
                    <li><strong>categories[type][index].sub_categories[index].category_id</strong> - which is associated with categories[index].id</li>
                    <li><strong>categories[type][index].sub_categories[index].sub_category_name</strong> - It represnts sub category name</li>
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
