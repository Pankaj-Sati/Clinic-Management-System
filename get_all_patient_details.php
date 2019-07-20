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

<h3>All Patients</h3>
<fieldset>
	<form action="get_all_patient_details.php" method="post">
		<p><label for="name">Search:</label><input type="text" required name="search_string"/> 
		
			&nbsp;<input type="radio" name="search_by" checked value="N">Name</input>
			&nbsp;<input type="radio" name="search_by" value="M">Mobile</input>
			&nbsp;<input type="radio" name="search_by" value="I">Patient ID</input>
			<input type="hidden" name="search_patient" value="searching_patient"/>
			&nbsp;<input type="submit" class="formbutton" value="Go"/>
		</p>
	</form>
</fieldset>

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
	
//-------------------------------Checking whether user has searched for any patient----------------//
if(! empty($_POST['search_patient']) && ! empty($_POST['search_string']))
{
	$search_patient_query='';
	 //If user has searched for some patient, we will add search query to our select query
	if(! empty($_POST['search_by']))
	{
		if(strcmp($_POST['search_by'],'M')==0)
		{
			//Search by mobile number
			$search_patient_query=' WHERE '.MOBILE_NUMBER.' LIKE \''.htmlentities($_POST['search_string']).'%\'';
		}
		else if(strcmp($_POST['search_by'],'N')==0)
		{
			//Search by name
			$name_array=explode(' ',htmlentities($_POST['search_string']));
			if(count($name_array)>1)
			{
				$search_patient_query=' WHERE '.FIRST_NAME.' LIKE \'%'.$name_array[0].'%\' OR '.LAST_NAME.' LIKE \'%'.$name_array[1].'%\'';
			}
			else
			{
				$search_patient_query=' WHERE '.FIRST_NAME.' LIKE \'%'.$name_array[0].'%\'';
			}
			
		}
		else
		{
			//Search by mobile number
			$search_patient_query=' WHERE '.PATIENT_ID.' LIKE \''.htmlentities($_POST['search_string']).'%\'';
		}
	}
	
}
else
{
	$search_patient_query='';
}

$select_query="SELECT `".PATIENT_ID."`,`".FIRST_NAME."`,`".LAST_NAME."`, `".YEAR_OF_BIRTH."`, `".GENDER."`, `".WEIGHT."`, `".HEIGHT."`, `".CITY."`, `".LOCALITY."`, `".STREET_ADDRESS."`, `".OCCUPATION."`, `".MOBILE_NUMBER."`, `".EMAIL_ID."`, `".BLOOD_GROUP."`, `".IS_BLACKLIST."` FROM `".PATIENT_DETAILS_TABLE."`".$search_patient_query." ORDER BY ".FIRST_NAME;
	
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
$statement->bind_result($patient_obj->patient_id,
	 $patient_obj->first_name,
	 $patient_obj->last_name,
	 $patient_obj->year_of_birth,
	 $patient_obj->gender,
	 $patient_obj->weight,
	 $patient_obj->height,
	 $patient_obj->city,
	 $patient_obj->locality,
	 $patient_obj->street_address,
	 $patient_obj->occupation,
	 $patient_obj->mobile_number,
	 $patient_obj->email_id,
	 $patient_obj->blood_group,
	 $patient_obj->is_blacklist
);

$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p></blockquote>");
	exit_script();
}

//Following lines will execute only if there is some data in the database
?>
<table>
<tr>

<TH>ID</TH>
<TH>NAME</TH>
<TH>AGE</TH>
<TH>GENDER</TH>
<TH>CITY</TH>
<TH>ADDRESS</TH>
<TH>OCCUPATION</TH>
<TH>MOBILE</TH>
<TH>EMAIL_ID</TH>

</tr>
<?php
$destination_url='patient_details.php';
while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{

	printf("%s","<tr onClick=\"window.location.href='".$destination_url."?p_id=".$patient_obj->patient_id."'\">");
	
	 printf("%s","<td><a href='".$destination_url."?p_id=".$patient_obj->patient_id."'>".$patient_obj->patient_id."</a></td>");
	
	printf("%s","<td ");
	if(strcasecmp($patient_obj->is_blacklist,"Y")==0)
	{
		printf("%s", "style=\"color:".PATIENT_IS_NOT_BLACKLIST_COLOR.";background-color:".PATIENT_IS_BLACKLIST_COLOR."\"");
	}
	printf("%s",">");
	 printf("%s",$patient_obj->first_name." ".$patient_obj->last_name."</td>");
	
	 printf("%s","<td>".(intval(date('Y'))-intval($patient_obj->year_of_birth))."</td>");
	 printf("%s","<td>".$patient_obj->gender."</td>");
	 printf("%s","<td>".$patient_obj->city."</td>");
	 printf("%s","<td>".$patient_obj->locality." ".$patient_obj->street_address."</td>");
	 printf("%s","<td>".$patient_obj->occupation."</td>");
	 printf("%s","<td>".$patient_obj->mobile_number."</td>");
	 printf("%s","<td>".$patient_obj->email_id."</td>");
	 printf("%s","<td><a href='update_patient_details.php?p_id=".$patient_obj->patient_id."'>EDIT</td>");
	printf("%s","</tr></a>");
	
}
?>
</table>
<br>

<a href="make_new_patient_details.php" class="button">Add New Patient</a>

</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>