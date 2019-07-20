<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<script type="text/javascript" src="js/print_script.js"></script>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
</head>

<body>

<section id="body" class="width">
		
<!-- ----------------------------Including the sidebar ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar ?>

<section id="content" class="column-left" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>Checkup & Sessions</h3>
<?php 
session_start(); //Instructing php engine to reload session variables in $_SESSION[] array or create new
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations

function exit_script()
{
	//Each time we want the script to stop executing further, we will call this function so that the basic html code is output at the end
	
	?>
	</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer 
	?>
	</section>
	
	<div class="clear"></div>
	</section>
    </body>
	</html>

<?php
	exit;
}

//------checking the required fields---------//
if(empty($_REQUEST['cr_id']))
{
	 printf("%s","<blockquote><p>".NO_CHECKUP_RECORD_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_cr_id=intval($_REQUEST['cr_id']);

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful

$select_query='SELECT PD.`patient_id`, PD.`first_name`, PD.`last_name`, PD.`year_of_birth`, PD.`gender`, PD.`weight`, PD.`height`, PD.`city`, PD.`locality`, PD.`street_address`, PD.`occupation`, PD.`mobile_number`, PD.`email_id`, PD.`blood_group`,PD.`is_blacklist`,CR.`checkup_id`, CR.`date_of_checkup`, CR.`counselor_id`, CT.`name`, CR.`no_of_sessions`, CR.`valid_till_date`, CR.`remarks`,CR.`referred_by`,C.`designation`, C.`first_name`,C.`last_name`
FROM `checkup_record` AS CR, `patient_details` AS PD, `counselor` AS C, `checkup_type` AS CT
WHERE CR.patient_id=PD.patient_id AND CR.counselor_id=C.counselor_id AND CT.checkup_type_id=CR.checkup_type_id AND CR.checkup_id='.$passed_cr_id;

@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server

$patient_obj=new Patient();
$checkup_record_obj=new CheckupRecord();
$counselor_obj=new Counselor();


$statement->bind_result($patient_obj->patient_id,
	 $patient_obj->first_name,
	 $patient_obj->last_name,
	 $patient_obj->year_of_birth,
	 $patient_obj->gender,
	 $patient_obj->weight,
	 $patient_obj->height,
	 $patient_obj->city,
	 $patient_obj->locality,
	 $patient_obj->street_address,
	 $patient_obj->occupation,
	 $patient_obj->mobile_number,
	 $patient_obj->email_id,
	 $patient_obj->blood_group,
	 $patient_obj->is_blacklist,
						
	$checkup_record_obj->checkup_id,
	$checkup_record_obj->date_of_checkup,
	$checkup_record_obj->counselor_id,
	$checkup_record_obj->checkup_type,
	$checkup_record_obj->no_of_sessions,
	$checkup_record_obj->valid_till_date,
	$checkup_record_obj->remarks,
	$checkup_record_obj->referred_by,

	$counselor_obj->designation,
	$counselor_obj->first_name,
	$counselor_obj->last_name);

$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p></blockquote>");
	exit_script();
}

if(! $statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	//If the record was not fetched, show error
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}
//Following lines will execute only if there is some data is fetched from the database
	$_SESSION['patient_details_obj']=$patient_obj;
	$_SESSION['checkup_details_obj']=$checkup_record_obj;
	$_SESSION['counselor_details_obj']=$counselor_obj;
?>


<!-- -------------------Printing patient Details---------------- -->
<div id="DivIdToPrint">	
<a id="NotToPrint" style="float: right" href="update_patient_details.php?p_id=<?php printf("%d",$patient_obj->patient_id);?>" >EDIT</a>
<table>
	<tr>

		<TD><b>PATIENT_ID:</b><?php printf("%s","<td><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>$patient_obj->patient_id</a></td>");?></TD>
		
		<TD><b>NAME:</b><?php printf("%s","<td>".$patient_obj->first_name." ".$patient_obj->last_name."</td>");?></TD>
		<TD><b>AGE:</b><?php printf("%s","<td>".(intval(date('Y'))-intval($patient_obj->year_of_birth))."</td>");?></TD>
		<TD><b>GENDER:</b><?php printf("%s","<td>".$patient_obj->gender."</td>");?></TD>
		
		
	</tr>
	<tr>
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
	<tr id='NotToPrint'>
		<TD <?php if(strcasecmp($patient_obj->is_blacklist,"Y")==0){printf("%s", "style=\"color:".PATIENT_IS_NOT_BLACKLIST_COLOR.";background-color:".PATIENT_IS_BLACKLIST_COLOR."\"");} ?> > BLACKLISTED</TD>
			
	</tr>

</table>
<br>
<fieldset ><legend id="NotToPrint">Checkup Details</legend></fieldset>
<a id="NotToPrint" href="update_checkup_record.php?cr_id=<?php printf("%s",$checkup_record_obj->checkup_id);?>" style="float: right">EDIT</a>
<br  id='NotToPrint'>

<!-- -------------------Printing Checkup Details------------------- -->
<table>
	<tr>
		<TD id="NotToPrint"><b>ID:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->checkup_id."</td>");?></TD>
		<TD><b>CONSULTANT:</b><?php printf("%s","<td>".$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");?></TD>
		<TD  id="NotToPrint"><b>DATE:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->date_of_checkup."</td>");?></TD>
		<TD id="NotToPrint"><b>TYPE:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->checkup_type."</td>");?></TD>
		
	</tr>
	<tr>
		<TD id="NotToPrint"><b>SESSIONS LEFT:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->no_of_sessions."</td>");?></TD>
		<TD id="NotToPrint"><b>REFERRED BY:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->referred_by."</td>");?></TD>
		<TD id="NotToPrint"><b>REMARKS:</b><?php printf("%s","<td id='NotToPrint'>".$checkup_record_obj->remarks."</td>");?></TD>

	</tr>

