<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />

<script>

	function confirm_delete()
	{
		var ok=confirm("Are you sure that you want to delete this Checkup?");
		if(ok)
			{
				document.getElementById('is_delete_id').value="Y";
				document.getElementById('update_form').submit();
			}
	}
	
</script>

</head>

<body>

<section id="body" class="width">
		
<!-- ----------------------------Including the sidebar ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar ?>

<section id="content" class="column-left" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>Update Checkup</h3>
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
$passed_checkup_id=intval($_REQUEST['cr_id']);

//Following lines will execute only if the patient id is received

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
?>

<h5><?php printf("%s",'Checkup ID= '.$passed_checkup_id); ?></h5>

<?php	
//------------------------------Checking whether the page was called after passing values---------------//
	
if(! empty($_POST['checkup_date']) && ! empty($_POST['counselor_id']) && ! empty($_POST['checkup_type_id']) && isset($_POST['no_of_sessions']))
{
	if(strcasecmp($_POST['is_delete'],'Y')==0)
	{
		//------------A checkup record can have crucial info. of Invoice data. Therefore, we will check whether this checkup has any invoice data or not. If yes, we will not delete it untill all invoice are deleted--------//
		
		$checkInvoiceQuery="SELECT count(invoice_number) FROM `invoice_record` WHERE checkup_id=".$passed_checkup_id;
		$statement=$database->prepare($checkInvoiceQuery); //Prepare a prepared-statement to avoid sql injection attacks
	
		
		if(!$statement->execute()) //Actually send the data to the MySQL server
		{
			//true means that insert query was successful, false means unsuccessful
			printf('%s',"<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
			exit_script();
		}
		
		$invoiceCount=0;
		
		$statement->bind_result($invoiceCount); //There will alwas be 1 row i.e count fetched from the database
		$statement->store_result(); //Stores all the fetched rows in the statement object
		$statement->fetch();
		
		if($statement->num_rows==0 || intval($invoiceCount)==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
		{
			
			//number of rows ==0 means that there is no invoice for this chekup in the database. If there were invoice, then it would have returned 1 row with count of invoices 
			
			$statement->close();
			$updateQuery="DELETE FROM `checkup_session` WHERE `checkup_id`=?;";
			$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
			$statement->bind_param('d',$passed_checkup_id); //Binding the ? to it's value; s-string 
		
			if(!$statement->execute()) //Actually send the data to the MySQL server
			{
				//true means that insert query was successful, false means unsuccessful
				printf('%s',"<blockquote><p>".DATABASE_DELETE_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
			}
			else
			{
				$updateQuery="DELETE FROM `checkup_record` WHERE checkup_id=?";
				$statement->close();
				$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
				$statement->bind_param('d',$passed_checkup_id); //Binding the ? to it's value; s-string 

				if(!$statement->execute()) //Actually send the data to the MySQL server
				{
					//true means that insert query was successful, false means unsuccessful
					printf('%s',"<blockquote><p>".DATABASE_DELETE_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
				}

				//Following lines will execute only if database updation was successful
				else
				{
					//Following lines will execute only if database updation was successful
				printf('%s',"<blockquote><p>SUCCESSFULLY DELETED</p></blockquote>"); //printf() gives advantage of specifying 
				printf('%s','<br><a href="index.php" class="button"> Done</a><br>');

				}
				$statement->close();
			}
			
			
		}
		else
		{
			printf("%s","<blockquote><p>".CHECKUP_DELETE_ERROR."</p></blockquote>"); //Telling user that we cannot delete checkup
			printf('%s','<br><a href="get_checkup_invoice.php?cr_id='.$passed_checkup_id.'" class="button"> See All Invoice</a><br>');
			exit_script();
		}
		
			
	}
	else
	{
		$checkup_update_obj=new CheckupRecord();
		$checkup_update_obj->date_of_checkup=htmlentities($_POST['checkup_date']);
		$checkup_update_obj->counselor_id=intval($_POST['counselor_id']);
		$checkup_update_obj->checkup_type=intval($_REQUEST['checkup_type_id']);
		$checkup_update_obj->no_of_sessions=intval($_REQUEST['no_of_sessions']);

		if( ! empty($_REQUEST['referred_by']))
		{
			//If this field is null, database will automatically change the referred by to SELF
			$checkup_update_obj->referred_by=htmlentities($_REQUEST['referred_by']);
		}
		if(! empty($_POST['checkup_remarks']))
		{
			$checkup_update_obj->remarks=htmlentities($_REQUEST['checkup_remarks']);
		}

		$updateQuery="UPDATE `checkup_record` SET `date_of_checkup`=?,`counselor_id`=?,`checkup_type_id`=?,`no_of_sessions`=?,`remarks`=?,`referred_by`=? WHERE `checkup_id`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('sdddssd',
			 $checkup_update_obj->date_of_checkup,
			 $checkup_update_obj->counselor_id,
			 $checkup_update_obj->checkup_type,
			 $checkup_update_obj->no_of_sessions,
			 $checkup_update_obj->remarks,
			 $checkup_update_obj->referred_by,
			 $passed_checkup_id); //Binding the ? to it's value; s-string 

		if(!$statement->execute()) //Actually send the data to the MySQL server
		{
			//true means that insert query was successful, false means unsuccessful
			printf('%s',"<blockquote><p>".DATABASE_UPDATE_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
			$statement->close();
		}
		else
		{
			//Following lines will execute only if database updation was successful
		printf('%s',"<blockquote><p>".DATABASE_UPDATE_SUCCESS."</p></blockquote>"); //printf() gives advantage of specifying 

		}
	}
	
	
	
}
//The following lines will run only if the connection to database server is successful

$select_query='SELECT CR.`checkup_id`, CR.`date_of_checkup`, CR.`counselor_id`, CT.`name`, CR.`no_of_sessions`, CR.`valid_till_date`, CR.`remarks`,CR.`referred_by`,C.`counselor_id`,C.`designation`, C.`first_name`,C.`last_name`
FROM `checkup_record` AS CR, `counselor` AS C, `checkup_type` AS CT
WHERE CR.counselor_id=C.counselor_id AND CT.checkup_type_id=CR.checkup_type_id AND CR.checkup_id='.$passed_checkup_id;

@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server
$checkup_record_obj=new CheckupRecord();
$counselor_obj=new Counselor();


$statement->bind_result($checkup_record_obj->checkup_id,
	$checkup_record_obj->date_of_checkup,
	$checkup_record_obj->counselor_id,
	$checkup_record_obj->checkup_type,
	$checkup_record_obj->no_of_sessions,
	$checkup_record_obj->valid_till_date,
	$checkup_record_obj->remarks,
	$checkup_record_obj->referred_by,

	$counselor_obj->counselor_id,
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
?>
<br>

<fieldset>
<legend>Fields marked with * are compulsary</legend>

	<form action="update_checkup_record.php" id="update_form" method="post">
		<p><label for="name">Date *:</label><input type="date" value="<?php printf('%s',$checkup_record_obj->date_of_checkup)?>" required name="checkup_date"/></p>
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

			$counselor_new_obj=new Counselor();
			$statement->bind_result(
			$counselor_new_obj->counselor_id,
			 $counselor_new_obj->designation,
			 $counselor_new_obj->first_name,
			 $counselor_new_obj->last_name );

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
				if($counselor_obj->counselor_id==$counselor_new_obj->counselor_id)
				{
					printf("%s","<option selected value=".$counselor_new_obj->counselor_id.">".$counselor_new_obj->designation." ".$counselor_new_obj->first_name.' '.$counselor_new_obj->last_name."</option>");
				
				}
				else
				{
					printf("%s","<option value=".$counselor_new_obj->counselor_id.">".$counselor_new_obj->designation." ".$counselor_new_obj->first_name.' '.$counselor_new_obj->last_name."</option>");
				
				}
				

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
				if(strcasecmp($checkup_record_obj->checkup_type,$checkup_type_obj->name)==0)
				{
					printf("%s","<option selected value=".$checkup_type_obj->checkup_type_id.">".$checkup_type_obj->name."</option>");

				}
				else
				{
					printf("%s","<option value=".$checkup_type_obj->checkup_type_id.">".$checkup_type_obj->name."</option>");

				}
				
			}

			?>
		</select></p>

		<p><label for="name">Sessions Left *: </label><input required type="number" name="no_of_sessions" value="<?php printf('%d',$checkup_record_obj->no_of_sessions); ?>"></p>
		
		<p><label for="name">Referred By *: </label><input required type="text" value="<?php printf("%s",$checkup_record_obj->referred_by)?>" name="referred_by"/></p>
		<p><label for="name">Remarks: </label><input type="text"  name="checkup_remarks" value="<?php printf('%s',$checkup_record_obj->remarks); ?>" /></p>
		<input type="hidden"  name="cr_id" value="<?php printf("%d",$passed_checkup_id);?>"/>
		
		<input type="hidden" name="is_delete" id="is_delete_id" value="N" />
		<input type="button" class="formbutton" name="delete" value="Delete" onClick="confirm_delete();" />
		<input type="submit" class="formbutton" value="Update"/>
		<a href="get_checkup_sessions.php?cr_id=<?php printf("%d",$passed_checkup_id)?>" class="button"> Cancel</a>
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
