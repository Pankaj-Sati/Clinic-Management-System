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

<h3>Patient Details</h3>
<?php 

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
if(empty($_REQUEST['p_id']))
{
	 printf("%s","<blockquote><p>".NO_PATIENT_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_patient_id=intval($_REQUEST['p_id']);

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful

$select_query="SELECT `".PATIENT_ID."`,`".FIRST_NAME."`,`".LAST_NAME."`, `".YEAR_OF_BIRTH."`, `".GENDER."`, `".WEIGHT."`, `".HEIGHT."`, `".CITY."`, `".LOCALITY."`, `".STREET_ADDRESS."`, `".OCCUPATION."`, `".MOBILE_NUMBER."`, `".EMAIL_ID."`, `".BLOOD_GROUP."`,`".IS_BLACKLIST."` FROM `".PATIENT_DETAILS_TABLE."` WHERE ".PATIENT_ID."=".$passed_patient_id;
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
	 $patient_obj->is_blacklist
);

$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p><blockquote>");
	exit_script();
}

if(! $statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	//If the record was not fetched, show error
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}
//Following lines will execute only if there is some data is fetched from the database
?>
<div id="DivIdToPrint">
<a id="NotToPrint" style="float: right" href="update_patient_details.php?p_id=<?php printf("%d",$patient_obj->patient_id);?>" >EDIT</a>

<table>
<!-- -------------------Printing patient Details------------------- -->
	<tr>

		
		<TD><b>PATIENT_ID:</b><?php printf("%s","<td>".$patient_obj->patient_id."</td>");?></TD>
		
		<TD><b>NAME:</b></TD><TD><?php printf("%s",$patient_obj->first_name." ".$patient_obj->last_name);?></TD>
		
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
</div>
<br>
<fieldset>
	<legend>All Checkups Of : <?php printf("%s",$patient_obj->first_name." ".$patient_obj->last_name);?></legend>
</fieldset>
<br>
<?php
//------------------------------------Getting all checkups of this patient---------------------------//

$select_query='SELECT CR.`checkup_id`,C.`designation`,C.`first_name`,C.`last_name`,CR.`date_of_checkup`, CT.`name`, CR.`no_of_sessions`, CR.`valid_till_date`,CR.`referred_by`, PD.`first_name`, PD.`last_name` 
FROM `checkup_record` AS CR, `counselor` AS C,`patient_details` AS PD, `checkup_type` AS CT
WHERE CR.patient_id='.$passed_patient_id.' AND CR.patient_id=PD.patient_id AND CR.counselor_id=C.counselor_id AND CR.checkup_type_id=CT.checkup_type_id
ORDER BY CR.`date_of_checkup` DESC';
@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p><blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server

$checkup_record_obj=new CheckupRecord();
$counselor_obj=new Counselor();
$patient_cr_obj=new Patient();

$statement->bind_result($checkup_record_obj->checkup_id,
$counselor_obj->designation,
$counselor_obj->first_name,
$counselor_obj->last_name,
						
$checkup_record_obj->date_of_checkup,
$checkup_record_obj->checkup_type,
$checkup_record_obj->no_of_sessions,
$checkup_record_obj->valid_till_date,
$checkup_record_obj->referred_by,
						
$patient_cr_obj->first_name,
$patient_cr_obj->last_name);

$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p></blockquote>");
	printf("%s","&nbsp;<a class=\"button\" href='make_new_checkup_record.php?p_id=".$passed_patient_id."'>Add new checkup for this patient</a>");
	exit_script();
}

//Following lines will execute only if there is some data in the database
?>

<br>
<table>
<tr>

<TH>CHECKUP_ID</TH>
<TH>CONSULTANT</TH>
<TH>Patient Name</TH>
<TH>Date Of Checkup</TH>
<TH>Checkup Type</TH>
<TH>Sessions Left</TH>
<TH>Referred By</TH>

</tr>
<?php
$destination_url="get_checkup_sessions.php";
while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	printf("%s","<tr onClick=\"window.location.href='".$destination_url."?cr_id=".$checkup_record_obj->checkup_id."'\">");
	 printf("%s","<td><a href='".$destination_url."?cr_id=".$checkup_record_obj->checkup_id."'>".$checkup_record_obj->checkup_id."</a></td>");
	 printf("%s","<td>".$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");
	 printf("%s","<td>".$patient_cr_obj->first_name." ".$patient_cr_obj->last_name."</td>");
	 printf("%s","<td>".$checkup_record_obj->date_of_checkup."</td>");
	 printf("%s","<td>".$checkup_record_obj->checkup_type."</td>");
	 printf("%s","<td>".$checkup_record_obj->no_of_sessions."</td>");
	 printf("%s","<td>".$checkup_record_obj->referred_by."</td>");
	 printf("%s","<td><a href='update_checkup_record.php?cr_id=".$checkup_record_obj->checkup_id."'>EDIT</a></td>");
	printf("%s","</tr>");
	
}
?>

</table>
</br>

<?php
printf("%s","<a class=\"button\" href='make_new_checkup_record.php?p_id=".$passed_patient_id."'>Add new checkup for this patient</a>");
?>

</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>