</table>
<br>
</div>
<fieldset><legend>Sessions</legend></fieldset>
<br>

<?php

//-------------------------Fetching Checkup Sessions of the passed Checkup ID----------------------------------//

$select_query='SELECT `session_number`, `date`, `remarks` FROM `checkup_session` 
WHERE checkup_id='.$passed_cr_id.' ORDER BY `date` DESC' ;

@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server

$checkup_session_obj=new CheckupSession();
	
$statement->bind_result($checkup_session_obj->session_number,
					   $checkup_session_obj->date,
					   $checkup_session_obj->remarks);
	
$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".NO_CHECKUP_SESSION_ERROR."</blockquote>");
?>
	<br><br>
	<a class="button" onclick="window.open('print_checkup_form.php')">Print Checkup Form</a>
	&nbsp;
<?php
	printf("%s","<br><br><a href=\"make_new_checkup_session.php?cr_id=".$passed_cr_id."\" class=\"button\">Add new session for this checkup</a>");
	printf("%s","&nbsp;<a href=\"get_checkup_invoice.php?cr_id=".$passed_cr_id."\" class=\"button\">Invoice</a>");
	exit_script();
}
//Following lines will execute only if there was some data in the database
?>
<span style="font-size: 1.4em;font-weight: bold;">Total Sessions=<?php printf("%d",$checkup_record_obj->no_of_sessions+$statement->num_rows);?></span>
 &nbsp &nbsp
<?php 
	if($checkup_record_obj->no_of_sessions==1)
	{
		printf(" <span style='padding:0px 20px;background-color: #ff4f31;color: #FFFFFF;display: inline-block;font-weight: bold;font-size: 1.4em;'> Sessions Left=%d",$checkup_record_obj->no_of_sessions);
	}
	else
	{
		printf(" <span style='font-size: 1.4em;'> Sessions Left=%d",$checkup_record_obj->no_of_sessions);
	}
	
?>
	
	</span>
&nbsp &nbsp 
<span style="font-size: 1.4em;font-weight: bold;">Sessions Done=<?php printf("%d",$statement->num_rows);?></span> <br>
<table>
<tr>

<TH>SESSION_NUMBER</TH>
<TH>DATE OF SESSION</TH>
<TH>REMARKS</TH>
</tr>

<?php
	
while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	printf("%s","<tr>");
	 printf("%s","<td>".$checkup_session_obj->session_number."</td>");
	 printf("%s","<td>".$checkup_session_obj->date."</td>");
	 printf("%s","<td>".$checkup_session_obj->remarks."</td>");
	 printf("%s","<td><a href='update_checkup_session.php?cs_id=".$checkup_session_obj->session_number."'>EDIT</td>");
	printf("%s","</tr>");
	
}
?>

</table>

<br>
<a class="button" onclick="window.open('print_checkup_form.php')">Print Checkup Form</a>
&nbsp;
<?php
printf("%s","<a href=\"make_new_checkup_session.php?cr_id=".$passed_cr_id."\" class=\"button\">Add new session</a>");
?>
&nbsp;
<a href="get_checkup_invoice.php?cr_id=<?php printf("%d",$passed_cr_id); ?>" class="button">Invoice</a>


</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>