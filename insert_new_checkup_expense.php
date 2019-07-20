<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations



//------checking the required fields---------//

$clinic_expense_obj=new ClinicExpense();


if(empty($_POST['expense_name']))
{
	 printf("%s",NO_EXPENSE_NAME_ERROR);
	 header("Location:make_new_clinic_expense.php");
	 exit; //To stop executing the php statements further
}
$clinic_expense_obj->expense_name=htmlentities($_POST['expense_name']);

if(empty($_POST['expense_price']))
{
	 printf("%s",NO_EXPENSE_PRICE_ERROR);
	 header("Location:make_new_clinic_expense.php");
	 exit; //To stop executing the php statements further
}
$clinic_expense_obj->price=floatval($_POST['expense_price']);

if(empty($_POST['expense_quantity']))
{
	 printf("%s",NO_EXPENSE_QUANTITY_ERROR);
	 header("Location:make_new_clinic_expense.php");
	 exit; //To stop executing the php statements further
}
$clinic_expense_obj->quantity=intval($_POST['expense_quantity']);

if(empty($_POST['expense_date']))
{
	 printf("%s",NO_EXPENSE_DATE_ERROR);
	 header("Location:make_new_clinic_expense.php");
	 exit; //To stop executing the php statements further
}
$clinic_expense_obj->date=htmlentities($_POST['expense_date']);

//-----------------------------Getting optional parameters-----------------------------//
if(! empty($_POST['expense_remarks']))
{
	$clinic_expense_obj->remarks=htmlentities($_POST['expense_remarks']);
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
$insertQuery="INSERT INTO `".CLINIC_EXPENSE_TABLE."`(`expense_name`, `price`, `quantity`, `date`, `remark`) VALUES (?,?,?,?,?)";

$statement=$database->prepare($insertQuery); //Prepare a prepared-statement to avoid sql injection attacks
$statement->bind_param('sdiss',
		   $clinic_expense_obj->expense_name,
		   $clinic_expense_obj->price,
		   $clinic_expense_obj->quantity,
		   $clinic_expense_obj->date,
		   $clinic_expense_obj->remarks); //Binding the ? to it's value; s-string 

if(!$statement->execute()) //Actually send the data to the MySQL server
{
	//true means that insert query was successful, false means unsuccessful
	printf('%s',DATABASE_INSERT_ERROR); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	exit; //Exit executing php further
}

//-----------------Following lines will execute only if database insertion was successful------------------------//

printf("%s","Insert Successful");
header("Location:get_all_clinic_expense.php");

?>