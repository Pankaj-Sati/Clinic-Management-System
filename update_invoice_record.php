<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
<script>

	function confirm_delete()
	{
		var ok=confirm("Are you sure that you want to delete this invoice?");
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

<h3>Update Invoice</h3>
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
if(empty($_REQUEST['ir_id']))
{
	 printf("%s","<blockquote><p>".NO_INVOICE_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_invoice_record_id=intval($_REQUEST['ir_id']);

//Following lines will execute only if the session number is received

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
?>

<h5><?php printf("%s",'Invoice Number= '.$passed_invoice_record_id); ?></h5>

<?php	
//------------------------------Checking whether the page was called after passing values---------------//
	
if(! empty($_POST['invoice_amount']) && ! empty($_POST['invoice_date']) && ! empty($_POST['invoice_payed_by']) && ! empty($_POST['is_delete']))
{
	if(strcasecmp($_POST['is_delete'],'Y')==0)
	{
		$updateQuery="DELETE FROM `invoice_record` WHERE `invoice_number`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('d',$passed_invoice_record_id); //Binding the ? to it's value; s-string 

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
		printf('%s','<br><a href="get_all_checkup_invoice.php" class="button"> Done</a><br>');

		}
	}
	else
	{
		$invoice_update_obj=new InvoiceRecord();
		$invoice_update_obj->amount=floatval($_POST['invoice_amount']);
		$invoice_update_obj->date=htmlentities($_POST['invoice_date']);
		$invoice_update_obj->payed_by=htmlentities($_POST['invoice_payed_by']);

		if(! empty($_POST['invoice_remarks']))
		{
			$invoice_update_obj->remarks=htmlentities($_REQUEST['invoice_remarks']);
		}

		$updateQuery="UPDATE `invoice_record` SET `amount`=?,`date`=?,`payed_by`=?,`remarks`=? WHERE `invoice_number`=?";

		$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
		$statement->bind_param('dsssd',
							   $invoice_update_obj->amount,
							   $invoice_update_obj->date,
							   $invoice_update_obj->payed_by,
							   $invoice_update_obj->remarks,
							   $passed_invoice_record_id); //Binding the ? to it's value; s-string 

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

$select_query='SELECT `invoice_number`, `checkup_id`,`amount`, `date`, `payed_by`, `remarks` FROM `invoice_record`
WHERE invoice_number='.$passed_invoice_record_id;


@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server
$invoice_record_obj=new InvoiceRecord();
	
$statement->bind_result(
	$invoice_record_obj->invoice_number,
	$invoice_record_obj->checkup_id,
	$invoice_record_obj->amount,
	$invoice_record_obj->date,
	$invoice_record_obj->payed_by,
	$invoice_record_obj->remarks);

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

	<form action="update_invoice_record.php" id="update_form" method="post">
		<p><label for="name">Amount *: </label><input type="number" required name="invoice_amount" value="<?php printf('%f',$invoice_record_obj->amount);?>" /></p>
		<p><label for="name">Invoice Date *: </label> <input type="date" value="<?php printf('%s',$invoice_record_obj->date)?>" required name="invoice_date"/></p>
		<p><label for="name">Payed By *: </label>
		<select required name="invoice_payed_by"></p>
			<option <?php if(strcasecmp($invoice_record_obj->payed_by,"CASH")==0){printf('%s','selected');}?> value="CASH">CASH</option>
			<option <?php if(strcasecmp($invoice_record_obj->payed_by,"CARD")==0){printf('%s','selected');}?> value="CARD">CARD</option>
			<option <?php if(strcasecmp($invoice_record_obj->payed_by,"OTHER")==0){printf('%s','selected');}?> value="OTHER">OTHER</option>
		</select>
		<p><label for="name">Remarks : </label><input type="text" value="<?php printf('%s',$invoice_record_obj->remarks)?>" name="invoice_remarks"/></p>
		<input type="hidden" name="ir_id" value="<?php printf("%d",$passed_invoice_record_id);?>" />
		<input type="hidden" name="is_delete" id="is_delete_id" value="N" />
		<input type="submit" class="formbutton" name="update" value="Update"/>
		<input type="button" class="formbutton" name="delete" value="Delete" onClick="confirm_delete();" />
		<a href="get_checkup_invoice.php?cr_id=<?php printf("%d",$invoice_record_obj->checkup_id)?>" class="button"> Cancel</a>
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
