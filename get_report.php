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

<h3>Clinic Report</h3>
<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations

	@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statementInvoice

	if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
	{
		 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
		 exit_script(); //To stop executing the php statementInvoices further
	}
	
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
?>

<fieldset>
	<form action="get_report.php" method="post">
		<p>
					
			From *:&nbsp; <input type="date" required <?php if(! empty($_POST['report_from_date'])) {printf("%s",'value="'.$_POST['report_from_date'].'"');}?> name="report_from_date"/>
			
			&nbsp; To *:&nbsp;<input type="date" required <?php if(! empty($_POST['report_to_date'])) {printf("%s",'value="'.$_POST['report_to_date'].'"');}?> name="report_to_date"/>
			
			&nbsp;<input type="hidden" name="report_generate" value="report_generation"/>
			
			&nbsp;<input type="radio" name="sort_by" checked value="IN">Invoice Date</input>
			
			&nbsp;<input type="radio" name="sort_by" <?php if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'CHECKUP_DATE')==0 ){printf("checked");}?> value="CHECKUP_DATE">Checkup Date</input>
			
			&nbsp;<input type="radio" name="sort_by" <?php if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'AGE')==0 ){printf("checked");}?> value="AGE">Patient Age</input>
			
			&nbsp;<input type="radio" name="sort_by" <?php if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'P_NAME')==0 ){printf("checked");}?> value="P_NAME">Patient Name</input>
			
			&nbsp;<input type="submit" class="formbutton" value="GO"/>
		</p>
		
		<p><label for="name">Patients Of:</label> <select required name="patients_of">
			<?php

				//-------------------------------------Getting list of all the counselors--------------------------//

			$select_query='SELECT `counselor_id`,`designation`,`first_name`, `last_name` FROM `counselor`';
			$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

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

			printf("%s","No counselor error");
			//Following lines will execute only if there is some data in the database
			if(! empty($_POST['patients_of'])) //Checking whether already searched by doctor
			{
				$doctor_id=intval($_POST['patients_of']);
			}
			else
			{
				$doctor_id=-9999; //imaginary value; not valid	
			}

			printf("%s","<option value=-1");
			if($doctor_id==-1)
			{
				printf("%s"," selected ");
			}
			printf("%s",">All</option>");

			while($statement->fetch())
			{
				printf("%s","<option value=".$counselor_obj->counselor_id);
				if($doctor_id==$counselor_obj->counselor_id)
				{
					printf("%s"," selected ");
				}
				printf("%s",">".$counselor_obj->designation." ".$counselor_obj->first_name.' '.$counselor_obj->last_name."</option>");

			}
			$statement->close();
			?>
		</select></p>
	</form>
</fieldset>

<?php
//------------------------------------------Checking whether searched this page for report--------------//

