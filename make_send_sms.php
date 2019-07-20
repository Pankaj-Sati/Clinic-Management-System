<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<script type="text/javascript" src="js/print_script.js"></script>

<script language="JavaScript">
function toggle(source) 
{
	console.log('In Toggle function Source='+source.checked);
  checkboxes = document.getElementsByName('contact[]');
	console.log(checkboxes);
  if (source.checked) 
  {
         for (var i = 0; i < checkboxes.length; i++) 
		 {
			  console.log(i);
             checkboxes[i].checked = true;
            
         }
    } 
	else 
	{
         for (var i = 0; i < checkboxes.length; i++) 
		 {
            console.log(i);
            checkboxes[i].checked = false;

         }
     }
    
}
</script>

<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
</head>
<body>

<section id="body" class="width">
		
<!-- ----------------------------Including the sidebar ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar ?>

<section id="content" class="column-left" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>Send SMS</h3>
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
$search_patient_query=' WHERE '.MOBILE_NUMBER.'>1000000000';

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

<fieldset>

	<legend>Fields marked with * are compulsary</legend>

	<form action="send_sms.php" method="post">
	<p><label for="name">Message *:</label> <textarea rows="4" maxlength="500" cols="50" required name="message" placeholder="Enter message (Not more than 500 words)">
</textarea></p>
		
	<input type="checkbox" onClick="toggle(this)" /> Select All<br/>
	
	<table>
		
	
	<?php
		
		$no_of_contact_in_one_row=3;
		$i=0;
		while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
			{
				if($i%$no_of_contact_in_one_row==0)
				{
					//New Row
					printf("%s","<tr>");
				}
				printf("%s","<td><p><label for='name'>".$patient_obj->first_name." ".$patient_obj->last_name." (".$patient_obj->mobile_number.")</label> <input type='checkbox'  name='contact[]' value='".$patient_obj->mobile_number."'/></p>"."</td>");
				
				if($i%$no_of_contact_in_one_row==($no_of_contact_in_one_row-1))
				{
					//End of New Row
					printf("%s","</tr>");
				}
				$i++;
			}
		
		
	?>
	</tr>
	</table>
	
	<input type="submit" class="formbutton"/>
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