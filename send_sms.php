<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
</head>

<body>

<section id="body" class="width">
		
<!-- ----------------------------Including the sidebar ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar
	
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sms_constants.php'); //This file contains data class definations
	
?>

<section id="content" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>Send SMS</h3>


<?php
	// Authorisation details.
	$username = USER_NAME;
	$hash = HASH;

	// Config variables. Consult http://api.textlocal.in/docs for more info.
	$test = TEST;

	// Data for text message. This is the text message data.
	$sender = SENDER; // This is who the message appears to be from.
	
	$numbers = implode(",",$_REQUEST['contact']); // A single number or a comma-seperated list of numbers
	$message = $_REQUEST['message'];
	// 612 chars or less
	// A single number or a comma-seperated list of numbers
	$message = urlencode($message);
	$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
	$ch = curl_init('http://api.textlocal.in/send/?');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); // This is the result from the API
	if($result)
	{
		$result=json_decode($result);
		echo "<h5>No. of SMS Sent Successfully:"+$result->num_messages."</h5><br>";
		echo "<h5>SMS Remaining:"+$result->status."</h5><br>";
		
		if(strcasecmp($result->status,'success')==0)
		{
			echo "<center><h4>SMS Sent Successfully</h4></center>";
			echo "<h4>Sent To:</h4>";
			foreach($result->messages as $message)
			{
				echo "<h6>".$message->recipient."</h6>";
			}
		}
		else
		{
			echo "<center><h4>Failed to send SMS</h4></center>";
		}
	}
	
	curl_close($ch);
?>


</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>