if(! empty($_POST['report_generate']) && ! empty($_POST['report_from_date']) && ! empty($_POST['report_to_date']) && ! empty($_POST['sort_by']) )
{
	
	$from=htmlentities($_POST['report_from_date']);
	$to=htmlentities($_POST['report_to_date']);
	
	$order_by=' ORDER BY IR.`date` DESC'; //Default sorting
	$date_compare=' IR.date<=? AND IR.date>=?'; //Deafult date from and to
	
	if(strcasecmp($_POST['sort_by'],'CHECKUP_DATE')==0)
	{
		
		$order_by=' ORDER BY CR.date_of_checkup DESC';
		$date_compare=' CR.date_of_checkup<=? AND CR.date_of_checkup>=?'; //Deafult date from and to
	}
	else if(strcasecmp($_POST['sort_by'],'AGE')==0)
	{
		$order_by=' ORDER BY PD.`year_of_birth` DESC';
	}
	else if(strcasecmp($_POST['sort_by'],'P_NAME')==0)
	{
		$order_by=' ORDER BY PD.`first_name`';
	}
	
	//----------Patients OF-----------//
if(! empty($_POST['patients_of']))
{
	if(intval($_POST['patients_of'])<0)
	{
		$patient_of_string='';
	}
	else
	{
		$patient_of_string=' AND C.counselor_id='.intval($_POST['patients_of']);
	}
}
else
{
	$patient_of_string='';
}

	
?>
	<fieldset>
		<legend>
			Showing report: FROM :<?php printf('%s',$from);?> | TO : <?php printf('%s',$to);?>
		</legend>
	</fieldset>
	<br>
	<h5 id="total_amount"></h5>
	<h5 id="total_expenses"></h5>
	<h5 id="total_profit"></h5>
<?php
	
	//The following lines will run only if the connection to database server is successful

	$select_query='SELECT PD.`patient_id`, PD.`first_name`, PD.`last_name`,PD.`year_of_birth`,PD.`is_blacklist`,CR.`checkup_id`, CR.`date_of_checkup`,IR.`invoice_number`,IR.`amount`,IR.`date`, C.`designation`,C.`first_name`,C.`last_name`, C.`display_color`
	FROM `checkup_record` AS CR, `counselor` AS C, `patient_details` AS PD, `invoice_record` AS IR
	WHERE CR.patient_id=PD.patient_id AND CR.counselor_id=C.counselor_id AND IR.checkup_id=CR.checkup_id AND' .$date_compare.$patient_of_string. $order_by;

	@$statementInvoice=$database->prepare($select_query); //This method prepares a statementInvoice that can be used later for processing
	$statementInvoice->bind_param('ss',$to,$from);

	if( ! $statementInvoice->execute()) //The execute method will actually send the query parameters to the MySQL server.
	{
		//execute() returns false or failure therefor we checked whether it is false.
		//'!' will make false to true which will execute this if block
		printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
		exit_script();

	}
	//The following lines will execute only if the query ran successfully on the database server

	$patient_obj=new Patient();
	$checkup_record_obj=new CheckupRecord();
	$invoice_record_obj=new InvoiceRecord();
	$counselor_obj=new Counselor();
	$statementInvoice->bind_result(
		$patient_obj->patient_id,
		$patient_obj->first_name,
		$patient_obj->last_name,
		$patient_obj->year_of_birth,
		$patient_obj->is_blacklist,
		$checkup_record_obj->checkup_id,
		$checkup_record_obj->date_of_checkup,
		$invoice_record_obj->invoice_number,
		$invoice_record_obj->amount,
		$invoice_record_obj->date,
		$counselor_obj->designation,
		$counselor_obj->first_name,
		$counselor_obj->last_name,
		$counselor_obj->display_color);

	$statementInvoice->store_result(); //Stores all the fetched rows in the statementInvoice object
	
	//---------------------Getting expense Records-------------------------//
	
	$between_dates=' CE.date<=? AND CE.date>=?';
	$sort_by= ' ORDER BY CE.`date` DESC';
	
	$select_query='SELECT `expense_id`, `expense_name`, `price`, `quantity`, `date`, `remark` FROM `'.CLINIC_EXPENSE_TABLE.'` AS CE WHERE'.$between_dates.
	$sort_by ;

	@$statementExpense=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

	if(!empty($between_dates))
	{
		$statementExpense->bind_param('ss',$to,$from); //Between dates query could be null therefore we will ignore bind param
	}

	if( ! $statementExpense->execute()) //The execute method will actually send the query parameters to the MySQL server.
	{
		//execute() returns false or failure therefor we checked whether it is false.
		//'!' will make false to true which will execute this if block
		printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
		exit_script();

	}

//The following lines will execute only if the query ran successfully on the database server
$clinic_expense_obj=new ClinicExpense();
	
$statementExpense->bind_result($clinic_expense_obj->expense_id,
		   $clinic_expense_obj->expense_name,
		   $clinic_expense_obj->price,
		   $clinic_expense_obj->quantity,
		   $clinic_expense_obj->date,
		   $clinic_expense_obj->remarks);

$statementExpense->store_result(); //Stores all the fetched rows in the statement object
	
if($statementInvoice->num_rows==0 && $statementExpense->num_rows==0) //NUM_ROWS variable of statementInvoice class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
	printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p></blockquote>");
	exit_script();
}

	//------Following lines will execute only if there is invoice or expense records in the database-----//

	
