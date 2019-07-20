<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
<script>

	function confirm_delete()
	{
		var ok=confirm("Are you sure that you want to delete this expense?");
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

<h3>Update Expense</h3>
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
if(empty($_REQUEST['ce_id']))
{
	 printf("%s","<blockquote><p>".NO_EXPENSE_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_clinic_expense_id=intval($_REQUEST['ce_id']);

//Following lines will execute only if the expense ID is received

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
?>

<h5><?php printf("%s",'Expense ID= '.$passed_clinic_expense_id); ?></h5>

<?php	
//------------------------------Checking whether the page was called after passing values---------------//
	
if(! empty($_POST['expense_name']) && ! empty($_POST['expense_price']) && ! empty($_POST['expense_quantity']) && ! empty($_POST['expense_date']))
{
	if(strcasecmp($_POST['is_delete'],'Y')==0)
	{
		$updateQuery="DELETE FROM `".CLINIC_EXPENSE_TABLE."` WHERE `expense_id`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('d',$passed_clinic_expense_id); //Binding the ? to it's value; s-string 

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
		printf('%s','<br><a href="get_all_clinic_expense.php" class="button"> Done</a><br>');

		}
	}
	else
	{
		
		$clinic_expense_update_obj=new ClinicExpense();
		$clinic_expense_update_obj->expense_name=htmlentities($_POST['expense_name']);
		$clinic_expense_update_obj->price=floatval($_POST['expense_price']);
		$clinic_expense_update_obj->quantity=intval($_POST['expense_quantity']);
		$clinic_expense_update_obj->date=htmlentities($_POST['expense_date']);

		if(! empty($_POST['expense_remarks']))
		{
			$clinic_expense_update_obj->remarks=htmlentities($_REQUEST['expense_remarks']);
		}

		$updateQuery="UPDATE `".CLINIC_EXPENSE_TABLE."` SET `expense_name`=?,`price`=?,`quantity`=?,`date`=?, `remark`=? WHERE `expense_id`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('sdissd',
							   $clinic_expense_update_obj->expense_name,
							   $clinic_expense_update_obj->price,
							   $clinic_expense_update_obj->quantity,
							   $clinic_expense_update_obj->date,
							   $clinic_expense_update_obj->remarks, 
							   $passed_clinic_expense_id); //Binding the ? to it's value; s-string 

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

$select_query='SELECT `expense_id`, `expense_name`, `price`, `quantity`, `date`, `remark` FROM `'.CLINIC_EXPENSE_TABLE.'` WHERE expense_id='.$passed_clinic_expense_id;


@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server
$clinic_expense_obj=new ClinicExpense();
	
$statement->bind_result($clinic_expense_obj->expense_id,
		   $clinic_expense_obj->expense_name,
		   $clinic_expense_obj->price,
		   $clinic_expense_obj->quantity,
		   $clinic_expense_obj->date,
		   $clinic_expense_obj->remarks);

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

	<form action="update_clinic_expense.php" id="update_form" method="post">
	
		<p><label for="name">Name *:</label> <input type="text" maxlength="200" value="<?php printf('%s',$clinic_expense_obj->expense_name);?>" required name="expense_name"/></p>
		
	<p><label for="name">Price *:</label> <input type="number" min=1 required value="<?php printf('%s',$clinic_expense_obj->price);?>" placeholder="Enter amount" name="expense_price"/></p>
	
	<p><label for="name">Quantity *: </label><input type="number" requried min=1  value="<?php printf('%s',$clinic_expense_obj->quantity);?>" name="expense_quantity"/></p>
	
	<p><label for="name">Date *: </label><input type="date" requried value="<?php printf('%s',$clinic_expense_obj->date);?>" name="expense_date"/></p>
	
	<p><label for="name">Remarks :</label> <input type="text" maxlength="500" value="<?php printf('%s',$clinic_expense_obj->remarks);?>" name="expense_remarks"/></p>
		
		<input type="hidden" name="ce_id" value="<?php printf("%d",$clinic_expense_obj->expense_id);?>" />
		<input type="hidden" name="is_delete" id="is_delete_id" value="N" />
		<input type="submit" class="formbutton" name="update" value="Update"/>
		<input type="button" class="formbutton" name="delete" value="Delete" onClick="confirm_delete();" />
		<a href="get_all_clinic_expense.php?" class="button"> Cancel</a>
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
