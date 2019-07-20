<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations
session_start(); //Instructing php engine to reload session variables in $_SESSION[] array after including all class definations
$patient_obj=$_SESSION['patient_details_obj'];
$checkup_record_obj=$_SESSION['checkup_details_obj'];
$counselor_obj=$_SESSION['counselor_details_obj'];
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Print Checkup Form</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<style>
	#NotToPrint, #NotToPrint *{display:none;}
	@media print {#print_button,#print_button *{visibility: hidden;}
	td {font-size: 20px; color:#111}
	table {padding: 10px}
</style>
</head>

<body style="background:#fff">
	<center>					
	<img style="height:auto;width:1000" src="images/print_header.JPG"/>
	<br>
<!----------- Printing Patient ID and today's Date in a single line with help of table ----------->
	<table>
	<tr>	
	
	<td style='font-size: 20px;text-align:left; color:#111'>
		<b>REG. ID:</b>&nbsp;<?php printf("%s",$patient_obj->patient_id);?>
	</td>
	<td style="font-size: 20px;text-align: right; color:#111">
		<b>Date :</b>
<?php 
			$date=getdate();
			
			printf("%s",$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']);
?>
	</td>
	</tr>
	</table>
	
<div id="DivIdToPrint">	
<a id="NotToPrint" style="float: right" href="update_patient_details.php?p_id=<?php printf("%d",$patient_obj->patient_id);?>" >EDIT</a>
<table >
	<tr>

		<td><b>PATIENT NAME:</b> &nbsp;<?php printf("%s",$patient_obj->first_name." ".$patient_obj->last_name."</td>");?>
		<td><b>AGE/G:</b> &nbsp;&nbsp;&nbsp;<?php printf("%s",(intval(date('Y'))-intval($patient_obj->year_of_birth))." / ".substr($patient_obj->gender,0,1)."</td>");?>
		
		<TD><b>WEIGHT:</b><?php printf("%s","<td>".$patient_obj->weight." Kg</td>");?></TD>
		<TD><b>HEIGHT:</b><?php printf("%s","<td>".$patient_obj->height." cm</td>");?></TD>
		<TD id="NotToPrint"><b>CITY:</b><?php printf("%s","<td id='NotToPrint'>".$patient_obj->city."</td>");?></TD>
		<TD id="NotToPrint"><b>ADDRESS:</b><?php printf("%s","<td id='NotToPrint'>".$patient_obj->locality." ".$patient_obj->street_address."</td>");?></TD>
		
		
	</tr>
	<tr>
		<TD id="NotToPrint"><b>OCCUPATION:</b><?php printf("%s","<td id='NotToPrint'>".$patient_obj->occupation."</td>");?></TD>
		<TD id="NotToPrint"><b>MOBILE:</b><?php printf("%s","<td id='NotToPrint'>".$patient_obj->mobile_number."</td>");?></TD>
		<TD id="NotToPrint"><b>EMAIL:</b><?php printf("%s","<td id='NotToPrint'>".$patient_obj->email_id."</td>");?></TD>
		<TD id="NotToPrint"><b>BLOOD GROUP:</b><?php printf("%s","<td id='NotToPrint'>".$patient_obj->blood_group."</td>");?></TD>
	</tr>

</table>
<fieldset id="NotToPrint" ><legend id="NotToPrint">Checkup Details</legend></fieldset>
<a id="NotToPrint" href="update_checkup_record.php?cr_id=<?php printf("%s",$checkup_record_obj->checkup_id);?>" style="float: right">EDIT</a>
<br  id='NotToPrint'>

<!-- -------------------Printing Checkup Details------------------- -->
<table style="padding-right: 30px">
	<tr>
		<td style="text-align: left">
			
		<b>CONSULTANT:</b> &nbsp;&nbsp;&nbsp;<?php printf("%s",$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");?>
		
		<td style="text-align: right;margin-right: 20px">
		<b>REFERRED BY:</b> &nbsp;&nbsp;&nbsp;<?php printf("%s",$checkup_record_obj->referred_by."</td>");?>
	</tr>
</table>

<table id="NotToPrint">
	<tr>
		<TD id="NotToPrint"><b>ID:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->checkup_id."</td>");?></TD>
		<TD colspan="1"><b>CONSULTANT:</b></TD><?php printf("%s","<td>".$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");?>
		<TD  id="NotToPrint"><b>DATE:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->date_of_checkup."</td>");?></TD>
		<TD id="NotToPrint"><b>TYPE:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->checkup_type."</td>");?></TD>
		
	</tr>
	<tr>
		<TD id="NotToPrint"><b>SESSIONS LEFT:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->no_of_sessions."</td>");?></TD>
		<TD id="NotToPrint"><b>REMARKS:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->remarks."</td>");?></TD>

	</tr>

</table>
<br>
</div>

<img style="height:auto;width:1000" src="images/print_tests.JPG"/>
<br>
<img style="height:auto;width:auto;opacity: 0.25;" src="images/page_bg.png"/>
<br>
<div style="position:absolute; bottom:0;">
	<img style="height:auto;width:1000" src="images/print_footer.JPG"/>
	<br><a class="button" id="print_button" onclick="window.print()">Print</a>
</div>
</center>
</body>
</html>