$GLOBALS['total_earnings']=0;
$GLOBALS['total_expenses']=0;
function open_ExpenseData()
{

?>
	
	<table>
	<tr>
		<td colspan="8" style='color:#D44547'>
			<center>EXPENSE</center>
		</td>
	</tr>
	<tr>
<TH>EXPENSE NAME</TH>
<TH>EXPENSE ID</TH>
<TH>PRICE</TH>
<TH>QUANTITY</TH>
<TH>DATE</TH>
<TH>REMARKS</TH>

</tr>

<?php
	
}
	
function close_ExpenseData()
{
		printf("%s","</table><br><hr> <br>");
}
function print_ExpenseData($clinic_expense_obj)
{

	printf("%s","<tr>");
	 printf("%s","<td>".$clinic_expense_obj->expense_name."</td>");
	 printf("%s","<td><a href='update_clinic_expense.php?ce_id=".$clinic_expense_obj->expense_id."'>".$clinic_expense_obj->expense_id."</a></td>");
	 printf("%s","<td>".$clinic_expense_obj->price."</td>");
	 printf("%s","<td>".$clinic_expense_obj->quantity."</td>");
	 printf("%s","<td>".$clinic_expense_obj->date."</td>");
	 printf("%s","<td>".$clinic_expense_obj->remarks."</td>");
	
	 printf("%s","<td><a href='update_clinic_expense.php?ce_id=".$clinic_expense_obj->expense_id."'>EDIT</a></td>");
	 printf("%s","</tr>");
	$GLOBALS['total_expenses']+=$clinic_expense_obj->price; //Adding up all the expenses
	
}

function open_invoiceData()
{
	
?>
	
	<table>
	
	<tr>
		<td colspan="9" style='color:#24C83B'>
			<center>EARNINGS</center>
		</td>
	</tr>
	<tr>

	<TH>PATIENT ID</TH>
	<TH>PATIENT NAME</TH>
	<TH>AGE</TH>
	<TH>CHECKUP ID</TH>
	<TH>CONSULTANT</TH>
	<TH>CHECKED ON</TH>
	<TH>INVOICE NUMBER</TH>
	<TH>INVOICE DATE</TH>
	<TH>AMOUNT</TH>
	</tr>

<?php
}
	
function close_invoiceData()
{
		printf("%s","</table><br><hr><br>");

}
	
