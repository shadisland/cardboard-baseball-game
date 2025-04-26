<?php
 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Baseball Card Display Cases</title>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var url = "https://prod-customer-sgc-api.azurewebsites.net/v1/pop-report/getPopReportByCardSet";
 $(document).ready(function () {
	$(".send-button").click(function(e) {
		$.ajax({
			type: "POST",
			url: "https://proxy.cors.sh/https://prod-customer-sgc-api.azurewebsites.net/v1/pop-report/getPopReportByCardSet",
			data: JSON.stringify({ 
			    cardSet: "1968 Topps",	
			    sport: "Baseball"
			}),
			headers: {
			      'Accept': 'application/json',
			      'Content-Type': 'application/json',
			      'x-cors-api-key': 'temp_9675417240c62af38c570fa8138fc319'
			      
    			},
			success: function(response) {
			    //alert('ok');
			    console.log(response);
			   $('#status-msg-text').text(response);

			   
			},
			error: function(response) {
			    alert('error');
			}
		    });
	});
  });

</script>
</head>

<body>
<button class="send-button" >Send</button>
<div id="status-msg-text">
output:
</div>

"authorization": "bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Ik1qVTVNMFkxT1RsRU5UUTBSVUU0TjBReFJqTTNNRE0zUlRGRU16Y3dPRFEwUkRRNE5qaEZNUSJ9.eyJpc3MiOiJodHRwczovL2xvZ2luLmdvc2djLmNvbS8iLCJzdWIiOiJhdXRoMHw2NTM4OTczYjEyYWM2MWVkY2I2ODdlM2EiLCJhdWQiOlsiaHR0cHM6Ly9hcGkuc2djY2FyZC5jb20iLCJodHRwczovL3NnY2NhcmQuYXV0aDAuY29tL3VzZXJpbmZvIl0sImlhdCI6MTcyMTk0MDU3NSwiZXhwIjoxNzIxOTkwOTc1LCJzY29wZSI6Im9wZW5pZCBwcm9maWxlIGVtYWlsIiwiYXpwIjoibnZub2ZuQW5wOWVpNWNBQ05nODZyR2cwSGNqOE5BM2MifQ.W4V6ce5LDscIg7LrNbPRUVV70YAua1Pr6J0N-j-T0F5Yi7LrcJP9Np4KUxkUMe5sQ6WKu2q56KNAkN3Ybvqx2xrq70Ts1ICv-wrkw_EIAjIYY5eQ5mcLtPTj4jfSC2hLUU6mRxDYkASvWnRxeojs502WwVg3znSaShnjCu2oBcHR3RSlZ9o98K7DBQ_vfDAH64cPosqTnf2hDD87zTYsizy7e4TYH8zonZNKt0zgjq42YdiO4jxDUFcf8tOX3X2PlAtVbeB0vvPficEsJzSmCNJeDFwIex60brEM11Tgut9p6I1NbdaUzvGBTVclOmdhn0QbbqCOtrAu8rfPvyE8Kw"

</body>
</html>