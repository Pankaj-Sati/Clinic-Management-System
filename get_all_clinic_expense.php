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

<section id="content" class="column-left">
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>
<h3>All Expenses</h3>

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
?>

<fieldset>
	<form action="get_all_clinic_expense.php" method="post">
		<p><label for="name">Search:</label><input type="text" value="<?php if(! empty($_POST['search_string'])){ printf('%s',$_POST['search_string']);}?>" name="search_string" placeholder="Expense Name or ID" /></p>
		 
		<p><label for="name">Arrange by:</label>
			<input type="radio" name='sort_by' value="sort_date">&nbsp;Expense Date</input>
			&nbsp;<input type="radio" name='sort_by' value="sort_price">&nbsp;Price</input>
			&nbsp;<input type="radio" name='sort_by' value="sort_name">&nbsp;Name</input></p>
		
			From:&nbsp; <input type="date" <?php if(! empty($_POST['from_date'])) {printf("%s",'value="'.$_POST['from_date'].'"');} else {printf('%s',date('Y-m-d'));}?> name="from_date"/>
			
			&nbsp; To:&nbsp;<input type="date" <?php if(! empty($_POST['to_date'])) {printf("%s",'value="'.$_POST['to_date'].'"');} else {printf('%s',date('Y-m-d'));}?> name="to_date"/>
		
		
		<input type="submit" class="formbutton" value="Go"/>
			
	
	</form>
</fieldset>
<br>


<?php

if(! empty($_POST['search_string']))
{	
	if(intval(substr($_POST['search_string'],0,1))!=0) //intval will try to convert string into a number if it has any number in it
	{
		//If the first letter in the search string is a number not equal to 0, then we will search by patient ID
		$search_string=' CE.`expense_id`='.intval($_POST['search_string']);
		
	}
	else
	{
		$name=htmlentities($_POST['search_string']); //seperating first name and last name if given
		$search_string=' CE.`expense_name`like \''.$name."%'";
	}
	
}
else
{
 $search_string=' 1'; //Get all records
}
//----------Sort records by-----------//
if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'sort_price')==0)
{
	$sort_by= ' ORDER BY CE.`price` DESC' ;
	$seperate_result=false;
}
else if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'sort_name')==0)
{
	$sort_by= ' ORDER BY CE.`expense_name`' ;
	$seperate_result=true;
	$seperate_by_name=true;
}
else
{
	$sort_by= ' ORDER BY CE.`date` DESC' ;
	$seperate_result=true;
	$seperate_by_name=false;
}
//----------Between dates----------//
$from='';
$to='';
if(! empty($_POST['from_date']) && ! empty($_POST['to_date']) )
{
		$between_dates=' AND CE.date<=? AND CE.date>=?';
		$from=$_POST['from_date'];
		$to=$_POST['to_date'];
}
else
{
	$between_dates='';
}

$select_query='SELECT `expense_id`, `expense_name`, `price`, `quantity`, `date`, `remark` FROM `'.CLINIC_EXPENSE_TABLE.'` AS CE WHERE'.$search_string.$between_dates.
$sort_by ;

@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if(!empty($between_dates))
{
	$statement->bind_param('ss',$to,$from); //Between dates query could be null therefore we will ignore bind param
}

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
	printf("%s",'<a href="make_new_clinic_expense.php" class="button">Add new expense </a>');
	exit_script();
}

//Following lines will execute only if there is some data in the database
?>
<table>
<tr>

<TH>Serial No.</TH>
<TH>EXPENSE NAME</TH>
<TH>EXPENSE ID</TH>
<TH>PRICE</TH>
<TH>QUANTITY</TH>
<TH>DATE</TH>
<TH>REMARKS</TH>

</tr>
<?php
$destination_url="update_clinic_expense.php"; //To view a single record
$temp_value=null;
	$serial_number=0; //To show serial number 
while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	$serial_number++; //Increment serial number
	//--------Checking whether to seperated records by date-------------//
	if($seperate_result)
	{
		//If true
		if($seperate_by_name)
		{
			if($temp_value==null)
			{
				$temp_value=substr($clinic_expense_obj->expense_name,0,1);//Set the temp_first_character to first record's character
				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><center>$temp_value</center></td></tr>");
			}
			else if(strcasecmp($temp_value,substr($clinic_expense_obj->expense_name,0,1))!=0)
			{
				$temp_value=substr($clinic_expense_obj->expense_name,0,1); //Change the temp_first_character
				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><br><hr><center>$temp_value</center></td></tr>");
				$serial_number=1; //reset serial number
			}
		}
		else
		{
			if($temp_value==null)
			{
				$temp_value=$clinic_expense_obj->date;
				$date=getdate(strtotime($temp_value));

				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><center>".$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']."</center></td></tr>");
			}
			else if(strtotime($temp_value)!=strtotime($clinic_expense_obj->date))
			{
				//If both dates are different, then add seperator, and make temp_value the new date
				$temp_value=$clinic_expense_obj->date;	
				$date=getdate(strtotime($temp_value));

				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><br><hr><center>".$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']."</center></td></tr>");
				$serial_number=1; //reset serial number
			}
		}
	}
	
	
	 printf("%s","<tr>");
	 printf("%s","<td>".$serial_number."</td>");
	 printf("%s","<td>".$clinic_expense_obj->expense_name."</td>");
	 printf("%s","<td><a href='".$destination_url."?ce_id=".$clinic_expense_obj->expense_id."'>".$clinic_expense_obj->expense_id."</a></td>");
	 printf("%s","<td>".$clinic_expense_obj->price."</td>");
	 printf("%s","<td>".$clinic_expense_obj->quantity."</td>");
	 printf("%s","<td>".$clinic_expense_obj->date."</td>");
	 printf("%s","<td>".$clinic_expense_obj->remarks."</td>");
	
	 printf("%s","<td><a href='update_clinic_expense.php?ce_id=".$clinic_expense_obj->expense_id."'>EDIT</a></td>");
	 printf("%s","</tr>");
	
}
?>

</table>


<br>
<a  class="button" onclick="window.print()">Print page</a>
<a href="make_new_clinic_expense.php" class="button">Add new expense </a>
</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>