function print_invoiceData($invoice_record_obj,$patient_obj,$checkup_record_obj,$counselor_obj)
{

	
	$total_patients=0;
	$total_amount=0;
	printf("%s","<tr>");
		printf("%s","<td><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>".$patient_obj->patient_id."</a></td>");
		
		printf("%s","<td ");
		if(strcasecmp($patient_obj->is_blacklist,"Y")==0)
		{
			printf("%s", "style=\"color:".PATIENT_IS_NOT_BLACKLIST_COLOR.";background-color:".PATIENT_IS_BLACKLIST_COLOR."\"");
		}
		printf("%s","><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>");
		 printf("%s",$patient_obj->first_name." ".$patient_obj->last_name."</a></td>");
		
		 printf("%s","<td>".(intval(date('Y'))-intval($patient_obj->year_of_birth))."</td>");
		 printf("%s","<td><a href='get_checkup_sessions.php?cr_id=".$checkup_record_obj->checkup_id."'>".$checkup_record_obj->checkup_id."</a></td>");

 		printf("%s","<td style='background-color:".$counselor_obj->display_color."'>".$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");
	
		 printf("%s","<td>".$checkup_record_obj->date_of_checkup."</td>");
		 printf("%s","<td><a href='get_checkup_invoice.php?cr_id=".$checkup_record_obj->checkup_id."&in_id=".$invoice_record_obj->invoice_number."'>".$invoice_record_obj->invoice_number."</a></td>");
		 printf("%s","<td>".$invoice_record_obj->date."</td>");
		 printf("%s","<td>Rs. ".$invoice_record_obj->amount." /-</td>");
		printf("%s","</tr>");
		
		//-------------Calculating things------------//
		$GLOBALS['total_earnings']+=$invoice_record_obj->amount; //Amount
} //End of function printInvoiceData()

	
	
$isInvoicePresent=true;	 //To check whether there are any invoice items left in the result object
$isExpensePresent=true;	//To check whether there are any expense items left in the result object
	
$isExpenseOpen=false; //To list repetative expense items in a single table
$isInvoiceOpen=false;//To list repetative invoice items in a single table
	
if($statementInvoice->fetch())
{
	$isInvoicePresent=true;
}
else
{
	$isInvoicePresent=false;
}

if($statementExpense->fetch())
{
	$isExpensePresent=true;
}
else
{
	$isExpensePresent=false;
}
while(true)
{
	
	if($isInvoicePresent && $isExpensePresent)
	{
		if(strtotime($invoice_record_obj->date)>=strtotime($clinic_expense_obj->date))
		{
			//Invoice date is greater i.e ahead of expense
			
			if($isExpenseOpen) //Closing and opening new tables to display records
			{
				close_ExpenseData();
				$isExpenseOpen=false;
			}
			if(! $isInvoiceOpen)
			{
				open_InvoiceData();
				$isInvoiceOpen=true;
			}
			print_invoiceData($invoice_record_obj,$patient_obj,$checkup_record_obj,$counselor_obj);
			$isInvoicePresent=$statementInvoice->fetch(); //Fetch the next result
		}
		else
		{
			if($isInvoiceOpen)
			{
				close_invoiceData();
				$isInvoiceOpen=false;
			}
			if(! $isExpenseOpen)
			{
				open_ExpenseData();
				$isExpenseOpen=true;
			}
			
			print_ExpenseData($clinic_expense_obj);
			$isExpensePresent=$statementExpense->fetch(); //Fetch the next result
		}
	}
	elseif($isExpensePresent)
	{
			if($isInvoiceOpen)
			{
				close_invoiceData();
				$isInvoiceOpen=false;
			}
			if(! $isExpenseOpen)
			{
				open_ExpenseData();
				$isExpenseOpen=true;
			}
		print_ExpenseData($clinic_expense_obj);
		$isExpensePresent=$statementExpense->fetch(); //Fetch the next result
	}
	elseif($isInvoicePresent)
	{
		if($isExpenseOpen)
			{
				close_ExpenseData();
				$isExpenseOpen=false;
			}
			if(! $isInvoiceOpen)
			{
				open_InvoiceData();
				$isInvoiceOpen=true;
			}
		
		print_invoiceData($invoice_record_obj,$patient_obj,$checkup_record_obj,$counselor_obj);
		$isInvoicePresent=$statementInvoice->fetch(); //Fetch the next result
	}
	else
	{
		break; //Exit the While() loop
	}
	
}

if($isExpenseOpen) //Closing and opening new tables to display records
{
	close_ExpenseData();
	$isExpenseOpen=false;
}
if($isInvoiceOpen)
{
	close_invoiceData();
	$isInvoiceOpen=false;
}
	
	
	
}//Ending of the if statementInvoice to check whether this page is called after form submission
?>

<script>
	document.getElementById("total_amount").innerHTML="<?php printf('%s','Total Earnings= RS. '.$GLOBALS['total_earnings'].'/-'); ?>";
	document.getElementById("total_expenses").innerHTML="<?php printf('%s','Total Expenses= RS. '.$GLOBALS['total_expenses'].'/-'); ?>";
	document.getElementById("total_profit").innerHTML="<?php printf('%s','Total Profit= RS. '.(floatval( $GLOBALS['total_earnings'])-floatval($GLOBALS['total_expenses'])).'/-'); ?>";
</script>
</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
	
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>