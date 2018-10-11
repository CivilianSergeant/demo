@include('doc.page-style')
<div class="title">
    <strong>Custom Error Code Description</strong>
</div>
<br/>
<table class="table api">
    <thead>
    <tr>
        <th><strong>Code</strong></th>
        <th><strong>Description</strong></th>
        <th><strong>Type</strong></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>100</td>
        <td>Any kind of Debug Message</td>
        <td>Debug</td>
    </tr>
    <tr>
        <td>101</td>
        <td>Name Property is required</td>
        <td></td>
    </tr>
    <tr>
        <td>102</td>
        <td>Phone Number Property is required</td>
        <td></td>
    </tr>
    <tr>
    <tr>
        <td>103</td>
        <td>Phone Number is Invalid</td>
        <td></td>
    </tr>
    <tr>
        <td>104</td>
        <td>E-mail is Invalid</td>
        <td></td>
    </tr>
    <tr>
        <td>105</td>
        <td>Phone No and Email is doesn't match</td>
        <td></td>
    </tr>
    <tr>
        <td>106</td>
        <td>Code is Required</td>
        <td></td>
    </tr>
    <tr>
        <td>107</td>
        <td>Confirmation Code Expired</td>
        <td></td>
    </tr>
    <tr>
        <td>108</td>
        <td>Invalid Confirm Code</td>
        <td></td>
    </tr>
    <tr>
        <td>109</td>
        <td>No Account found</td>
        <td></td>
    </tr>
    <tr>
        <td>110</td>
        <td>Email not available</td>
        <td></td>
    </tr>
    <tr>
        <td>115</td>
        <td>Reset Password is Required</td>
        <td></td>
    </tr>
    <tr>
        <td>116</td>
        <td>Reset confirm password is Required</td>
        <td></td>
    </tr>
    <tr>
        <td>117</td>
        <td>Password and Confirm Password is not matched</td>
        <td></td>
    </tr>
    <tr>
        <td>118</td>
        <td>Content not found</td>
        <td></td>
    </tr>
    <tr>
        <td>119</td>
        <td>Not Enough Balance</td>
        <td></td>
    </tr>
    <tr>
        <td>120</td>
        <td>Password length should not less than 8 characters</td>
        <td></td>
    </tr>
    <tr>
        <td>121</td>
        <td>Sorry! Old Password not matched</td>
        <td></td>
    </tr>
    </tbody>
</table>

<div class="title">
    <strong>General Error JSON</strong>
</div>


<pre class="json"></pre>

<script type="text/javascript">
    var data = {
        "status": 1,
        //"request_uri": "/route-name",
        "api_name": "api name",
        "response": null,
        "errorCode": 101,
        "errorMsg": "Error message",
        "debugCode": 100,
        "debugMsg": "Debug Message"
    };

    document.getElementsByClassName("json")[0].innerHTML = JSON.stringify(data, undefined, 3);
</script>