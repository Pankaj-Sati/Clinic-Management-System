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

<h3>New Checkup</h3>
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
if(empty($_REQUEST['p_id']))
{
	 printf("%s","<blockquote><p>".NO_PATIENT_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_patient_id=intval($_REQUEST['p_id']);

//Following lines will execute only if the patient id is received

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful

$select_query="SELECT `".PATIENT_ID."`,`".FIRST_NAME."`,`".LAST_NAME."`, `".YEAR_OF_BIRTH."`, `".GENDER."`, `".WEIGHT."`, `".HEIGHT."`, `".CITY."`, `".LOCALITY."`, `".STREET_ADDRESS."`, `".OCCUPATION."`, `".MOBILE_NUMBER."`, `".EMAIL_ID."`, `".BLOOD_GROUP."` FROM `".PATIENT_DETAILS_TABLE."` WHERE ".PATIENT_ID."=".$passed_patient_id;

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
	 $patient_obj->blood_group
);

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
?>
<fieldset><legend>Patient Details</legend></fieldset>
<a id="NotToPrint" style="float: right" href="update_patient_details.php?p_id=<?php printf("%d",$patient_obj->patient_id);?>" >EDIT</a>

<br>
<table>
<!-- -------------------Printing patient Details------------------- -->
	<tr>

		<TD><b>PATIENT_ID:</b><?php printf("%s","<td><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>$patient_obj->patient_id</a></td>");?></TD>
		<TD><b>NAME:</b><?php printf("%s","<td>".$patient_obj->first_name." ".$patient_obj->last_name."</td>");?></TD>
		<TD><b>AGE:</b><?php printf("%s","<td>".(intval(date('Y'))-intval($patient_obj->year_of_birth))."</td>");?></TD>
		<TD><b>GENDER:</b><?php printf("%s","<td>".$patient_obj->gender."</td>");?></TD>
		
		
	</tr>
	<tr>
		<TD><b>WEIGHT:</b><?php printf("%s","<td>".$patient_obj->weight."</td>");?></TD>
		<TD><b>HEIGHT:</b><?php printf("%s","<td>".$patient_obj->height."</td>");?></TD>
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
<legend>Fields marked with * are compulsary</legend>

	<form action="insert_new_checkup_record.php" method="post">
		<p><label for="name">Date *:</label><input type="date" value="<?php printf('%s',date('Y-m-d'))?>" required name="checkup_date"/></p>
		<p><label for="name">CONSULTANT *: </label>
		<select required name="counselor_id">
			<?php

				//-------------------------------------Getting list of all the counselors--------------------------//

			$select_query='SELECT `counselor_id`,`designation`,`first_name`, `last_name` FROM `counselor`';
			@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

			if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
			{
				//execute() returns false or failure therefor we checked whether it is false.
				//'!' will make false to true which will execute this if block
				printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
				exit_script();

			}

			//The following lines will execute only if the query ran successfully on the database server

			$counselor_obj=new Counselor();
			$statement->bind_result(
			$counselor_obj->counselor_id,
			 $counselor_obj->designation,
			 $counselor_obj->first_name,
			 $counselor_obj->last_name );

			$statement->store_result(); //Stores all the fetched rows in the statement object
			if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
			{
				//number of rows ==0 means that there is no record in the database 
				printf("%s","<blockquote><p>".NO_COUNSELOR_ERROR."</p></blockquote>");
				exit_script();
			}

			//Following lines will execute only if there is some data in the database
			while($statement->fetch())
			{
				printf("%s","<option value=".$counselor_obj->counselor_id.">".$counselor_obj->designation." ".$counselor_obj->first_name.' '.$counselor_obj->last_name."</option>");

			}

			?>
		</select></p>

		<p><label for="name">Checkup Type *: </label>
		<select required name="checkup_type_id">
			<?php

				//-------------------------------------Getting list of all the checkup types--------------------------//

			$select_query='SELECT `checkup_type_id`, `name` FROM `checkup_type`';
			@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

			if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
			{
				//execute() returns false or failure therefor we checked whether it is false.
				//'!' will make false to true which will execute this if block
				printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
				exit_script();

			}

			//The following lines will execute only if the query ran successfully on the database server

			$checkup_type_obj=new CheckupType();
			$statement->bind_result(
				$checkup_type_obj->checkup_type_id,
				 $checkup_type_obj->name);

			$statement->store_result(); //Stores all the fetched rows in the statement object
			if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
			{
				//number of rows ==0 means that there is no record in the database 
				printf("%s","<blockquote><p>".NO_CHECKUP_TYPE_ERROR."</p></blockquote>");
				exit_script();
			}

			//Following lines will execute only if there is some data in the database
			while($statement->fetch())
			{
				printf("%s","<option value=".$checkup_type_obj->checkup_type_id.">".$checkup_type_obj->name."</option>");

			}

			?>
		</select></p>

		<p><label for="name">No. Of Sessions *: </label><input required type="number" value="1" min=1 name="no_of_sessions"/></p>
		
		<p><label for="name">Referred By *: </label><input required type="text" value="SELF" name="referred_by"/></p>
		<p><label for="name">Remarks: </label><input type="text"  name="checkup_remarks"/></p>
		<input type="hidden"  name="p_id" value="<?php printf("%d",$passed_patient_id)?>"/>

		<input type="submit" class="formbutton"/>
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
