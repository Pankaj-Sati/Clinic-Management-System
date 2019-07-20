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

<h3>Update Patient Details</h3>
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
if(empty($_REQUEST['p_id']))
{
	 printf("%s","<blockquote><p>".NO_PATIENT_ID_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
$passed_patient_id=intval($_REQUEST['p_id']);

//Following lines will execute only if the session number is received

@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}
?>

<h5><?php printf("%s",'Patient ID = '.$passed_patient_id); ?></h5>

<?php	
//------------------------------Checking whether the page was called after passing values---------------//
	
if(! empty($_POST['patient_name']) && ! empty($_POST['patient_year_of_birth']) && ! empty($_POST['patient_gender']) && ! empty($_POST['patient_mobile_number']))
{
	$patient_update_obj=new Patient();
	$name=explode(' ',trim($_POST['patient_name']));
	if(count($name)==1)
	{
		$patient_update_obj->first_name=htmlentities($name[0]);
		$patient_update_obj->last_name=' '; //Cannot be blank in database
	}
	else if(count($name)>1)
	{
		for($i=0;$i<count($name)-1;$i++)
		{
			if($i==0)
			{
				$patient_update_obj->first_name=htmlentities($name[$i]);	
			}
			else
			{
				$patient_update_obj->first_name=$patient_update_obj->first_name.' '.htmlentities($name[$i]);
			}
			
		}
		$patient_update_obj->last_name=htmlentities($name[count($name)-1]); //Cannot be blank in database
	}
	
	$patient_update_obj->year_of_birth=intval($_POST['patient_year_of_birth']);
	if($patient_update_obj->year_of_birth>0 && $patient_update_obj->year_of_birth<=200) //0<Age<200 else it is already year of birth
	{
		$patient_update_obj->year_of_birth=intval(date('Y'))-$patient_update_obj->year_of_birth; //Current Year- age (2018-25)
	}

	if($_REQUEST['patient_gender']=='M')
	{
		$patient_update_obj->gender='MALE';
	}
	else if($_REQUEST['patient_gender']=='F')
	{
		$patient_update_obj->gender='FEMALE';
	}
	$patient_update_obj->mobile_number=htmlentities($_POST['patient_mobile_number']);
	
	
	if(! empty($_POST['patient_weight']))
	{
		$patient_update_obj->weight=intval($_POST['patient_weight']);
	
	}
	if(! empty($_POST['patient_height']))
	{
		$patient_update_obj->height=intval($_POST['patient_height']);
	
	}
	if(! empty($_POST['patient_city']))
	{
		$patient_update_obj->city=htmlentities($_POST['patient_city']);
	
	}
	if(! empty($_POST['patient_locality']))
	{
		$patient_update_obj->locality=htmlentities($_POST['patient_locality']);
	
	}
	if(! empty($_REQUEST['patient_street address']))
	{
		$patient_update_obj->street_address=htmlentities($_REQUEST['patient_street address']);
	}

	if(! empty($_REQUEST['patient_occupation']))
	{
		$patient_update_obj->occupation=htmlentities($_REQUEST['patient_occupation']);
	}

	if(! empty($_REQUEST['patient_email_id']))
	{
		$patient_update_obj->email_id=htmlentities($_REQUEST['patient_email_id']);
	}

	if(! empty($_REQUEST['patient_blood_group']))
	{
		$patient_update_obj->blood_group=htmlentities($_REQUEST['patient_blood_group']);
	}
	if(! empty($_REQUEST['patient_is_blacklist']) && strcasecmp($_REQUEST['patient_is_blacklist'],"Y")==0)
	{
		$patient_update_obj->is_blacklist="Y";
	}
	else
	{
		$patient_update_obj->is_blacklist="N";
	}

	$updateQuery="UPDATE `patient_details` SET `first_name`=?,`last_name`=?,`year_of_birth`=?,`gender`=?,`weight`=?,`height`=?,`city`=?,`locality`=?,`street_address`=?,`occupation`=?,`mobile_number`=?,`email_id`=?,`blood_group`=?, `is_blacklist`=? WHERE `patient_id`=?";

	$statement=$database->prepare($updateQuery); //Prepare a prepared-statement to avoid sql injection attacks
	$statement->bind_param('ssdsddssssdsssd',
     $patient_update_obj->first_name,
	 $patient_update_obj->last_name,
	 $patient_update_obj->year_of_birth,
	 $patient_update_obj->gender,
	 $patient_update_obj->weight,
	 $patient_update_obj->height,
	 $patient_update_obj->city,
	 $patient_update_obj->locality,
	 $patient_update_obj->street_address,
	 $patient_update_obj->occupation,
	 $patient_update_obj->mobile_number,
	 $patient_update_obj->email_id,
	 $patient_update_obj->blood_group,
	 $patient_update_obj->is_blacklist,
	 $passed_patient_id); //Binding the ? to it's value; s-string 

	if(!$statement->execute()) //Actually send the data to the MySQL server
	{
		//true means that insert query was successful, false means unsuccessful
		printf('%s',"<blockquote><p>".DATABASE_UPDATE_ERROR."</p></blockquote>"); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	}
	else
	{
		//Following lines will execute only if database updation was successful
	printf('%s',"<blockquote><p>".DATABASE_UPDATE_SUCCESS."</p></blockquote>"); //printf() gives advantage of specifying 

	}
	
	
}

//The following lines will run only if the connection to database server is successful

$select_query='SELECT `patient_id`, `first_name`, `last_name`, `year_of_birth`, `gender`, `weight`, `height`, `city`, `locality`, `street_address`, `occupation`, `mobile_number`, `email_id`, `blood_group`, `is_blacklist` FROM `patient_details` WHERE `patient_id`='.$passed_patient_id;


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

	<form action="update_patient_details.php" method="post">
	<p><label for="name">Name *:</label> <input type="text" required value="<?php printf('%s',$patient_obj->first_name.' '. $patient_obj->last_name); ?>" name="patient_name"/></p>
	<p><label for="name">Year Of Birth/ Age *:</label> <input type="number" min=1 value="<?php printf('%d',$patient_obj->year_of_birth); ?>" required max=2100 placeholder="Age/Y.O.B" name="patient_year_of_birth"/></p>
	<p><label for="name">Gender *:</label> <select required name="patient_gender">
	<option <?php if(strcasecmp($patient_obj->gender,'MALE')==0){printf('%s','selected');} ?> value="M">Male</option> 
	<option <?php if(strcasecmp($patient_obj->gender,'FEMALE')==0){printf('%s','selected');} ?> value="F">Female</option> </select></p>
	<p><label for="name">Mobile Number *: </label><input type="number" min=1 value="<?php if(! empty($patient_obj->mobile_number)){ printf('%s',$patient_obj->mobile_number);} ?>" requried name="patient_mobile_number"/></p>
	<p><label for="name">Weight(kgs):</label> <input type="number" value="<?php printf('%d',$patient_obj->weight); ?>" step=0.1 max=200 name="patient_weight"/></p>
	<p><label for="name">Height(cm): </label><input type="number" value="<?php printf('%d',$patient_obj->height); ?>" step=0.1 max=300 name="patient_height"/></p>
	<p><label for="name">City: </label><input type="text" value="<?php printf('%s',$patient_obj->city); ?>"  name="patient_city"/></p>
	<p><label for="name">Locality: </label><input type="text" value="<?php printf('%s',$patient_obj->locality); ?>" name="patient_locality"/></p>
	<p><label for="name">Street Address: </label><input type="text" value="<?php printf('%s',$patient_obj->street_address); ?>" name="patient_street address"/></p>
	<p><label for="name">Occupation: </label><input type="text" value="<?php printf('%s',$patient_obj->occupation); ?>" name="patient_occupation"/></p>
	<p><label for="email">Email ID: </label><input type="email" value="<?php printf('%s',$patient_obj->email_id); ?>"  name="patient_email_id"/></p>
	<p><label for="name">Blood Group: </label><input type="text" value="<?php printf('%s',$patient_obj->blood_group); ?>" name="patient_blood_group"/></p>
	
	<p><label for="name">IS BLACKLIST: </label><select required name="patient_is_blacklist">
	<option <?php if(strcasecmp($patient_obj->is_blacklist,'N')==0){printf('%s','selected');} ?> value="N">NO</option> 
	<option <?php if(strcasecmp($patient_obj->is_blacklist,'Y')==0){printf('%s','selected');} ?> value="Y">YES</option> </select></p>
	
	
	<input type="hidden" name="p_id" value="<?php printf("%d",$patient_obj->patient_id);?>" />
	<input type="submit" class="formbutton" value="Update"/>
	<a href="patient_details.php?p_id=<?php printf("%d",$passed_patient_id)?>" class="button"> Cancel</a>
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
