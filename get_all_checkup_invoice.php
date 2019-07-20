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

<h3>All Invoice</h3>
<fieldset>
	<form action="get_all_checkup_invoice.php" method="post">
				
			<label for="name">Search:</label>&nbsp;<input type="text" value="<?php if(! empty($_POST['search_string'])){ printf('%s',$_POST['search_string']);}?>" placeholder="Enter ID or leave empty" name="search_string"/> 

			<label for="name"> By:</label> &nbsp;&nbsp;<input type="radio" checked name='search_by' value="search_in_no">&nbsp;Invoice Number</input>
					&nbsp;<input type="radio" name='search_by' value="search_patient_name">&nbsp;Patient Name</input>
					&nbsp;<input type="radio" name='search_by' value="search_patient_id">&nbsp;Patient ID</input>
					&nbsp;<input type="submit" class="formbutton" value="Go"/>
			
			
	</form>
</fieldset>
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
	
@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful
if(! empty($_POST['search_string']))
{
	if(! empty($_POST['search_by']) && strcasecmp($_POST['search_by'],'search_in_no')==0)
	{
		$search_string= ' AND IR.`invoice_number`='.intval($_POST['search_string']);

	}
	else if(! empty($_POST['search_by']) && strcasecmp($_POST['search_by'],'search_patient_name')==0)
	{
		$name_array=explode(' ',htmlentities($_POST['search_string'])); //seperating first name and last name 
		$search_string= ' AND PD.`first_name` LIKE \''.$name_array[0].'%\'';
		

	}
	else if(! empty($_POST['search_by']) && strcasecmp($_POST['search_by'],'search_patient_id')==0)
	{
		$search_string= ' AND PD.`patient_id`='.intval($_POST['search_by']);

	}
	else
	{
		$search_string='';
	}
}
else
{
 $search_string='';
}
	
$select_query='SELECT IR.`invoice_number`, IR.`amount`, IR.`date`, PD.`patient_id`, PD.`first_name`, PD.`last_name`, PD.`mobile_number`, PD.`is_blacklist`,CR.`checkup_id`, CT.`name`
FROM `checkup_record` AS CR, `patient_details` AS PD, `counselor` AS C, `checkup_type` AS CT, `invoice_record` AS IR
WHERE CR.patient_id=PD.patient_id AND CR.counselor_id=C.counselor_id AND CT.checkup_type_id=CR.checkup_type_id AND CR.checkup_id=IR.checkup_id'.$search_string.
' ORDER BY IR.`date` DESC';

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
$invoice_record_obj=new InvoiceRecord();
	
$statement->bind_result(
	$invoice_record_obj->invoice_number,
	$invoice_record_obj->amount,
	$invoice_record_obj->date,
	$patient_obj->patient_id,
	$patient_obj->first_name,
	$patient_obj->last_name,
	$patient_obj->mobile_number,
	$patient_obj->is_blacklist,
	$invoice_record_obj->checkup_id,
	$checkup_record_obj->checkup_type);
	
$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p></blockquote>");
	exit_script();
}
//Following lines will execute only if there is some data fetched from the database
?>
<table>
<tr>

<TH>INVOICE NO.</TH>
<TH>AMOUNT</TH>
<TH>DATE</TH>
<TH>PATIENT ID</TH>
<TH>PATIENT NAME</TH>
<TH>MOBILE</TH>
<TH>CHECKUP ID</TH>
<TH>TYPE</TH>
</tr>

<?php
$temp_value=null;
$colspan=8;
while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	printf("%s","<tr>");
	
	if($temp_value==null)
	{
		$temp_value=$invoice_record_obj->date;
		$date=getdate(strtotime($temp_value));

		printf("%s","<tr><td colspan=$colspan style='color:".LIST_SEPERATE_COLOR."'><center>".$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']."</center></td></tr>");
	}
	else if(strtotime($temp_value)!=strtotime($invoice_record_obj->date))
	{
		//If both dates are different, then add seperator, and make temp_value the new date
		$temp_value=$invoice_record_obj->date;	
		$date=getdate(strtotime($temp_value));

		printf("%s","<tr><td colspan=$colspan style='color:".LIST_SEPERATE_COLOR."'><br><hr><center>".$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']."</center></td></tr>");
	}
	
 	printf("%s","<td><a href='get_checkup_invoice.php?cr_id=".$invoice_record_obj->checkup_id."&in_id=".$invoice_record_obj->invoice_number."'>".$invoice_record_obj->invoice_number."</a></td>");
	printf("%s","<td>Rs. ".$invoice_record_obj->amount." /-</td>");
	printf("%s","<td>".$invoice_record_obj->date."</td>");
	printf("%s","<td><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>".$patient_obj->patient_id."</a></td>");
	
	printf("%s","<td ");
		if(strcasecmp($patient_obj->is_blacklist,"Y")==0)
		{
			printf("%s", "style=\"color:".PATIENT_IS_NOT_BLACKLIST_COLOR.";background-color:".PATIENT_IS_BLACKLIST_COLOR."\"");
		}
		printf("%s","><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>");
		 printf("%s",$patient_obj->first_name." ".$patient_obj->last_name."</a></td>");
	
	printf("%s","<td><a href='patient_details.php?p_id=".$patient_obj->patient_id."'>".$patient_obj->mobile_number."</a></td>");
	
	 printf("%s","<td><a href='get_checkup_sessions.php?cr_id=".$invoice_record_obj->checkup_id."'>".$invoice_record_obj->checkup_id."</a></td>");
	 
	 printf("%s","<td>".$checkup_record_obj->checkup_type."</td>");
	 
	printf("%s","</tr>");
	
}

?>

</table>
<br>

<a class="button" onclick="window.print()">Print All</a>
</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>


