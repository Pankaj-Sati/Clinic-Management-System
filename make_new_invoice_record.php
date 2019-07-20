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
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar ?>

<section id="content" class="column-left" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>Add New Invoice</h3>
<br>
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

$select_query='SELECT PD.`patient_id`, PD.`first_name`, PD.`last_name`, PD.`year_of_birth`, PD.`gender`, PD.`weight`, PD.`height`, PD.`city`, PD.`locality`, PD.`street_address`, PD.`occupation`, PD.`mobile_number`, PD.`email_id`, PD.`blood_group`,CR.`checkup_id`, CR.`date_of_checkup`, CR.`counselor_id`, CT.`name`, CR.`no_of_sessions`, CR.`valid_till_date`, CR.`remarks`,CR.`referred_by`,C.`designation`, C.`first_name`,C.`last_name`
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
<fieldset>
	<legend>Patient Details</legend> 
	<a id="NotToPrint" style="float: right" href="update_patient_details.php?p_id=<?php printf("%d",$patient_obj->patient_id);?>" >EDIT</a>

</fieldset>
<table>
<!-- -------------------Printing patient Details------------------- -->
	<tr>

		<TD><b>PATIENT_ID:</b><?php printf("%s","<td><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>$patient_obj->patient_id</a></td>");?></TD>
		<TD><b>NAME:</b><?php printf("%s","<td>".$patient_obj->first_name." ".$patient_obj->last_name."</td>");?></TD>
		<TD><b>AGE:</b><?php printf("%s","<td>".(intval(date('Y'))-intval($patient_obj->year_of_birth))."</td>");?></TD>
		<TD><b>GENDER:</b><?php printf("%s","<td>".$patient_obj->gender."</td>");?></TD>
		
		
	</tr>
	<tr>
		<TD><b>WEIGHT:</b><?php printf("%s","<td>".$patient_obj->weight." Kg</td>");?></TD>
		<TD><b>HEIGHT:</b><?php printf("%s","<td>".$patient_obj->height." cm</td>");?></TD>
		<TD><b>CITY:</b><?php printf("%s","<td>".$patient_obj->city."</td>");?></TD>
		<TD><b>ADDRESS:</b><?php printf("%s","<td>".$patient_obj->locality." ".$patient_obj->street_address."</td>");?></TD>
		
		
	</tr>
	<tr>
		<TD><b>OCCUPATION:</b><?php printf("%s","<td>".$patient_obj->occupation."</td>");?></TD>
		<TD><b>MOBILE:</b><?php printf("%s","<td>".$patient_obj->mobile_number."</td>");?></TD>
		<TD><b>EMAIL:</b><?php printf("%s","<td>".$patient_obj->email_id."</td>");?></TD>
		<TD><b>BLOOD GROUP:</b><?php printf("%s","<td>".$patient_obj->blood_group."</td>");?></TD>
	</tr>

</table>
<br>

<fieldset>
	<legend>Checkup Details</legend> 
</fieldset>
<a id="NotToPrint" href="update_checkup_record.php?cr_id=<?php printf("%s",$checkup_record_obj->checkup_id);?>" style="float: right">EDIT</a>
<br  id='NotToPrint'>
<br>

<!-- -------------------Printing Checkup Details------------------- -->
<table>
	<tr>
		<TD><b>ID:</b><?php printf("%s","<td>".$checkup_record_obj->checkup_id."</td>");?></TD>
		<TD><b>CONSULTANT:</b><?php printf("%s","<td>".$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");?></TD>
		<TD><b>DATE:</b><?php printf("%s","<td>".$checkup_record_obj->date_of_checkup."</td>");?></TD>
		<TD><b>TYPE:</b><?php printf("%s","<td>".$checkup_record_obj->checkup_type."</td>");?></TD>
		
	</tr>
	<tr>
		<TD><b>SESSIONS LEFT:</b><?php printf("%s","<td>".$checkup_record_obj->no_of_sessions."</td>");?></TD>
		<TD><b>REFERRED BY:</b><?php printf("%s","<td>".$checkup_record_obj->referred_by."</td>");?></TD>
		<TD><b>REMARKS:</b><?php printf("%s","<td>".$checkup_record_obj->remarks."</td>");?></TD>

	</tr>

</table>
<br>

<fieldset>
	<legend>Add New Invoice</legend>
<br>


<!-- ---------If everything works well till now,we will make form to add new invoice data------------------- -->

Fields marked with * are compulsary <br>

	<form action="insert_new_invoice_record.php" method="post">
		<p><label for="name">Amount *: </label><input type="number" required name="invoice_amount"/></p>
		<p><label for="name">Invoice Date *: </label> <input type="date" value="<?php printf('%s',date('Y-m-d'))?>" required name="invoice_date"/></p>
		<p><label for="name">Payed By *: </label>
		<select required name="invoice_payed_by"></p>
			<option value="CASH">CASH</option>
			<option value="CARD">CARD</option>
			<option value="OTHER">OTHER</option>
		</select>
		<p><label for="name">Remarks : </label><input type="text" name="invoice_remarks"/></p>
		<input type="hidden" name="cr_id" value="<?php printf("%d",$passed_cr_id);?>" />
		<input type="submit" class="formbutton"/>
	<a class="normalbutton" href="<?php printf("%s",$_SERVER['HTTP_REFERER']); ?>" value="Back">Back</a>
	</form>
</fieldset>

</article>
<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>
