<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations



//------checking the required fields---------//
if(empty($_POST['cr_id']))
{
	 printf("%s",NO_CHECKUP_RECORD_ID_ERROR);
	 exit; //To stop executing the php statements further
}

$checkup_session_obj=new CheckupSession();
$checkup_session_obj->checkup_id=intval($_POST['cr_id']);

echo intval($_POST['cr_id']);
if(empty($_POST['session_date']))
{
	 printf("%s",NO_SESSION_DATE_ERROR);
	 exit; //To stop executing the php statements further
}
$checkup_session_obj->date=htmlentities($_POST['session_date']);

//----------------Getting optional fields------------------//
if(! empty($_POST['session_remarks']))
{
  $checkup_session_obj->remarks=htmlentities($_POST['session_remarks']); 
}

//Following lines will execute only if the required fields are received

//--------------------------------Connecting to the database------------------------------------------//

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s",DATABASE_CONNECION_ERROR);
	 exit; //To stop executing the php statements further
}

print_r($checkup_session_obj);
//The following lines will run only if the connection to database server is successful
$insertQuery="INSERT INTO `".CHECKUP_SESSION_TABLE."`(`checkup_id`, `date`, `remarks`)"
			." VALUES(?,?,?);";

$statement=$database->prepare($insertQuery); //Prepare a prepared-statement to avoid sql injection attacks
$statement->bind_param('dss',
		   $checkup_session_obj->checkup_id,
		   $checkup_session_obj->date,
		   $checkup_session_obj->remarks ); //Binding the ? to it's value; s-string 

if(!$statement->execute()) //Actually send the data to the MySQL server
{
	//true means that insert query was successful, false means unsuccessful
	printf('%s',DATABASE_INSERT_ERROR); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	exit; //Exit executing php further
}

//-----------------Following lines will execute only if database insertion was successful------------------------//

printf("%s","Insert Successful");

header("Location:".$_SERVER['HTTP_REFERER']);

?>