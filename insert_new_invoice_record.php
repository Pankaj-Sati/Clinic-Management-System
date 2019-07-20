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

$invoice_record_obj=new InvoiceRecord();
$invoice_record_obj->checkup_id=intval($_POST['cr_id']);


if(empty($_POST['invoice_amount']))
{
	 printf("%s",NO_INVOICE_AMOUNT_ERROR);
	 header("Location:make_new_invoice_record.php?cr_id=".$_POST['cr_id']);
	 exit; //To stop executing the php statements further
}
$invoice_record_obj->amount=floatval($_POST['invoice_amount']);

if(empty($_POST['invoice_date']))
{
	 printf("%s",NO_INVOICE_DATE_ERROR);
	 header("Location:make_new_invoice_record.php?cr_id=".$_POST['cr_id']);
	 exit; //To stop executing the php statements further
}
$invoice_record_obj->date=htmlentities($_POST['invoice_date']);

if(empty($_POST['invoice_payed_by']))
{
	 printf("%s",NO_INVOICE_PAYED_BY_ERROR);
	 header("Location:make_new_invoice_record.php?cr_id=".$_POST['cr_id']);
	 exit; //To stop executing the php statements further
}
$invoice_record_obj->payed_by=htmlentities($_POST['invoice_payed_by']);

//-----------------------------Getting optional parameters-----------------------------//
if(! empty($_POST['invoice_remarks']))
{
	$invoice_record_obj->remarks=htmlentities($_POST['invoice_remarks']);
}



//Following lines will execute only if the required fields are received

//--------------------------------Connecting to the database------------------------------------------//

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s",DATABASE_CONNECION_ERROR);
	 exit; //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful
$insertQuery="INSERT INTO `".INVOICE_RECORD_TABLE."`(`checkup_id`, `amount`, `date`, `payed_by`, `remarks`)"
			." VALUES(?,?,?,?,?);";

$statement=$database->prepare($insertQuery); //Prepare a prepared-statement to avoid sql injection attacks
$statement->bind_param('ddsss',
		   $invoice_record_obj->checkup_id,
		   $invoice_record_obj->amount,
		   $invoice_record_obj->date,
		   $invoice_record_obj->payed_by,
		   $invoice_record_obj->remarks); //Binding the ? to it's value; s-string 

if(!$statement->execute()) //Actually send the data to the MySQL server
{
	//true means that insert query was successful, false means unsuccessful
	printf('%s',DATABASE_INSERT_ERROR); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	exit; //Exit executing php further
}

//-----------------Following lines will execute only if database insertion was successful------------------------//

printf("%s","Insert Successful");
header("Location:get_checkup_sessions.php?cr_id=".$invoice_record_obj->checkup_id);

?>