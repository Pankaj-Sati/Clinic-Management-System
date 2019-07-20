<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
<script>

	function confirm_delete()
	{
		var ok=confirm("Are you sure that you want to delete this session?");
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

<h3>Update Checkup Session</h3>
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
if(empty($_REQUEST['cs_id']))
{
	 printf("%s","<blockquote><p>".NO_CHECKUP_SESSION_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_session_id=intval($_REQUEST['cs_id']);

//Following lines will execute only if the session number is received

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
?>

<h5><?php printf("%s",'Session Number= '.$passed_session_id); ?></h5>

<?php	
//------------------------------Checking whether the page was called after passing values---------------//
if(! empty($_POST['session_date']) && ! empty($_POST['is_delete']) && ! empty($_POST['cr_id']))
{
	if(strcasecmp($_POST['is_delete'],'Y')==0)
	{
		//Delete the session
		$updateQuery="DELETE FROM `checkup_session` WHERE `session_number`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('d',$passed_session_id); //Binding the ? to it's value; s-string 

		if(!$statement->execute()) //Actually send the data to the MySQL server
		{
			//true means that insert query was successful, false means unsuccessful
			printf('%s',"<blockquote><p>".DATABASE_DELETE_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
		}
		//Following lines will execute only if database updation was successful
		else
		{
			printf('%s',"<blockquote><p>SUCCESSFULLY DELETED</p></blockquote>"); //printf() gives advantage of 
			printf('%s','<br><a href="index.php" class="button"> Done</a><br>');
		

		}
	}
	else
	{
		$session_update_obj=new CheckupSession();
		$session_update_obj->date=htmlentities($_POST['session_date']);

		if(! empty($_POST['session_remarks']))
		{
			$session_update_obj->remarks=htmlentities($_REQUEST['session_remarks']);
		}

		$updateQuery="UPDATE `checkup_session` SET `date`=?,`remarks`=? WHERE `session_number`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('ssd',
							   $session_update_obj->date,
							   $session_update_obj->remarks,
							   $passed_session_id
			 ); //Binding the ? to it's value; s-string 

		if(!$statement->execute()) //Actually send the data to the MySQL server
		{
			//true means that insert query was successful, false means unsuccessful
			printf('%s',"<blockquote><p>".DATABASE_UPDATE_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
		}
		//Following lines will execute only if database updation was successful
		else
		{
			//Following lines will execute only if database updation was successful
		printf('%s',"<blockquote><p>".DATABASE_UPDATE_SUCCESS."</p></blockquote>"); //printf() gives advantage of specifying 

		}
	}
	
	
}
//The following lines will run only if the connection to database server is successful

$select_query='SELECT `session_number`, `date`, `remarks`, `checkup_id` FROM `checkup_session` 
WHERE session_number='.$passed_session_id;


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
					   $checkup_session_obj->remarks,
					   $checkup_session_obj->checkup_id);

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

	<form action="update_checkup_session.php" id="update_form" method="post">
		<p><label for="name">Session Date*: </label> <input type="date" value="<?php printf('%s', $checkup_session_obj->date)?>" required name="session_date"/></p>
		<p><label for="name">Remarks: </label><input type="text" name="session_remarks" value="<?php printf('%s', $checkup_session_obj->remarks)?>"/></p>
		<input type="hidden" name="cs_id" value="<?php printf("%d",$passed_session_id);?>" />
		<input type="hidden" name="cr_id" value="<?php printf("%d",$checkup_session_obj->checkup_id);?>" />
		<input type="hidden" name="is_delete" id="is_delete_id" value="N" />
		<input type="submit" class="formbutton" name="update" value="Update"/>
		<input type="button" class="formbutton" name="delete" value="Delete" onClick="confirm_delete();" />
		<a href="get_checkup_sessions.php?cr_id=<?php printf('%d', $checkup_session_obj->checkup_id)?>" class="button">Cancel</a>
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
